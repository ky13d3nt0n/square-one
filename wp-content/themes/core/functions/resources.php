<?php
/**
 * Functions: Front-end Resources (CSS & JS)
 *
 * Functions for handling scripts, styles, and any other needed resources
 *
 * @since core 1.0
 */


// Login Resources
add_action( 'login_enqueue_scripts', 'login_styles' );

// Site Resources
add_action( 'wp_enqueue_scripts', 'enqueue_styles' );
add_action( 'wp_enqueue_scripts', 'enqueue_scripts' );

// Legacy Check
add_action( 'wp_head', 'old_browsers', 0, 0 );

// Site Fonts
add_action( 'wp_head', 'core_fonts', 0, 0 );
add_action( 'login_head', 'core_fonts', 0, 0 );

// Remove WP Emoji Scripts
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

// Remove WP SEO json-ld output
add_filter( 'wpseo_json_ld_output', '__return_false' );


/**
 * Add a stylesheet to the login page
 */

function login_styles() {

	$css_dir = trailingslashit( get_template_directory_uri() ) . 'css/admin/';
	$version = tribe_get_version();

	// CSS
	$css_login = 'login.css';

	// Production
	if ( ! defined( 'SCRIPT_DEBUG' ) || SCRIPT_DEBUG === false ) {
		$css_login = 'dist/login.min.css';
	}

	wp_enqueue_style( 'core-theme-login', $css_dir . $css_login, $version );

}


/**
 * Enqueue styles
 */

function enqueue_styles() {

	$css_dir = trailingslashit( get_template_directory_uri() ) . 'css/';
	$version = tribe_get_version();

	// CSS
	$css_global = 'master.css';
	$css_print  = 'print.css';

	// Production
	if ( ! defined( 'SCRIPT_DEBUG' ) || SCRIPT_DEBUG === false ) {
		$css_global = 'dist/master.min.css';
		$css_print  = 'dist/print.min.css';
	}

	// CSS: base
	wp_enqueue_style( 'core-theme-base', $css_dir . $css_global, array(), $version, 'all' );

	// CSS: print
	wp_enqueue_style( 'core-theme-print', $css_dir . $css_print, array(), $version, 'print' );

}


/**
 * Enqueue scripts
 */

function enqueue_scripts() {

	$js_dir  = trailingslashit( get_template_directory_uri() ) . 'js/';
	$version = tribe_get_version();

	// Custom jQuery
	// We version 2 due to browser support & can save large amounts of weight
	wp_deregister_script( 'jquery' );

	// JS
	$scripts     = 'dist/scripts.js';
	$jquery      = 'vendor/jquery.js';
	$script_deps = array( 'jquery', 'babel-polyfill' );

	// Production
	if ( ! defined( 'SCRIPT_DEBUG' ) || SCRIPT_DEBUG === false ) {
		$jquery      = 'vendor/jquery.min.js';
		$scripts     = 'dist/master.min.js';
		$script_deps = array( 'jquery' );
	} else {
		wp_enqueue_script( 'babel-polyfill', $js_dir . 'vendor/polyfill.js', [], $version, true );
		wp_enqueue_script( 'core-globals', $js_dir . 'vendor/globals.js', ['babel-polyfill'], $version, true );
	}

	wp_register_script( 'jquery', $js_dir . $jquery, array(), $version, false );

	wp_enqueue_script( 'core-theme-scripts', $js_dir . $scripts, $script_deps, $version, true );

	wp_localize_script( 'core-theme-scripts', 'modern_tribe_i18n', core_js_i18n() );
	wp_localize_script( 'core-theme-scripts', 'modern_tribe_config', core_js_config() );

	wp_enqueue_script( 'core-theme-scripts' );

	// Accessibility Testing
	if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true ) {
		wp_enqueue_script( 'core-theme-totally', $js_dir . 'vendor/tota11y.min.js', array( 'core-theme-scripts' ), $version, true );
	}

	// JS: Comments
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

}


/**
 * Redirect old browsers to a unique message page (IE9 and below)
 */

function old_browsers() {

?>

	<script type="text/javascript">
		function is_browser() {
			return (
				navigator.userAgent.indexOf("Chrome") !== -1 ||
				navigator.userAgent.indexOf("Opera") !== -1 ||
				navigator.userAgent.indexOf("Firefox") !== -1 ||
				navigator.userAgent.indexOf("MSIE") !== -1 ||
				navigator.userAgent.indexOf("Safari") !== -1 ||
				navigator.userAgent.indexOf("Edge") !== -1
			);
		}
		function not_excluded_page() {
			return (
				window.location.href.indexOf("/unsupported-browser/") === -1 &&
				document.title.toLowerCase().indexOf('page not found') === -1
			);
		}
		if (is_browser() && !window.atob && not_excluded_page()) {
			window.location = location.protocol + '//' + location.host + '/unsupported-browser/';
		}
	</script>

<?php

}


/**
 * Add any required fonts
 */

function core_fonts() {


}


/**
 * Provides config data to be used by front-end JS
 *
 * @return array
 */

function core_js_config() {

	static $data = array();
	if ( empty( $data ) ) {
		$data = array(
			'images_url'   => trailingslashit( get_template_directory_uri() ) . 'img/',
			'template_url' => trailingslashit( get_template_directory_uri() )
		);
		$data = apply_filters( 'core_js_config', $data );
	}

	return $data;

}