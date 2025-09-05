<?php  if ( !defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap">
	<h1>Custom Post Scheduler Plugin</h1>
	<?php settings_errors(); ?>

	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab-1">CPS Features</a></li>
	</ul>

	<div class="tab-content">
		<div id="tab-1" class="tab-pane active">

			<form method="post" action="options.php">
				<?php
				settings_fields('mrdipesh_cps_plugin_settings');
				do_settings_sections('mrdipesh_cps_plugin');
				submit_button();
				?>
			</form>

		</div>
	</div>
</div>