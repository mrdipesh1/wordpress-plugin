<?php

/**
 * @package  MrdipeshCPSPlugin
 */
/*
Plugin Name: Custom Post Scheduler
Plugin URI: https://wordpress.org/plugin
Description: A plugin that creates Custom Post Type, Taxonomies and schedule the post.
Version: 1.0.1
Author: Dipesh Adhikari
Author URI: https://dipeshadhikari.com
License: GPLv2 or later
Text Domain: custom-post-scheduler
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2005-2015 Automattic, Inc.
*/

// If this file is called firectly, abort!!!
defined('ABSPATH') or die('');

// Require once the Composer Autoload
if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
	require_once dirname(__FILE__) . '/vendor/autoload.php';
}

/**
 * The code that runs during plugin activation
 */
function activate_cps_plugin()
{
	Mrdipesh\CPS\Base\Activate::activate();
}
register_activation_hook(__FILE__, 'activate_cps_plugin');

/**
 * The code that runs during plugin deactivation
 */
function deactivate_cps_plugin()
{
	Mrdipesh\CPS\Base\Deactivate::deactivate();
}
register_deactivation_hook(__FILE__, 'deactivate_cps_plugin');

/**
 * Initialize all the core classes of the plugin
 */
if (class_exists('Mrdipesh\CPS\Init')) {
	Mrdipesh\CPS\Init::register_services();
}

function cpx_is_active()
{
	$active = true;
	global $post;
	if ($post) {
		$post_id = $post->post_status;
		$ctrl =  new Mrdipesh\CPS\Base\CustomPostStatusController();
		// if ($post->post_status == $ctrl->post_status_expired) {
		// 	$expired = false;
		// }
		if ($post_id > 0) {
			if ($ctrl->isPostExpired($post_id)) {
				$active = false;
			}
		}
	}
	return $active;
}
