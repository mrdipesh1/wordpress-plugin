<?php

/**
 * @package  MrdipeshCPSPlugin
 */

namespace Mrdipesh\CPS\Api\Callbacks;

class CustomStatusCallbacks
{

	public function cptSectionManager()
	{
		echo 'Create as many Custom Post Types as you want.';
	}

	public function cptSanitize($input)
	{
		$output = get_option('mrdipesh_cps_plugin_cpt');

		if (isset($_POST["remove"])) { 
			if ( isset($_POST['mrdipesh_cps_remove_cpt']) || 
				wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mrdipesh_cps_remove_cpt'])), 'mrdipesh_cps_remove_cpt_20250905') ) { 
					unset($output[sanitize_text_field(wp_unslash($_POST["remove"]))]);
		
					return $output;
			}
		}

		if (count($output) == 0) {
			$output[$input['post_type']] = $input;

			return $output;
		}

		foreach ($output as $key => $value) {
			if ($input['post_type'] === $key) {
				$output[$key] = $input;
			} else {
				$output[$input['post_type']] = $input;
			}
		}

		return $output;
	}

	public function textField($args)
	{
		$name = $args['label_for'];
		$option_name = $args['option_name'];
		$value = '';

		if (isset($_POST["edit_post"])) {
			if ( isset($_POST['mrdipesh_cps_edit_cpt']) || 
				wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mrdipesh_cps_edit_cpt'])), 'mrdipesh_cps_edit_cpt_20250905') ) { 
					$input = get_option($option_name);
					$value = $input[sanitize_text_field(wp_unslash($_POST["edit_post"]))][$name];
			}
			 
		}

        // echo '<input type="date" id="applicable_from" name="applicable_from" value="' . esc_attr($applicable_from) . '" />';
		echo '<input type="text" class="regular-text" id="' . esc_attr($name) . '" name="' . esc_attr($option_name) . '[' . esc_html($name) . ']" value="' . esc_attr($value) . '" placeholder="' . esc_attr($args['placeholder']) . '" required>';
	}

	public function checkboxField($args)
	{
		$name = $args['label_for'];
		$classes = $args['class'];
		$option_name = $args['option_name'];
		$checked = false;

		if (isset($_POST["edit_post"])) {
			if ( isset($_POST['mrdipesh_cps_edit_cpt']) || 
			wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mrdipesh_cps_edit_cpt'])),'mrdipesh_cps_edit_cpt_20250905') ) { 
					$checkbox = get_option($option_name);
					$checked = isset($checkbox[sanitize_text_field(wp_unslash($_POST["edit_post"]))][$name]) ?: false;
			}
		}

		echo '
		<div class="' . esc_attr($classes) . '">
			<input type="checkbox" id="' . esc_attr($name) . '" name="' . esc_attr($option_name) . '[' . esc_html($name) . ']" value="1" class="" ' . ($checked ? 'checked' : '') . '>
			<label for="' . esc_attr($name) . '"><div></div></label>
		</div>';
	}
}
