<?php
/**
 * PHPUnit bootstrap file
 *
 * @package memcache-object-cache
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

$_core_dir = getenv( 'WP_CORE_DIR' );
if ( ! $_core_dir ) {
	$_core_dir = '/tmp/wordpress';
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

// Manually copy the object-cache.php to wp-content
copy( dirname( __FILE__ ) . '/../src/object-cache.php', $_core_dir . '/wp-content/object-cache.php' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
require $_tests_dir . '/tests/cache.php';
require 'base.php';
