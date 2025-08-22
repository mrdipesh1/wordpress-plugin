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
class TestimonialController extends BaseController
{
	public $callbacks;

	public $subpages = array();

	public function register()
	{
		if (!$this->activated('testimonial_manager')) return;

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
				'page_title' => 'Testimonial Manager',
				'menu_title' => 'Testimonial Manager',
				'capability' => 'manage_options',
				'menu_slug' => 'mrdipesh_cps_testimonial',
				'callback' => array($this->callbacks, 'adminTestimonial')
			)
		);
	}
}
