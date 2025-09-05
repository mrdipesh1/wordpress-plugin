<?php

/**
 * @package  MrdipeshCPSPlugin
 */

namespace Mrdipesh\CPS\Base;

class BaseController
{
	public $plugin_path;

	public $plugin_url;

	public $plugin;
	public $settings;

	public $managers = array();

	public function __construct()
	{
		$this->plugin_path = plugin_dir_path(dirname(__FILE__, 2));
		$this->plugin_url = plugin_dir_url(dirname(__FILE__, 2));
		$this->plugin = plugin_basename(dirname(__FILE__, 3)) . '/custom-post-scheduler.php';

		$this->managers = array(
			'cpt_manager' => 'Activate CPT Manager',
			'cps_manager' => 'Activate Schedule Manager',
			'taxonomy_manager' => 'Activate Taxonomy Manager'
		);
	}

	public function activated(string $key)
	{
		$option = get_option('mrdipesh_cps_plugin');

		return isset($option[$key]) ? $option[$key] : false;
	}
	public function mrdipesh_cps_getTimestamp()
	{
		return strtotime(date_i18n('Y-m-d H:i:s'));
	}
}
