<?php

/**
 * Trigger this file on Plugin uninstall
 *
 * @package  MrdipeshCPSPlugin
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
	die;
}

// Clear Database stored data
$mrdipeshcpss = get_posts(array('post_type' => 'mrdipeshcps', 'numberposts' => -1));

foreach ($mrdipeshcpss as $mrdipeshcps) {
	wp_delete_post($mrdipeshcps->ID, true);
}

// Access the database via SQL
global $wpdb;
$wpdb->query("DELETE FROM wp_posts WHERE post_type = 'mrdipeshcps'");
$wpdb->query("DELETE FROM wp_postmeta WHERE post_id NOT IN (SELECT id FROM wp_posts)");
$wpdb->query("DELETE FROM wp_term_relationships WHERE object_id NOT IN (SELECT id FROM wp_posts)");
