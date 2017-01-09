<?php
/*
 * Plugin Name: Zao Sensei Media Attachments
 * Version: 1.1.0
 * Plugin URI: http://zao.is
 * Description: Enhance your lessons by attaching media files to lessons and courses in Sensei. Requires the CMB2 plugin.
 * Author: Zao
 * Author URI: http://zao.is
 * Requires at least: 3.8
 * Tested up to: 4.7
 *
 * @package WordPress
 * @author Zao
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Functions used by plugins
 */
if ( ! class_exists( 'WooThemes_Sensei_Dependencies' ) ) {
	require_once 'woo-includes/class-woothemes-sensei-dependencies.php';
}

/**
 * Sensei Detection
 */
if ( ! function_exists( 'is_sensei_active' ) ) {
  function is_sensei_active() {
    return WooThemes_Sensei_Dependencies::sensei_active_check();
  }
}

if ( ! is_sensei_active() ) {
	add_action( 'all_admin_notices', 'zao_sensei_media_attachments_requires_sensei' );
} else {
	require_once( 'classes/class-zao-sensei-media-attachments.php' );
	add_action( 'plugins_loaded', array( 'Zao_Sensei_Media_Attachments', 'get_instance' ) );
}


/**
 * Load localisation
 * @return void
 */
function zao_sensei_media_attachments_load_localisation () {
	load_plugin_textdomain( 'zao_sensei_media_attachments', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
}
add_action( 'init', 'zao_sensei_media_attachments_load_localisation', 0 );

/**
 * Load plguin textdomain
 * @return void
 */
function zao_sensei_media_attachments_load_plugin_textdomain () {
	$domain = 'zao_sensei_media_attachments';

	$locale = apply_filters( 'plugin_locale' , get_locale() , $domain );

	load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	load_plugin_textdomain( $domain, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
}
add_action( 'plugins_loaded', 'zao_sensei_media_attachments_load_plugin_textdomain' );

function zao_sensei_media_attachments_requires_sensei() {
	echo '<div id="message" class="error">' . __( 'Zao Sensei Media Attachments requires the Sensei plugin to be installed/active.', 'zao_sensei_media_attachments' ) . '<p>';
}
