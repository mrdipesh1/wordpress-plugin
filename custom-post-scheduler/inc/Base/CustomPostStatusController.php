<?php

/**
 * @package  MrdipeshCPSPlugin
 */

namespace Mrdipesh\CPS\Base;

use Mrdipesh\CPS\Api\SettingsApi;
use Mrdipesh\CPS\Base\BaseController;
use Mrdipesh\CPS\Api\Callbacks\CustomStatusCallbacks;
use Mrdipesh\CPS\Api\Callbacks\AdminCallbacks;

class CustomPostStatusController extends BaseController
{

    public $mrdipesh_cps_callbacks;
    public $mrdipesh_cps_status_list = array();
    public $mrdipesh_cps_status_option_script = "";
    public $mrdipesh_cps_custom_post_types = array('post', 'page');
    public $mrdipesh_cps_status_callbacks;
    public $mrdipesh_cps_subpages = array();
    public $mrdipesh_cps_post_key_expired = "mrdipesh_cps_expiry_date";
    public $mrdipesh_cps_post_key_scheduled = "mrdipesh_cps_applicable_from";
    public $mrdipesh_cps_post_status_expired = "expired";
    public $mrdipesh_cps_post_status_scheduled = "scheduled";
    public $mrdipesh_cps_post_status_published = "publish";


