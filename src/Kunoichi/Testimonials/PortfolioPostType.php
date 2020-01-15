<?php

namespace Kunoichi\Testimonials;

/**
 * Portfolio
 *
 * @package testimonials
 */
class PortfolioPostType extends PostType {

	protected function post_type() {
		return 'portfolio';
	}

	public function register_post_type() {
		$args = wp_parse_args( $this->args, [
			'post_type'    => static::get_post_type(),
			'public'       => true,
			'menu_icon'    => 'dashicons-products',
			'show_in_rest' => true,
			'supports'     => [ 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ],
			'label'        => __( 'Portfolio', 'testimonials' ),
			'labels'       => [
				'singular_name'            => __( 'Project', 'testimonials' ),
				'add_new_item'             => __( 'Add New Project', 'testimonials' ),
				'edit_item'                => __( 'Edit Project', 'testimonials' ),
				'new_item'                 => __( 'New Project ', 'testimonials' ),
				'view_item'                => __( 'View Project', 'testimonials' ),
				'view_items'               => __( 'View Projects', 'testimonials' ),
				'search_items'             => __( 'Search Projects', 'testimonials' ),
				'not_found'                => __( 'No project found', 'testimonials' ),
				'not_found_in_trash'       => __( 'No project found in Trash', 'testimonials' ),
				'all_items'                => __( 'All projects', 'testimonials' ),
				'archives'                 => __( 'Portfolio', 'testimonials' ),
				'attributes'               => __( 'Project Attributes', 'testimonials' ),
				'insert_into_item'         => __( 'Insert into project', 'testimonials' ),
				'uploaded_to_this_item'    => __( 'Upload to this project', 'testimonials' ),
				'filter_items_list'        => __( 'Filter projects list', 'testimonials' ),
				'item_published'           => __( 'Project published.', 'testimonials' ),
				'item_published_privately' => __( 'Project published privately.', 'testimonials' ),
				'item_reverted_to_draft'   => __( 'Project reverted to draft.', 'testimonials' ),
				'item_scheduled'           => __( 'Project scheduled.', 'testimonials' ),
				'item_updated'             => __( 'Project updated', 'testimonials' ),
			],
		] );
		register_post_type( static::get_post_type(), $args );

		if ( apply_filters( 'testimonials_create_portfolio_categories', true ) ) {
			$args = apply_filters( 'testimonials_portfolio_category_args', [
				'label'                => __( 'Category', 'testimonials' ),
				'hierarchical'         => false,
				'show_in_rest'         => true,
				'public'               => true,
                'show_admin_column' => true,
			] );
			register_taxonomy( 'portfolio-category', [ static::get_post_type() ], $args );
		}
	}

	protected function enter_title( $string = '', $post = null ) {
		return __( 'Enter project title here.', 'testimonials' );
	}

	/**
	 * Override args.
	 *
	 * 1. If Jetpack is used, change post type.
	 */
	protected function initialize_args() {
		if ( isset( $this->args['jetpack'] ) && $this->args['jetpack'] && $this->post_type_exists( 'jetpack-portfolio' ) ) {
			// Jetpack is used. so change post type.
			$this->old_post_type = 'jetpack-portfolio';
		}
	}

	protected function add_meta_boxes( \WP_Post $post ) {
		add_meta_box( 'portfolio-information', __( 'Project Detail', 'testimonials' ), function( \WP_Post $post ) {
			$this->nonce_field();
			?>
            <table class="form-table">
                <tr>
                    <th><label for="portfolio-url"><?php esc_html_e( 'Related Link', 'testimonials' ) ?></label></th>
                    <td>
                        <input type="url" name="portfolio-url" id="portfolio"
                               value="<?php echo esc_attr( get_post_meta( $post->ID, '_portfolio_url', true ) ) ?>"
                               placeholder="<?php esc_attr_e( 'e.g. https://example.com/project', 'testimonials' ) ?>" />
                    </td>
                </tr>
            </table>
			<?php
        }, $post->post_type );
	}

	protected function save( $post ) {
	    foreach ( [ 'url' ] as $key ) {
	        update_post_meta( $post->ID, '_portfolio_' . $key, filter_input( INPUT_POST, 'portfolio-' . $key ) );
        }
	}
}
