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
// $mrdipeshcpss = get_posts(array('post_type' => 'mrdipeshcps', 'numberposts' => -1));

// foreach ($mrdipeshcpss as $mrdipeshcps) {
// 	wp_delete_post($mrdipeshcps->ID, true);
// }

// // Access the database via SQL
// global $wpdb;
// $wpdb->query("DELETE FROM wp_posts WHERE post_type = 'mrdipeshcps'");
// $wpdb->query("DELETE FROM wp_postmeta WHERE post_id NOT IN (SELECT id FROM wp_posts)");
// $wpdb->query("DELETE FROM wp_term_relationships WHERE object_id NOT IN (SELECT id FROM wp_posts)");

 
// /**
//  * Fired when the plugin is uninstalled.
//  *
//  * @package Custom Post Scheduler
//  */

// if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
//     exit; // Exit if accessed directly
// }

// Example: Delete plugin options
delete_option( 'mrdipesh_cps_plugin' );

// Example: Delete site options in multisite
delete_site_option( 'mrdipesh_cps_plugin' );

// Example: Delete custom post meta
// global $wpdb;
// $wpdb->query(
//     $wpdb->prepare(
//         "DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s",
//         'custom_scheduler_meta'
//     )
// );

// // Example: Delete custom posts (if needed)
// $custom_posts = get_posts( array(
//     'post_type'      => 'custom_schedule',
//     'posts_per_page' => -1,
//     'fields'         => 'ids',
// ) );

// foreach ( $custom_posts as $post_id ) {
//     wp_delete_post( $post_id, true ); // true = force delete, not trash
// }