    public function register()
    {
        if (!$this->activated('cpt_manager')) return;
        if (!$this->activated('cps_manager')) return;
        $this->mrdipesh_cps_storeCustomPostTypes();
        $this->mrdipesh_cps_setStatusList();

        if (empty($this->mrdipesh_cps_status_list)) return;
        $this->mrdipesh_cps_registerStatus();

        add_action('add_meta_boxes', array($this, 'mrdipesh_cps_registerCustomMetaBoxes'));
        add_action('admin_footer-edit.php', array($this, 'mrdipesh_cps_registerJs'));
        add_action('admin_footer-post-new.php', array($this, 'mrdipesh_cps_registerJs'));
        add_action('save_post', array($this, 'mrdipesh_cps_saveCustomMetaData'),10,2);
        // $this->registerCustomMetaBoxes();
        add_action('mrdipesh_cps_check_expired_posts', array($this, 'mrdipesh_cps_check_expired_posts'));

        // Add custom class "expired" to body for expired posts/pages
        add_filter('body_class', array($this, 'mrdipesh_cps_addClassExpired'));

        // Schedule cron event to check for expired posts daily
        add_action('wp', array($this, 'mrdipesh_cps_scheduleCheckPostExpiry'));
        add_action('wp_ajax_overlayExpired', array($this, 'mrdipesh_cps_overlayExpired'));
        add_action('wp_ajax_nopriv_overlayExpired', array($this, 'mrdipesh_cps_overlayExpired'));
    }
    public function mrdipesh_cps_storeCustomPostTypes()
    {
        $options = get_option('mrdipesh_cps_plugin_cpt') ?: array();
        foreach ($options as $option) {
            $post_type = $option['post_type'];
            array_push($this->mrdipesh_cps_custom_post_types, $post_type);
        }
    }
    public function mrdipesh_cps_setStatusList()
    {
        $this->mrdipesh_cps_status_list = [
            $this->mrdipesh_cps_post_status_scheduled => [
                "label" => "Scheduled",
                "meta" => [
                    "id" => $this->mrdipesh_cps_post_key_scheduled,
                    "title" => "Applicable From",
                    "callback" => array($this, 'mrdipesh_cps_callbackStatus'),
                    "screen" =>  $this->mrdipesh_cps_custom_post_types,
                    "position" => "side",
                ],
                "post" => [
                    "key" =>  $this->mrdipesh_cps_post_key_scheduled,
                ]
            ],
            $this->mrdipesh_cps_post_status_expired  => [
                "label" => "Expired",
                "meta" => [
                    "id" => $this->mrdipesh_cps_post_key_expired,
                    "title" => "Expiry Date",
                    "callback" => array($this, 'mrdipesh_cps_callbackStatus'),
                    "screen" =>  $this->mrdipesh_cps_custom_post_types,
                    "position" => "side",
                ],
                "post" => [
                    "key" =>  $this->mrdipesh_cps_post_key_expired,
                ]
            ]
        ];
    }
    public function mrdipesh_cps_callbackStatus($post)
    {
        foreach ($this->mrdipesh_cps_status_list as $post_status => $args) {
            $post_key = $args["post"]["key"];
            $meta = $args["meta"];
            // $post_key = $this->post_key_expired;
            $value =  get_post_meta($post->ID, $post_key, true);
            echo '
            <div class="mrdipeshcps-dates-group">
                <label for="' . esc_attr($meta["id"]) . '" class="cps-label">' . esc_attr($meta["title"]) . '</label><br>
                <input type="date" id="' . esc_attr($post_key) . '" name="' . esc_attr($post_key) . '" value="' . esc_attr($value) . '" class=""/><br>
            </div>';
        }
    }
    public function mrdipesh_cps_registerStatus()
    {
        foreach ($this->mrdipesh_cps_status_list as $post_status => $args) {
            $args = array(
                'label'                     => $args["label"],
                'public'                    => true,
                'publicly_queryable'        => false,
                'internal'                  => true,
                'private'                   => true,
                'exclude_from_search'       => true,
                'show_in_admin_all_list'    => false,
                'show_in_admin_status_list' => true,
                'label_count'               => 0
            );
            register_post_status(strtolower($post_status), $args);
            $this->mrdipesh_cps_status_option_script .= "jQuery( 'select[name=\"_status\"]' ).append( '<option value=\"" . strtolower($post_status) . "\">" . $args["label"] . "</option>' );";
            $this->mrdipesh_cps_status_option_script .= "jQuery( 'select[name=\"post_status\"]' ).append( '<option value=\"" . strtolower($post_status) . "\">" . $args["label"] . "</option>' );";
        }
    }
    public function mrdipesh_cps_registerJs()
    {
        $script =  "<script>
        jQuery(document).ready( function() {
            " .  esc_html($this->mrdipesh_cps_status_option_script) . "
        }); 
        </script>";
        wp_add_inline_script('mrdipesh_cpsscript', $script, $position = 'after');
    }
    public function mrdipesh_cps_registerCustomMetaBoxes()
    {
        add_meta_box('mrdipesh_cps_status', 'CPS Dates', array($this, 'mrdipesh_cps_callbackStatus'), $this->mrdipesh_cps_custom_post_types, 'side');
    }
    public function mrdipesh_cps_saveCustomMetaData($post_id, $post)
    {
        // unhook this function so it doesn't loop infinitely
        remove_action('save_post', array($this, 'mrdipesh_cps_saveCustomMetaData'));
        
		// if (isset($_POST['mrdipesh_cps_edit_cpt']) || 
		// 		wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mrdipesh_cps_edit_cpt'])), 'mrdipesh_cps_edit_cpt_20250905') ) { 	
        
        // Check autosave
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
            return;
        }

