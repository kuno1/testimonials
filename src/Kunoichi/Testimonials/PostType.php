<?php

namespace Kunoichi\Testimonials;


use Hametuha\SingletonPattern\Singleton;

/**
 * Testimonial post type.
 *
 * @package testimonials
 * @property-read string $nonce
 * @property-read string $action
 */
abstract class PostType extends Singleton {

	protected static $is_initialized = false;

	private static $language = false;

	protected $old_post_type = '';

	protected $args = [];

	/**
	 * Register post types.
	 */
	protected function init() {
		static::$is_initialized = true;
		if ( ! self::$language ) {
			add_action( 'init', [ $this, 'i18n' ], 2 );
			self::$language = true;
		}
		add_action( 'init', [ $this, 'register_post_type' ] );
		add_action( 'add_meta_boxes', [ $this, 'register_meta_boxes' ], 10, 2 );
		add_action( 'save_post', [ $this, 'save_post' ], 10, 2 );
		add_filter( 'enter_title_here', [ $this, 'enter_title_here' ], 10, 2 );
	}

	/**
	 * Register po files.
	 */
	public function i18n() {
		$mo = dirname( dirname( dirname( __DIR__ ) ) ) . '/languages/testimonials-%s.mo';
		$mo = sprintf( $mo, get_locale() );
		if ( file_exists( $mo ) ) {
			load_textdomain( 'testimonials', $mo );
		}
	}

	/**
	 * Override enter title here.
	 *
	 * @param string $string
	 * @param \WP_Post $post
	 * @return string
	 */
	final public function enter_title_here( $string, $post ) {
		if ( static::get_post_type() === $post->post_type ) {
			return $this->enter_title( $string, $post );
		} else {
			return $string;
		}
	}

	/**
	 * Override title.
	 *
	 * @param string   $string
	 * @param \WP_Post $post
	 * @return string
	 */
	protected function enter_title( $string = '', $post = null ) {
		return $string;
	}

	/**
	 * Get post type.
	 *
	 * @return string
	 */
	public static function get_post_type() {
		$instance = static::get_instance();
		return $instance->old_post_type ?: $instance->post_type();
	}

	/**
	 * Detect if this class is initialized.
	 *
	 * @return bool
	 */
	public static function is_active() {
		return static::$is_initialized;
	}

	/**
	 * Returns post type.
	 *
	 * @return string
	 */
	abstract protected function post_type();

	/**
	 * Register post type.
	 *
	 * @return string
	 */
	abstract public function register_post_type();

	/**
	 * Setting args.
	 *
	 * @param array $args
	 * @return static
	 */
	protected function set_args( $args = [] ) {
		$this->args = $args;
		return $this;
	}

	/**
	 * Do something on argument register.
	 *
	 * @return void
	 */
	abstract protected function initialize_args();

	/**
	 * Register post type.
	 *
	 * @param array $args
	 */
	public static function register( $args = [] ) {
		static::get_instance()->set_args( $args )->initialize_args();
	}

	/**
	 * Render nonce field.
	 */
	protected function nonce_field() {
		wp_nonce_field( $this->action, $this->nonce, false );
	}

	/**
	 * Save post information.
	 *
	 * @param int      $post_id
	 * @param \WP_Post $post
	 */
	public function save_post( $post_id, $post ) {
		if ( static::get_post_type() !== $post->post_type ) {
			return;
		}
		if ( ! wp_verify_nonce( filter_input( INPUT_POST, $this->nonce ), $this->action ) ) {
			return;
		}
		$this->save( $post );
	}

	/**
	 * Save post data.
	 *
	 * @param \WP_Post $post
	 */
	abstract protected function save( $post );

	/**
	 * Entry point for meta box.
	 *
	 * @param string   $post_type
	 * @param \WP_Post $post
	 */
	public function register_meta_boxes( $post_type, $post ) {
		if ( static::get_post_type() !== $post_type ) {
			return;
		}
		$this->add_meta_boxes( $post );
	}

	/**
	 * Register meta boxes.
	 *
	 * @param \WP_Post $post
	 * @return void
	 */
	abstract protected function add_meta_boxes( \WP_Post $post );

	/**
	 * Search and find old post type.
	 *
	 * @param string $post_type
	 * @return bool
	 */
	protected function post_type_exists( $post_type ) {
		global $wpdb;
		$query = <<<SQL
			SELECT COUNT( ID ) FROM { $wpdb->posts }
			WHERE post_type = %s
SQL;
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		return (bool) $wpdb->get_var( $wpdb->prepare( $query, $post_type ) );
	}

	/**
	 * Getter
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function __get( $name ) {
		switch ( $name ) {
			case 'nonce':
				return sprintf( '_nonceof%s', static::get_post_type() );
			case 'action':
				return sprintf( 'update_%s', static::get_post_type() );
			default:
				return null;
		}
	}
}
