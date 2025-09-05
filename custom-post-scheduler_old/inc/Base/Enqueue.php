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
		add_action('admin_enqueue_scripts', array($this, 'adminEnqueue'));
		add_action('wp_enqueue_scripts', array($this, 'wpEnqueue'));
	}

	function adminEnqueue()
	{
		$plugin_data = get_file_data( __FILE__, array( 'Version' => 'Version' ), false );
		wp_enqueue_style('mrdipeshcpsstyle', $this->plugin_url . 'assets/mrdipeshcpsstyle-admin.css',array(),$plugin_data['Version']);
		wp_enqueue_script('mrdipeshcpsscript', $this->plugin_url . 'assets/mrdipeshcpsscript-admin.js',array(),$plugin_data['Version']."1");
	}
	function wpEnqueue()
	{
		$plugin_data = get_file_data( __FILE__, array( 'Version' => 'Version' ), false );
		wp_enqueue_style('mrdipeshcpsstyle', $this->plugin_url . 'assets/mrdipeshcpsstyle.css');
		wp_enqueue_script('mrdipeshcpsscript', $this->plugin_url . 'assets/mrdipeshcpsscript.js', array('jquery'), null, true);
		wp_localize_script('mrdipeshcpsscript', 'mrdipeshcpsscript_vars', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'page_id' => get_the_ID()
		));
	}
}