        // Check user permission
        if ( !current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
 
        if (isset($_POST['_wpnonce'] ) ) {
            $nonce_action = $post->ID ? 'update-post_' . $post_id : 'add-post'; // edit vs add
            if (wp_verify_nonce( sanitize_text_field(wp_unslash( $_POST['_wpnonce'] )), $nonce_action ) ) {
                foreach ($this->mrdipesh_cps_status_list as $post_status => $args) {
                    $post_key = $args["post"]["key"];
                    if (isset($_POST[$post_key])) {
                        $value = sanitize_text_field(wp_unslash($_POST[$post_key]));
                        if ($value != "") {
                            update_post_meta($post_id,  $post_key,  $value);
                            $this->mrdipesh_cps_checkPostDateAndUpdateStatus($post_key, $value, $post_id);
                        }
                    }
                }                
            }
        } 
        add_action('save_post',  array($this, 'mrdipesh_cps_saveCustomMetaData'),10,2);
    }
    public function mrdipesh_cps_checkPostDateAndUpdateStatus($post_key, $date_value, $post_id)
    {
        if ($this->mrdipesh_cps_post_key_expired == $post_key) {
            $expiry_date = $date_value . " 23:59:59";
            if (strtotime($expiry_date) < $this->mrdipesh_cps_getTimestamp()) {
                $this->mrdipesh_cps_changePostStatus($post_id, $this->mrdipesh_cps_post_status_expired);
            }
        }
        if ($this->mrdipesh_cps_post_key_scheduled == $post_key) {
            $applicable_date = $date_value . " 00:00:00";
            if (strtotime($applicable_date) > $this->mrdipesh_cps_getTimestamp()) {
                $this->mrdipesh_cps_changePostStatus($post_id, $this->mrdipesh_cps_post_status_scheduled);
            }
        }
    }
    public function mrdipesh_cps_addClassExpired($classes)
    {
        global $post;
        if ($post) {
            if ($post->post_status == 'publish') {
                $expired = $this->mrdipesh_cps_isPostExpired($post->ID);
                if ($expired) {
                    $this->mrdipesh_cps_changePostStatus($post->ID, $this->mrdipesh_cps_post_status_expired);
                    $classes[] = 'expired';
                }
            }
            if ($post->post_status == $this->mrdipesh_cps_post_status_expired) {
                $classes[] = 'expired';
            }
        }
        return $classes;
    }
    public function mrdipesh_cps_isPostExpired($post_id)
    {
        $expired = false;
        if (!$this->activated('cps_manager')) return  $expired;
        $expiry_date = get_post_meta($post_id, $this->mrdipesh_cps_post_key_expired, true);
        $expiry_date = $expiry_date . " 23:59:59";
        if ($expiry_date && strtotime($expiry_date) < $this->mrdipesh_cps_getTimestamp()) {
            $expired = true;
        }
        return $expired;
    }
    public function mrdipesh_cps_changePostStatus($post_id, $new_status)
    {
        // print_r($new_status);
        wp_update_post(array(
            'ID'          => $post_id,
            'post_status' => $new_status
        ));
    }
    public function mrdipesh_cps_overlayExpired()
    {
        $expired = false;

        if (isset( $_POST['nonce'] ) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'mrdipesh_cpsscript_overlay_expired_action') ) {
            $page_id = isset($_POST['page_id']) ? intval(sanitize_text_field(wp_unslash($_POST['page_id']))) : 0;
            // $page_id=get_the_ID();
            if ($page_id > 0) {
                $expired = $this->mrdipesh_cps_isPostExpired($page_id);
            }
        }

       
        $message = array("status" =>  $expired);

