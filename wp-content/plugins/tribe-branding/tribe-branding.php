<?php
/*
Plugin Name: Tribe Branding
Description: Update links, logos, and social icons
Author: Modern Tribe, Inc.
Author URI: http://tri.be
Version: 1.0

@todo Default login logo and favicon
*/

/**
 * Load all the plugin files and initialize appropriately
 *
 * @return void
 */
if ( !function_exists('tribe_branding_load') ) { // play nice
	function tribe_branding_load() {
		require_once( 'Tribe_Branding.php' );
		require_once( 'branding-template-tags.php' );
		add_action( 'plugins_loaded', array( 'Tribe_Branding', 'init' ) );
	}

	tribe_branding_load();
}
