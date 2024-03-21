<?php

defined( 'ABSPATH' ) || exit;

/**
 * Shortcode articles.
 *
 * @package Expla_Test/admin
 * @author  Roman Peniaz <roman.peniaz@gmail.com>
 */
class Expla_Shortcode_Articles {

	protected $atts;

	public function __construct() {
		add_shortcode( 'expla_articles', [ $this, 'articles_shortcode' ] );
	}

	public function enqueue_styles():void {
		global $post;

		if (
			! is_a( $post, 'WP_Post' )
			|| ! has_shortcode( $post->post_content, 'expla_articles' )
		) {
			return;
		}

		wp_enqueue_style(
			'expla-shortcode-articles',
			EXPLA_TEST_MAIN_FILE_URL . 'public/css/expla-test-shortcode-articles.css',
			[],
			EXPLA_TEST_VERSION
		);
	}

	public function articles_shortcode( $atts ) {
		$this->atts = shortcode_atts(
			[
				'title' => esc_html__( 'Articles', 'expla-test' ), // title - H2 заголовок перед списком статей;
				'count' => 3, // count - кількість статей для виводу;
				'sort' => 'date', // sort - значення може бути одне з: date, title, rating;
				'ids' => '', // ids - можливість вказати id статей через кому.
			],
			$atts,
			'expla_articles'
		);

		$this->validate_atts();
		$posts = $this->get_posts();
		return $this->get_shortcode_html( $posts );
	}

	protected function validate_atts():void {
		if ( empty( $this->atts ) ) {
			throw new InvalidArgumentException( 'Shortcode attributes missing.' );
		}

		$sort_allowed_values = [ 'date', 'title', 'rating' ];
		if ( ! in_array( $this->atts['sort'], $sort_allowed_values )  ) {
			throw new InvalidArgumentException( 'Invalid shortcode value: ' . $this->atts['sort'] );
		}
	}

	protected function get_posts():array {
		$args = [
			'numberposts' => $this->atts['count'],
			'orderby'     => $this->atts['sort'],
			'order'       => 'DESC',
			'post_type'   => 'post',
			'post_status' => 'publish'
		];


		return get_posts( $args );
	}

	protected function get_shortcode_html( array $posts ):string {
		global $post;

		$result = '<div class="expla-shortcode-articles">';
		$result .= '<h2>' . esc_html( $this->atts['title'] ) . '</h2>';

		foreach ( $posts as $post ) {
			setup_postdata( $post );

			$result .= '
				<article class="shortcode-articles__article">
				</div>
			';
		}
		wp_reset_postdata();

		$result .= '</div>';

		return $result;
	}


}