        wp_send_json_success($message);
    }
    public function mrdipesh_cps_scheduleCheckPostExpiry()
    {
        if (!wp_next_scheduled('mrdipesh_cps_checkPostDates')) {
            wp_schedule_event(strtotime(date_i18n('Y-m-d') . " 00:00:00"), 'daily', 'mrdipesh_cps_checkPostDates');
        }
    }
    public function mrdipesh_cps_checkPostDates()
    {
        $this->mrdipesh_cps_checkPostDateForExpiry();
        $this->mrdipesh_cps_checkPostDateForPublish();
    }
    public function mrdipesh_cps_checkPostDateForExpiry()
    {
        // $args = array(
        //     'post_type'      => $this->custom_post_types,
        //     'post_status'    => $this->post_status_published,
        //     'meta_query'     => array(
        //         'relation' => 'AND',
        //         array(
        //             'key'     => $this->post_key_expired,
        //             'value'   => $this->getTimestamp(),
        //             'compare' => '<',
        //             'type'    => 'TEXT',
        //         ),
        //         array(
        //             'key'     => $this->post_key_expired,
        //             'value'   => gmdate('Y-m-d') . " 23:59:59",
        //             'compare' => 'EXISTS',
        //         ),
        //     ),
        //     'fields'         => 'ids',
        //     'no_found_rows'  => true,
        //     'posts_per_page' => -1,
        // );
        // $expired_posts = new \WP_Query($args);
        // if ($expired_posts->have_posts()) {
        //     while ($expired_posts->have_posts()) {
        //         $expired_posts->the_post();
        //         $this->changePostStatus(get_the_ID(), $this->post_status_expired);
        //     }
        //     wp_reset_postdata();
        // }
        global $wpdb;
        $cache_key = 'mrdipesh_cps_expired_post_ids';
        $expired_post_ids = wp_cache_get( $cache_key, 'MrdipeshCPSPlugin' );
        
        if ( false === $expired_post_ids ) {
            // Direct SQL to fetch expired post IDs
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
            $expired_post_ids = $wpdb->get_col( $wpdb->prepare(
                "SELECT p.ID
                FROM {$wpdb->posts} p
                INNER JOIN {$wpdb->postmeta} pm 
                    ON p.ID = pm.post_id
                WHERE p.post_type = %s
                AND p.post_status = %s
                AND pm.meta_key = %s
                AND pm.meta_value < %s",
                $this->mrdipesh_cps_custom_post_types,
                $this->mrdipesh_cps_post_status_published,
                $this->mrdipesh_cps_post_key_expired,
                $this->mrdipesh_cps_getTimestamp()
            ) );   
            wp_cache_set( $cache_key, $expired_post_ids, 'MrdipeshCPSPlugin', 60 );
        }

        // Loop through IDs and update status
        if (!empty( $expired_post_ids ) ) {
            foreach ( $expired_post_ids as $post_id ) {
                $this->mrdipesh_cps_changePostStatus( (int) $post_id, $this->mrdipesh_cps_post_status_expired );
            } 
            wp_reset_postdata();
        }
    }
    public function mrdipesh_cps_checkPostDateForPublish()
    {
        // $args = array(
        //     'post_type'      => $this->custom_post_types,
        //     'post_status'    => $this->post_status_scheduled,
        //     'meta_query'     => array(
        //         // 'relation' => 'AND',
        //         array(
        //             'key'     => $this->post_key_scheduled,
        //             'value'   => $this->getTimestamp(),
        //             'compare' => '<',
        //             'type'    => 'DATETIME',
        //         ),
        //         // array(
        //         //     'key'     => $this->post_key_scheduled,
        //         //     'value'   => gmdate('Y-m-d') . " 00:00:00",
        //         //     'compare' => 'EXISTS',
        //         // ),
        //     ),
        //     'fields'         => 'ids',
        //     'no_found_rows'  => true,
        //     'posts_per_page' => -1,
        // );
        // $scheduled_posts = new \WP_Query($args);
        // if ($scheduled_posts->have_posts()) {
        //     while ($scheduled_posts->have_posts()) {
        //         $scheduled_posts->the_post();
        //         $this->changePostStatus(get_the_ID(), $this->post_status_published);
        //     }
        //     wp_reset_postdata();
        // }
        global $wpdb;

        $cache_key = 'mrdipesh_cps_scheduled_post_ids';
        $scheduled_post_ids = wp_cache_get( $cache_key, 'MrdipeshCPSPlugin' );

        if ( false === $scheduled_post_ids ) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
            $scheduled_post_ids = $wpdb->get_col( $wpdb->prepare(
                "SELECT p.ID
                FROM {$wpdb->posts} p
                INNER JOIN {$wpdb->postmeta} pm 
                    ON p.ID = pm.post_id
                WHERE p.post_type = %s
                AND p.post_status = %s
                AND pm.meta_key = %s
                AND pm.meta_value < %s",
                $this->mrdipesh_cps_custom_post_types,
                $this->mrdipesh_cps_post_status_scheduled,
                $this->mrdipesh_cps_post_key_scheduled,
                $this->mrdipesh_cps_getTimestamp()
            ) );

            // Cache for 1 hour
            wp_cache_set( $cache_key, $scheduled_post_ids, 'MrdipeshCPSPlugin', 60 );
        }

        if ( ! empty( $scheduled_post_ids ) ) {
            foreach ( $scheduled_post_ids as $post_id ) {
                $this->mrdipesh_cps_changePostStatus( (int) $post_id, $this->mrdipesh_cps_post_status_published );
            }
        }
    }
}
