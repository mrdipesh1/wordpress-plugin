<?php

/**
 * @package  MrdipeshCPSPlugin
 */

namespace Mrdipesh\CPS\Base;

use Mrdipesh\CPS\Base\BaseController;

/**
 * 
 */
class Enqueue extends BaseController
{
	public function register()
	{
		add_action('admin_enqueue_scripts', array($this, 'mrdipesh_cps_adminEnqueue'));
		add_action('wp_enqueue_scripts', array($this, 'mrdipesh_cps_wpEnqueue'));
	}

	function mrdipesh_cps_adminEnqueue()
	{
		$plugin_data = get_file_data( __FILE__, array( 'Version' => 'Version' ), false );
		wp_enqueue_style('mrdipesh_cpsstyle', $this->plugin_url . 'assets/mrdipeshcpsstyle-admin.css',array(),$plugin_data['Version']);
		wp_enqueue_script('mrdipesh_cpsscript', $this->plugin_url . 'assets/mrdipeshcpsscript-admin.js',array(),$plugin_data['Version'],true);
	}
	function mrdipesh_cps_wpEnqueue()
	{
		$plugin_data = get_file_data( __FILE__, array( 'Version' => 'Version' ), false );
		wp_enqueue_style('mrdipesh_cpsstyle', $this->plugin_url . 'assets/mrdipeshcpsstyle.css',array(),$plugin_data['Version']);
		wp_enqueue_script('mrdipesh_cpsscript', $this->plugin_url . 'assets/mrdipeshcpsscript.js', array('jquery'), $plugin_data['Version'],true);
		wp_localize_script('mrdipesh_cpsscript', 'mrdipesh_cpsscript_vars', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'page_id' => get_the_ID(),
			'nonce' => wp_create_nonce('mrdipesh_cpsscript_overlay_expired_action')
		));
	}
}
