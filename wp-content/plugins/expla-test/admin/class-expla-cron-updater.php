<?php
/**
 * @package Expla_Test/admin
 * @author  Roman Peniaz <roman.peniaz@gmail.com>
 */
class Expla_Cron_Updater {

	public static function schedule_cron_events() {
		if ( wp_next_scheduled( 'expla/cron/sync-posts' ) ) {
			return;
		}

		wp_schedule_event( time(), 'daily', 'expla/cron/sync-posts' );
	}

	public static function delete_scheduled_events() {
		wp_clear_scheduled_hook( 'expla/cron/sync-posts' );
	}

	public function __construct() {
		add_action( 'expla/cron/sync-posts', [ $this, 'api_sync_posts' ] );
	}

	public function api_sync_posts() {
		$api_posts = $this->retrieve_api_posts(); // Статті потрібно отримувати з API Reponse
		$author_id = $this->retrieve_admin_id();

		foreach ( $api_posts as $api_post ) {
			$exist_post = get_posts( [
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'title'          => $api_post->title,
			] );

			if ( $exist_post ) {
				continue; // Якщо стаття з таким заголовком уже є базі, її не потрібно заливати;
			}

			$new_post = [
				'post_title'   => $api_post->title,
				'post_content' => $api_post->content,
				'post_status'  => 'publish',
				'post_author'  => $author_id, // Автор кожної статті - перший знайдений юзер з ролю “administrator”;
				'post_type'    => 'post',
				'post_date'    => $this->get_random_post_date(), // Дата публікації має бути випадковою: від “сьогодні” до “мінус 1 місяць”.
				'post_category' => $this->retrieve_category_id( $api_post->category ), // В response є категорія, вона має присвоюватись посту. Якщо такої немає - то створювати;
			];

			$post_id = wp_insert_post( $new_post );

			if ( is_wp_error( $post_id ) ) {
				throw new Error( 'Post creation error: ' . $post_id->get_error_message() );
			}

			$attachment_id = $this->save_api_post_image( $api_post->image, $api_post->title );
			set_post_thumbnail( $post_id, $attachment_id ); // Image потрібно заливати в Media і проставляти як featured image (post thumbnail);
		}
	}

	protected function retrieve_api_posts():array {
		$response = wp_remote_get( 'https://my.api.mockaroo.com/posts.json', [
			'headers' => [
				'X-API-Key' => '413dfbf0'
			]
		] );

		if ( is_wp_error( $response ) ) {
			throw new Exception( $response->get_error_message() );
		}

		$body = wp_remote_retrieve_body( $response );
		$posts = json_decode( $body );

		if ( $posts === null && json_last_error() !== JSON_ERROR_NONE ) {
			throw new Exception( 'JSON: ' . json_last_error_msg() );
		}

		return $posts;
	}

	protected function retrieve_admin_id():int {
		$admin = get_users( [
			'role'    => 'administrator',
			'orderby' => 'ID',
			'order'   => 'ASC',
			'number'  => 1,
		] );

		if ( empty( $admin[0] ) ) {
			throw new Exception( 'Administator is absent' );
		}

		return $admin[0]->ID;
	}

	protected function get_random_post_date():string {
		$random_timestamp = rand( strtotime( '-1 month' ), time() );
		return date( 'Y-m-d H:i:s', $random_timestamp );
	}

	protected function retrieve_category_id( string $category_name ):array {
		if ( ! function_exists( 'wp_create_category' ) )  {
			require_once ABSPATH . 'wp-admin/includes/taxonomy.php';
		}

		$category_id = wp_create_category( $category_name );

		if ( is_wp_error( $category_id ) ) {
			throw new Exception( 'Category creation error: ' . $category_id->get_error_message() );
		}

		return [ $category_id ];
	}

	protected function save_api_post_image( $img_url, $img_name ):int {
		$upload_file = wp_upload_bits(
			sanitize_title( $img_name ),
			null,
			file_get_contents( $img_url )
		);

		if ( $upload_file['error'] ) {
			throw new Exception( 'Image downloading error: ' . $upload_file['error'] );
		}

		$file_path = $upload_file['file'];
		$file_name = basename( $file_path );
		$file_type = wp_check_filetype( $file_name, null );

		$attachment = [
			'post_mime_type' => $file_type['type'],
			'post_title'     => sanitize_file_name( $file_name ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		];

		$attachment_id = wp_insert_attachment( $attachment, $file_path );

		if ( is_wp_error( $attachment_id ) ) {
			throw new Exception( 'WP Media error: ' . $attachment_id->get_error_message() );
		}
		$attachment_data = wp_generate_attachment_metadata( $attachment_id, $file_path );
		wp_update_attachment_metadata( $attachment_id, $attachment_data );

		return $attachment_id;
	}

}