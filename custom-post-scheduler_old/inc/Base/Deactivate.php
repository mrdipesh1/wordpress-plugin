<?php

/**
 * @package  MrdipeshCPSPlugin
 */

namespace Mrdipesh\CPS\Base;

class Deactivate
{
	public static function deactivate()
	{
		flush_rewrite_rules();
	}
}
