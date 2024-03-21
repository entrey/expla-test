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

    public function articlesShortcode($atts)
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
        if (empty($this->atts)) {
            throw new \InvalidArgumentException('Shortcode attributes missing.');
        }

        $sort_allowed_values = [ 'date', 'title', 'rating' ];
        if (! in_array($this->atts['sort'], $sort_allowed_values)) {
            throw new \InvalidArgumentException('Invalid shortcode value: ' . $this->atts['sort']);
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

        if ($this->atts['ids']) {
            $args['post__in'] = explode(',', $this->atts['ids']);
        }

        return get_posts($args);
    }

    protected function getShortcodeHtml(array $posts): string
    {
        global $post;

        $result = '<div class="expla-shortcode-articles">';
        $result .= '<h2>' . esc_html($this->atts['title']) . '</h2>';

        foreach ($posts as $post) {
            setup_postdata($post);

            $result .= '
				<article class="shortcode-articles__article">
					<div class="article__img">
						<img src="' . wp_get_attachment_url(get_post_thumbnail_id($post->ID)) . '" />
					</div>
					<div class="article__content">
						<div class="content__categories">
							' . get_the_category_list(', ') . '
						</div>
						<div class="content__title">
							' . esc_html($post->post_title) . '
						</div>

						<p class="testimonial_desc">' . get_the_content() . '</p>

						<div class="author-details">
							<img src="' . esc_attr($post->author_image) . '" alt="image">
							<p>'
                                . esc_html($post->author_name)
                                . '<span>' . esc_html($post->author_designation) . '</span>
							</p>
						</div>
					</div>
				</div>
			';
        }
        wp_reset_postdata();

        $result .= '</div>';

        return $result;
    }
}
