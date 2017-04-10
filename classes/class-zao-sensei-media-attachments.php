<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Zao_Sensei_Media_Attachments {
	protected static $single_instance = null;

	/**
	 * Creates or returns an instance of this class.
	 * @since  0.1.0
	 * @return Zao_Sensei_Media_Attachments A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	protected function __construct() {
		add_action( 'all_admin_notices', array( $this, 'check_if_cmb2_loaded' ) );
		add_action( 'cmb2_admin_init', array( $this, 'register_media_meta_box' ) );

		// Media files display
		add_action( 'sensei_single_course_content_inside_before', array( $this, 'display_attached_media' ), 35 );
		add_action( 'sensei_lesson_single_meta', array( $this, 'display_attached_media' ), 1 );
	}

	/**
	 * Add admin notice if CMB2 is not installed/active.
	 *
	 * @since  1.1.0
	 *
	 * @return void
	 */
	public function check_if_cmb2_loaded() {
		if ( ! defined( 'CMB2_LOADED' ) ) {
			echo '<div id="message" class="error"><p>' . __( 'Zao Sensei Media Attachments requires the CMB2 plugin to be installed/active. Without it, you will not have the attached media metabox.', 'zao_sensei_media_attachments' ) . '</p></div>';
		}
	}

	/**
	 * Register CMB2 metaboxes to course and lesson edit pages
	 *
	 * @since  1.1.0
	 *
	 * @return void
	 */
	function register_media_meta_box() {

		// Add Sensei attached media metabox
		$cmb = new_cmb2_box( array(
			'id'           => 'sensei-attached-media',
			'title'        => __( 'Associated Media', 'zao_sensei_media_attachments' ),
			'object_types' => array( 'course', 'lesson' ),
		) );

		$cmb->add_field( array(
			'id'   => '_attached_media',
			'type' => 'file_list',
		) );
	}

	/**
	 * Display attached media files on single lesson & course pages.
	 *
	 * @since  1.0.0
	 *
	 * @param  int $post_id The post id to get the media for. Will default to get_the_ID().
	 *
	 * @return void
	 */
	public function display_attached_media( $post_id = 0 ) {
		echo $this->get_attached_media( $post_id );
	}

	/**
	 * Get attached media files on single lesson & course pages.
	 *
	 * @since  1.1.0
	 *
	 * @param  int $post_id The post id to get the media for. Will default to get_the_ID().
	 *
	 * @return void
	 */
	public function get_attached_media( $post_id = 0 ) {
		$post_id = $post_id ? $post_id : get_the_ID();
		$media = get_post_meta( $post_id, '_attached_media', true );
		$should_show = apply_filters( 'zao_sensei_media_attachments_should_show', is_user_logged_in() );

		if ( ! $should_show || empty( $media ) || ! is_array( $media ) ) {
			return '';
		}

		$html = '';
		$html .= '<div id="zao-sensei-media-attachments" class="course-section zao-sensei-media-attachments">';
			$html .= '<header><h2>' . __( 'Course Media', 'zao_sensei_media_attachments' ) . '</h2></header>';
			$html .= '<ul class="media-list">';
				$index = 0;
				foreach ( $media as $attach_id => $file ) {
					$index++;

					$file_name = get_the_title( $attach_id );

					// If $index === $attach_id, then this is using the old style metabox values (no attach ids).
					if ( empty( $file_name ) || $index === $attach_id  ) {
						$file_parts = explode( '/', $file );
						$file_name = array_pop( $file_parts );
					}
					$html .= '<li id="attached_media_' . $attach_id . '"><a href="' . esc_url( $file ) . '" target="_blank"><i class="fa fa-download" aria-hidden="true"></i>' . esc_html( $file_name ) . '</a></li>';
				}
			$html .= '</ul>';
		$html .= '</div><!-- #zao-sensei-media-attachments -->';

		return apply_filters( 'zao_sensei_media_attachments', $html, $post_id );
	}

}
Zao_Sensei_Media_Attachments::get_instance();
