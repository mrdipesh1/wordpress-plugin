<?php

/**
 * @package  MrdipeshCPSPlugin
 */

namespace Mrdipesh\CPS\Base;

class Activate
{
	public static function activate()
	{
		flush_rewrite_rules();

		$default = array();

		if (!get_option('mrdipesh_cps_plugin')) {
			update_option('mrdipesh_cps_plugin', $default);
		}

		if (!get_option('mrdipesh_cps_plugin_cpt')) {
			update_option('mrdipesh_cps_plugin_cpt', $default);
		}

		if (!get_option('mrdipesh_cps_plugin_tax')) {
			update_option('mrdipesh_cps_plugin_tax', $default);
		}
	}
}
