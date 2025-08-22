<?php

/**
 * @package  MrdipeshCPSPlugin
 */

namespace Mrdipesh\CPS\Base;

use Mrdipesh\CPS\Api\SettingsApi;
use Mrdipesh\CPS\Base\BaseController;
use Mrdipesh\CPS\Api\Callbacks\AdminCallbacks;

/**
 * 
 */
class GalleryController extends BaseController
{
	public $callbacks;

	public $subpages = array();

	public function register()
	{
		if (!$this->activated('gallery_manager')) return;

		$this->settings = new SettingsApi();

		$this->callbacks = new AdminCallbacks();

		$this->setSubpages();

		$this->settings->addSubPages($this->subpages)->register();
	}

	public function setSubpages()
	{
		$this->subpages = array(
			array(
				'parent_slug' => 'mrdipesh_cps_plugin',
				'page_title' => 'Gallery Manager',
				'menu_title' => 'Gallery Manager',
				'capability' => 'manage_options',
				'menu_slug' => 'mrdipesh_cps_gallery',
				'callback' => array($this->callbacks, 'adminGallery')
			)
		);
	}
}
