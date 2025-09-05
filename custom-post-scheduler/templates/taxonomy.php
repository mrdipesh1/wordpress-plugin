<?php  if ( !defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap">
    <h1>Taxonomy Manager</h1>
    <?php 
		settings_errors(); 

		$tab_text = "Add";
		$tab1_status = "active";
		$tab2_status = "";
		if (isset($_POST['mrdipesh_cps_edit_tax']) || 
		wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mrdipesh_cps_edit_tax'])), 'mrdipesh_cps_edit_tax_20250905') ) {
			if(isset($_POST["edit_taxonomy"])){
				$tab1_status = "";
				$tab2_status = "active";
				$tab_text = "Edit";
			}
		}

	?>

	<ul class="nav mrdipesh-cps-nav-tabs">
		<li class="<?php echo esc_attr($tab1_status); ?>"><a href="#tab-1">Your Taxonomies</a></li>
		<li class="<?php echo esc_attr($tab2_status); ?>">
			<a href="#tab-2">
				<?php echo esc_html($tab_text); ?> Taxonomy
			</a>
		</li>
	</ul> 

    <div class="tab-content">
		<div id="tab-1" class="tab-pane <?php echo esc_attr($tab1_status); ?>">

            <h3>Manage Your Custom Taxonomies</h3>

            <?php
            $options = get_option('mrdipesh_cps_plugin_tax') ?: array();

            echo '<table class="cpt-table"><tr><th>ID</th><th>Singular Name</th><th class="text-center">Hierarchical</th><th class="text-center">Actions</th></tr>';

            foreach ($options as $option) {
                $hierarchical = isset($option['hierarchical']) ? "TRUE" : "FALSE";

                echo "
                <tr>
                <td>".esc_html($option['taxonomy'])."</td>
                <td>".esc_html($option['singular_name'])."</td>
                <td class=\"text-center\">".esc_html($hierarchical)."</td>
                <td class=\"text-center\">";

                    echo '<form method="post" action="" class="inline-block">';
                    wp_nonce_field( 'mrdipesh_cps_edit_tax_20250905', 'mrdipesh_cps_edit_tax' ); 
                    echo '<input type="hidden" name="edit_taxonomy" value="' . esc_attr($option['taxonomy']) . '">';
                    submit_button('Edit', 'primary small', 'submit', false);
                    echo '</form> ';

                    echo '<form method="post" action="options.php" class="inline-block">';
                    settings_fields('mrdipesh_cps_plugin_tax_settings');
                    wp_nonce_field( 'mrdipesh_cps_remove_tax_20250905', 'mrdipesh_cps_remove_tax' ); 
                    echo '<input type="hidden" name="remove" value="' . esc_attr($option['taxonomy']) . '">';
                    submit_button('Delete', 'delete small', 'submit', false, array(
                        'onclick' => 'return confirm("Are you sure you want to delete this Custom Taxonomy? The data associated with it will not be deleted.");'
                    ));
                    echo '</form>
                </td>
                </tr>';
            }

            echo '</table>';
            ?>

        </div>
        
		<div id="tab-1" class="tab-pane <?php echo esc_attr($tab2_status); ?>">
            <form method="post" action="options.php">
                <?php
                settings_fields('mrdipesh_cps_plugin_tax_settings');
                do_settings_sections('mrdipesh_cps_taxonomy');
                wp_nonce_field( 'mrdipesh_cps_edit_tax_20250905', 'mrdipesh_cps_edit_tax' ); 
                submit_button();
                ?>
            </form>
        </div>
    </div>
</div>