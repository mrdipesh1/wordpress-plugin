<?php

/**
 * @package  MrdipeshCPSPlugin
 */

namespace Mrdipesh\CPS\Api\Callbacks;

class TaxonomyCallbacks
{
	public function taxSectionManager()
	{
		echo 'Create as many Custom Taxonomies as you want.';
	}

	public function taxSanitize($input)
	{
		$output = get_option('mrdipesh_cps_plugin_tax');

		if (isset($_POST["remove"])) { 
			if (isset($_POST['mrdipesh_cps_remove_tax']) || 
				wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mrdipesh_cps_remove_tax'])), 'mrdipesh_cps_remove_tax_20250905') ) { 
					unset($output[sanitize_text_field(wp_unslash($_POST["remove"]))]);
		
					return $output;
			}
		}

		if (count($output) == 0) {
			$output[$input['taxonomy']] = $input;

			return $output;
		}

		foreach ($output as $key => $value) {
			if ($input['taxonomy'] === $key) {
				$output[$key] = $input;
			} else {
				$output[$input['taxonomy']] = $input;
			}
		}

		return $output;
	}

	public function textField($args)
	{
		$name = $args['label_for'];
		$option_name = $args['option_name'];
		$value = '';

		if (isset($_POST["edit_taxonomy"])) {
			if (isset($_POST['mrdipesh_cps_edit_tax']) || 
				wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mrdipesh_cps_edit_tax'])), 'mrdipesh_cps_edit_tax_20250905') ) { 
					$input = get_option($option_name);
					$value = $input[sanitize_text_field(wp_unslash($_POST["edit_taxonomy"]))][$name];
			} 
		}

		echo '<input type="text" class="regular-text" id="' . esc_attr($name) . '" name="' . esc_attr($option_name) . '[' . esc_html($name) . ']" value="' . esc_attr($value) . '" placeholder="' . esc_attr($args['placeholder']) . '" required>';
	}

	public function checkboxField($args)
	{
		$name = $args['label_for'];
		$classes = $args['class'];
		$option_name = $args['option_name'];
		$checked = false;

		if (isset($_POST["edit_taxonomy"])) {
			if (isset($_POST['mrdipesh_cps_edit_tax']) || 
				wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mrdipesh_cps_edit_tax'])), 'mrdipesh_cps_edit_tax_20250905') ) { 
					$checkbox = get_option($option_name);
					$checked = isset($checkbox[sanitize_text_field(wp_unslash($_POST["edit_taxonomy"]))][$name]) ?: false; 
			}
		}

		echo '<div class="' . esc_attr($classes) . '"><input type="checkbox" id="' . esc_attr($name) . '" name="' . esc_attr($option_name) . '[' . esc_html($name) . ']" value="1" class="" ' . ($checked ? 'checked' : '') . '><label for="' . esc_attr($name) . '"><div></div></label></div>';
	}

	public function checkboxPostTypesField($args)
	{
		$output = '';
		$name = $args['label_for'];
		$classes = $args['class'];
		$option_name = $args['option_name'];
		$checked = false;

		if (isset($_POST["edit_taxonomy"])) {
			if (isset($_POST['mrdipesh_cps_edit_tax']) || 
				wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mrdipesh_cps_edit_tax'])), 'mrdipesh_cps_edit_tax_20250905') ) { 
					$checkbox = get_option($option_name);
			}
		}

		$post_types = get_post_types(array('show_ui' => true));

		foreach ($post_types as $post) {

			if (isset($_POST["edit_taxonomy"])) {
				$checked = isset($checkbox[sanitize_text_field(wp_unslash($_POST["edit_taxonomy"]))][$name][$post]) ?: false;
			}

			$output .= '
			<div class="' . esc_attr($classes) . ' mb-10">
				<input type="checkbox" id="' . esc_attr($post) . '" name="' . esc_attr($option_name) . '[' . esc_html($name) . '][' . esc_html($post) . ']" value="1" class="" ' . ($checked ? 'checked' : '') . '>
				<label for="' . esc_attr($post) . '"><div></div></label> <strong>' . esc_attr($post) . '</strong>
			</div>';
		}

		// echo '<div class="' . $classes . '"><input type="checkbox" id="' . $name . '" name="' . $option_name . '[' . $name . ']" value="1" class="" ' . ( $checked ? 'checked' : '') . '><label for="' . $name . '"><div></div></label></div>';

		echo esc_html($output);
	}
}
