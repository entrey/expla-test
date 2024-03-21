<?php

namespace ExplaTest;

/**
 * Cron updater manager.
 *
 * @package ExplaTest/admin
 * @author  Roman Peniaz <roman.peniaz@gmail.com>
 */
class CronUpdater
{
    public static function scheduleCronEvents()
    {
        if (wp_next_scheduled('expla/cron/sync-posts')) {
            return;
        }

        wp_schedule_event(time(), 'daily', 'expla/cron/sync-posts');
    }

    public static function deleteScheduledEvents()
    {
        wp_clear_scheduled_hook('expla/cron/sync-posts');
    }

    public function __construct()
    {
        add_action('expla/cron/sync-posts', [ $this, 'syncPostsWithApi' ]);
    }

    public function syncPostsWithApi(): void
    {
        $posts = $this->retrieveApiPosts();
        $this->saveApiPosts($posts);
    }

    protected function retrieveApiPosts(): array
    {
        $response = wp_remote_get('https://my.api.mockaroo.com/posts.json', [
            'headers' => [
                'X-API-Key' => '413dfbf0'
            ]
        ]);

        if (is_wp_error($response)) {
            throw new \Exception($response->get_error_message());
        }

        $body = wp_remote_retrieve_body($response);
        $posts = json_decode($body);

        if ($posts === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('JSON: ' . json_last_error_msg());
        }

        return $posts;
    }

    protected function saveApiPosts(array $api_posts)
    {
        $author_id = $this->retrieveAdminId();

        foreach ($api_posts as $api_post) {
            $exist_post = get_posts([
                'post_type'      => 'post',
                'post_status'    => 'publish',
                'posts_per_page' => 1,
                'title'          => $api_post->title,
            ]);

            if ($exist_post) {
                continue;
            }

            $new_post = [
                'post_title'   => $api_post->title,
                'post_content' => $api_post->content,
                'post_status'  => 'publish',
                'post_author'  => $author_id,
                'post_type'    => 'post',
                'post_date'    => $this->getRandomPostDate(),
                'post_category' => $this->retrieveCategoryId($api_post->category),
            ];

            $post_id = wp_insert_post($new_post);

            if (is_wp_error($post_id)) {
                throw new \Error('Post creation error: ' . $post_id->get_error_message());
            }

            $attachment_id = $this->saveApiPostImage($api_post->image, $api_post->title);
            set_post_thumbnail($post_id, $attachment_id);
        }
    }

    protected function retrieveAdminId(): int
    {
        $admin = get_users([
            'role'    => 'administrator',
            'orderby' => 'ID',
            'order'   => 'ASC',
            'number'  => 1,
        ]);

        if (empty($admin[0])) {
            throw new \Exception('Administator is absent');
        }

        return $admin[0]->ID;
    }

    protected function getRandomPostDate(): string
    {
        $random_timestamp = rand(strtotime('-1 month'), time());
        return date('Y-m-d H:i:s', $random_timestamp);
    }

    protected function retrieveCategoryId(string $category_name): array
    {
        if (! function_exists('wp_create_category')) {
            require_once ABSPATH . 'wp-admin/includes/taxonomy.php';
        }

        $category_id = wp_create_category($category_name);

        if (is_wp_error($category_id)) {
            throw new \Exception('Category creation error: ' . $category_id->get_error_message());
        }

        return [ $category_id ];
    }

    protected function saveApiPostImage(string $img_url): int
    {
        preg_match_all('/([^\/?#]+)\.\w+/', $img_url, $matches);
        $file_name = $matches[0][1] ?? null;

        if (! $file_name) {
            throw new \Exception('Cannot derrive image name from URL: ' . $img_url);
        }

        $upload_file = wp_upload_bits(
            $file_name,
            null,
            file_get_contents($img_url)
        );

        if ($upload_file['error']) {
            throw new \Exception('Image downloading error: ' . $upload_file['error']);
        }

        $file_path = $upload_file['file'];
        $file_name = basename($file_path);
        $file_type = wp_check_filetype($file_name, null);

        $attachment = [
            'post_mime_type' => $file_type['type'],
            'post_title'     => sanitize_file_name($file_name),
            'post_content'   => '',
            'post_status'    => 'inherit',
        ];

        $attachment_id = wp_insert_attachment($attachment, $file_path);

        if (is_wp_error($attachment_id)) {
            throw new \Exception('WP Media error: ' . $attachment_id->get_error_message());
        }

        if (! function_exists('wp_generate_attachment_metadata')) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }

        $attachment_data = wp_generate_attachment_metadata($attachment_id, $file_path);
        wp_update_attachment_metadata($attachment_id, $attachment_data);

        return $attachment_id;
    }
}
