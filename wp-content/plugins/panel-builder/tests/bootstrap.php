<?php

$GLOBALS['wp_tests_options'] = array(
	'active_plugins' => array(
		basename(dirname(dirname(__FILE__))).'/tribe-panel-builder.php',
	),
	'template' => 'twentythirteen',
	'stylesheet' => 'twentythirteen',
	'permalink_structure' => '%postname%/',
);

// Check for select constants defined as environment variables
foreach ( array('ABSPATH', 'WP_CONTENT_DIR', 'WP_CONTENT_URL', 'WP_PLUGIN_DIR', 'WP_PLUGIN_URL', 'WPMU_PLUGIN_DIR') as $env_constant ) {
	if ( false !== getenv( $env_constant ) && !defined( $env_constant ) ) {
		define( $env_constant, getenv( $env_constant ));
	}
}

global $wp_version; // wp's test suite doesn't globalize this, but we depend on it for loading core

// If the wordpress-tests repo location has been customized (and specified
// with WP_TESTS_DIR), use that location. This will most commonly be the case
// when configured for use with Travis CI.

// Otherwise, we'll just assume that this plugin is installed in the WordPress
// SVN external checkout configured in the wordpress-tests repo.

if( false !== getenv( 'WP_TESTS_DIR' ) ) {
	require getenv( 'WP_TESTS_DIR' ) . '/includes/bootstrap.php';
} else {
	require dirname( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) ) . '/tests/phpunit/includes/bootstrap.php';
}

error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

include_once('UnitTestCase.php');
