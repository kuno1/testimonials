<?php

namespace Kunoichi\Testimonials;


/**
 * Testimonial
 *
 * @package testimonials
 */
class TestimonialPostType extends PostType {

	protected function post_type() {
		return 'testimonials';
	}

	public function register_post_type() {
		$args = wp_parse_args( $this->args, [
			'post_type'    => static::get_post_type(),
			'public'       => false,
			'show_ui'      => true,
			'menu_icon'    => 'dashicons-awards',
			'show_in_rest' => true,
			'supports'     => [ 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ],
            'label'        => __( 'Testimonials', 'testimonials' ),
            'labels'       => [
                'singular' => __( 'Testimonial', 'initialized' )
            ],
		] );
		register_post_type( static::get_post_type(), $args );
	}

	protected function enter_title( $string = '', $post ) {
		return __( 'Enter the name of the nominator.', 'testimonials' );
	}


	protected function add_meta_boxes( \WP_Post $post ) {
		add_meta_box( 'testimonial-source', __( 'Source of testimonial', 'testimonials' ), function( \WP_Post $post ) {
			$this->nonce_field();
			?>
			<table class="form-table">
				<tr>
					<th><label for="testimonial-position"><?php esc_html_e( 'Position', 'testimonials' ) ?></label></th>
                    <td><input type="text" class="regular-text" id="testimonial-position" name="testimonial-position"
                               value="<?php echo esc_attr( get_post_meta( $post->ID, '_testimonial_position', true ) ) ?>"
                               placeholder="<?php esc_attr_e( 'e.g. CEO of Super Company', 'testimonials' ) ?>" /></td>
				</tr>
                <tr>
                    <th><label for="testimonial-source"><?php esc_html_e( 'Source', 'testimonials' ) ?></label></th>
                    <td><input type="text" class="regular-text" id="testimonial-source" name="testimonial-source"
                               value="<?php echo esc_attr( get_post_meta( $post->ID, '_testimonial_source', true ) ) ?>"
                               placeholder="<?php esc_attr_e( 'e.g. Great Magazine Vol.999', 'testimonials' ) ?>" /></td>
                </tr>
                <tr>
                    <th><label for="testimonial-url"><?php esc_html_e( 'URL', 'testimonials' ) ?></label></th>
                    <td><input type="url" class="regular-text" id="testimonial-url" name="testimonial-url"
                               value="<?php echo esc_attr( get_post_meta( $post->ID, '_testimonial_url', true ) ) ?>"
                               placeholder="<?php esc_attr_e( 'e.g. https://example.com', 'testimonials' ) ?>" /></td>
                </tr>
			</table>
			<?php
		}, $post->post_type );
	}

	/**
	 * Override args.
	 *
	 * 1. If Jetpack is used, change post type.
	 */
	protected function initialize_args() {
		if ( isset( $this->args['jetpack'] ) && $this->args['jetpack'] ) {
			// Jetpack is used. so change post type.
			$this->old_post_type = 'jetpack-testimonial';
		}
	}

	protected function save( $post ) {
	    foreach ( [ 'position', 'source', 'url' ] as $key ) {
	        update_post_meta( $post->ID, '_testimonial_' . $key, filter_input( INPUT_POST, 'testimonial-' . $key ) );
        }
	}


}
