<?php

namespace ExplaTest;

/**
 * Shortcode articles.
 *
 * @package ExplaTest/admin
 * @author  Roman Peniaz <roman.peniaz@gmail.com>
 */
class ShortcodeArticles
{
    protected $atts;

    public function __construct()
    {
        add_shortcode('expla_articles', [ $this, 'articlesShortcode' ]);
    }

    public function enqueueStyles(): void
    {
        global $post;

        if (
            ! is_a($post, 'WP_Post')
            || ! has_shortcode($post->post_content, 'expla_articles')
        ) {
            return;
        }

        wp_enqueue_style(
            'expla-shortcode-articles',
            plugin_dir_url(__FILE__) . '../public/css/expla-test-shortcode-articles.css',
            [],
            '1.0.0'
        );
    }

    public function articlesShortcode($atts): string
    {
        $this->atts = shortcode_atts(
            [
                'title' => esc_html__('Articles', 'expla-test'), // title - H2 заголовок перед списком статей;
                'count' => 3, // count - кількість статей для виводу;
                'sort' => 'date', // sort - значення може бути одне з: date, title, rating;
                'ids' => '', // ids - можливість вказати id статей через кому.
            ],
            $atts,
            'expla_articles'
        );

        $this->validateAtts();
        $posts = $this->getPosts();
        return $this->getShortcodeHtml($posts);
    }

    protected function validateAtts(): void
    {
        $sort_allowed_values = [ 'date', 'title', 'rating' ];
        if (! in_array($this->atts['sort'], $sort_allowed_values)) {
            throw new \InvalidArgumentException('Invalid shortcode argument `sort`: ' . $this->atts['sort']);
        }

        if ($this->atts['ids']) {
            $ids = explode(',', $this->atts['ids']);
            foreach ($ids as $id) {
                if (intval($id) <= 0) {
                    throw new \InvalidArgumentException('Invalid shortcode argument `ids`: ' . $this->atts['ids']);
                }
            }
            $this->atts['ids'] = $ids;
        }
    }

    protected function getPosts(): array
    {
        $args = [
            'numberposts' => $this->atts['count'],
            'orderby'     => $this->atts['sort'],
            'order'       => 'DESC',
            'post_type'   => 'post',
            'post_status' => 'publish'
        ];

        switch ($this->atts['sort']) {
            case 'title':
                $args['order'] = 'ASC';
                break;

            case 'rating':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = 'post_rating';
                break;
        }

        if ($this->atts['ids']) {
            $args['post__in'] = $this->atts['ids'];
        }

        return get_posts($args);
    }

    protected function getShortcodeHtml(array $posts): string
    {
        global $post;

        $result = '<div class="expla-shortcode-articles">';
        $result .= '<h2 class="shortcode-articles__title">' . esc_html($this->atts['title']) . '</h2>';

        foreach ($posts as $post) {
            setup_postdata($post);

            $rating_value = get_post_meta($post->ID, 'post_rating', true);
            $rating_html = '';
            if ($rating_value) {
                $rating_html = '
                    <div class="actions__rating" title="' . esc_html__('Rating', 'expla-test') . '">
                        ⭐ ' . esc_html($rating_value) . '
                    </div>
                ';
            }

            $external_link = get_post_meta($post->ID, 'post_external_link', true);
            $external_html = '';
            if ($external_link) {
                $external_html = '
                    <div class="actions__external-link">
                        <a
                            href="' . esc_url($external_link) . '"
                            class="external-link"
                            target="_blank"
                            rel="nofollow"
                        >
                            ' . esc_html__('Visit Site', 'expla-test') . '
                        </a>
                    </div>
                ';
            }

            $result .= '
                <article class="shortcode-articles__article">
                    <div class="article__img">
                        <img src="' . wp_get_attachment_url(get_post_thumbnail_id($post->ID)) . '" alt="">
                    </div>
                    <div class="article__content">
                        <div class="content__categories">
                            ' . get_the_category_list(', ') . '
                        </div>
                        <div class="content__title">
                            ' . esc_html($post->post_title) . '
                        </div>
                        <div class="content__actions">
                            <div class="actions__permalink">
                                <a href="' . get_permalink($post->ID) . '" class="permalink">
                                    ' . esc_html__('Read More', 'expla-test') . '
                                </a>
                            </div>
                            ' . $rating_html . '
                            ' . $external_html . '
                        </div>
                    </div>
                </article>
            ';
        }
        wp_reset_postdata();

        $result .= '</div>';

        return $result;
    }
}
