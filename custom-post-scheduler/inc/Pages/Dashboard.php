<?php

/**
 * @package  MrdipeshCPSPlugin
 */

namespace Mrdipesh\CPS\Pages;

use Mrdipesh\CPS\Api\SettingsApi;
use Mrdipesh\CPS\Base\BaseController;
use Mrdipesh\CPS\Api\Callbacks\AdminCallbacks;
use Mrdipesh\CPS\Api\Callbacks\ManagerCallbacks;

class Dashboard extends BaseController
{
	public $settings;

	public $callbacks;

	public $callbacks_mngr;

	public $pages = array();

	public function register()
	{
		$this->settings = new SettingsApi();

		$this->callbacks = new AdminCallbacks();

		$this->callbacks_mngr = new ManagerCallbacks();

		$this->setPages();

		$this->setSettings();
		$this->setSections();
		$this->setFields();

		$this->settings->addPages($this->pages)->withSubPage('Dashboard')->register();
	}

	public function setPages()
	{
		$this->pages = array(
			array(
				'page_title' => 'Custom Post Scheduler Plugin',
				'menu_title' => 'CPS',
				'capability' => 'manage_options',
				'menu_slug' => 'mrdipesh_cps_plugin',
				'callback' => array($this->callbacks, 'adminDashboard'),
				'icon_url' => 'dashicons-calendar',
				'position' => 30
			)
		);
	}

	public function setSettings()
	{
		$args = array(
			array(
				'option_group' => 'mrdipesh_cps_plugin_settings',
				'option_name' => 'mrdipesh_cps_plugin',
				'callback' => array($this->callbacks_mngr, 'checkboxSanitize')
			)
		);

		$this->settings->setSettings($args);
	}

	public function setSections()
	{
		$args = array(
			array(
				'id' => 'mrdipesh_cps_admin_index',
				'title' => 'Feature Manager',
				'callback' => array($this->callbacks_mngr, 'adminSectionManager'),
				'page' => 'mrdipesh_cps_plugin'
			)
		);

		$this->settings->setSections($args);
	}

	public function setFields()
	{
		$args = array();

		foreach ($this->managers as $key => $value) {
			$args[] = array(
				'id' => $key,
				'title' => $value,
				'callback' => array($this->callbacks_mngr, 'checkboxField'),
				'page' => 'mrdipesh_cps_plugin',
				'section' => 'mrdipesh_cps_admin_index',
				'args' => array(
					'option_name' => 'mrdipesh_cps_plugin',
					'label_for' => $key,
					'class' => 'ui-toggle'
				)
			);
		}

		$this->settings->setFields($args);
	}
}
