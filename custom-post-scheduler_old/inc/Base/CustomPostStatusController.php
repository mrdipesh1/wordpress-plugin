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

    public $callbacks;
    public $status_list = array();
    public $status_option_script = "";
    public $custom_post_types = array('post', 'page');
    public $status_callbacks;
    public $subpages = array();
    public $post_key_expired = "expiry_date";
    public $post_key_scheduled = "applicable_from";
    public $post_status_expired = "expired";
    public $post_status_scheduled = "scheduled";
    public $post_status_published = "publish";


    public function register()
    {
        if (!$this->activated('cpt_manager')) return;
        if (!$this->activated('cps_manager')) return;
        $this->storeCustomPostTypes();
        $this->setStatusList();

        if (empty($this->status_list)) return;
        $this->registerStatus();

        add_action('add_meta_boxes', array($this, 'registerCustomMetaBoxes'));
        add_action('admin_footer-edit.php', array($this, 'registerJs'));
        add_action('admin_footer-post-new.php', array($this, 'registerJs'));
        add_action('save_post', array($this, 'saveCustomMetaData'));
        // $this->registerCustomMetaBoxes();
        add_action('check_expired_posts', array($this, 'check_expired_posts'));

        // Add custom class "expired" to body for expired posts/pages
        add_filter('body_class', array($this, 'addClassExpired'));

        // Schedule cron event to check for expired posts daily
        add_action('wp', array($this, 'scheduleCheckPostExpiry'));
        add_action('wp_ajax_overlayExpired', array($this, 'overlayExpired'));
        add_action('wp_ajax_nopriv_overlayExpired', array($this, 'overlayExpired'));
    }
    public function storeCustomPostTypes()
    {
        $options = get_option('mrdipesh_cps_plugin_cpt') ?: array();
        foreach ($options as $option) {
            $post_type = $option['post_type'];
            array_push($this->custom_post_types, $post_type);
        }
    }
    public function setStatusList()
    {
        $this->status_list = [
            $this->post_status_scheduled => [
                "label" => "Scheduled",
                "meta" => [
                    "id" => $this->post_key_scheduled,
                    "title" => "Applicable From",
                    "callback" => array($this, 'callbackStatus'),
                    "screen" =>  $this->custom_post_types,
                    "position" => "side",
                ],
                "post" => [
                    "key" =>  $this->post_key_scheduled,
                ]
            ],
            $this->post_status_expired  => [
                "label" => "Expired",
                "meta" => [
                    "id" => $this->post_key_expired,
                    "title" => "Expiry Date",
                    "callback" => array($this, 'callbackStatus'),
                    "screen" =>  $this->custom_post_types,
                    "position" => "side",
                ],
                "post" => [
                    "key" =>  $this->post_key_expired,
                ]
            ]
        ];
    }
    public function callbackStatus($post)
    {
        foreach ($this->status_list as $post_status => $args) {
            $post_key = $args["post"]["key"];
            $meta = $args["meta"];
            // $post_key = $this->post_key_expired;
            $value =  get_post_meta($post->ID, $post_key, true);
            echo '
            <div class="mrdipeshcps-dates-group">
                <label for="' . $meta["id"] . '" class="cps-label">' . $meta["title"] . '</label><br>
                <input type="date" id="' . $post_key . '" name="' . $post_key . '" value="' . esc_attr($value) . '" class=""/><br>
            </div>';
        }
    }
    public function registerStatus()
    {
        foreach ($this->status_list as $post_status => $args) {
            $args = array(
                'label'                     => _x($args["label"], 'post'),
                'public'                    => true,
                'publicly_queryable'        => false,
                'internal'                  => true,
                'private'                   => true,
                'exclude_from_search'       => true,
                'show_in_admin_all_list'    => false,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop($args["label"] . ' <span class="count">(%s)</span>', $args["label"] . ' <span class="count">(%s)</span>')
            );
            register_post_status(strtolower($post_status), $args);
            $this->status_option_script .= "jQuery( 'select[name=\"_status\"]' ).append( '<option value=\"" . strtolower($post_status) . "\">" . $args["label"] . "</option>' );";
            $this->status_option_script .= "jQuery( 'select[name=\"post_status\"]' ).append( '<option value=\"" . strtolower($post_status) . "\">" . $args["label"] . "</option>' );";
        }
    }
    public function registerJs()
    {
        echo "<script>
        jQuery(document).ready( function() {
            " . $this->status_option_script . "
        }); 
        </script>";
    }
    public function registerCustomMetaBoxes()
    {
        add_meta_box('mrdipesh_cps_status', 'CPS Dates', array($this, 'callbackStatus'), $this->custom_post_types, 'side');
    }
    public function saveCustomMetaData($post_id)
    {
        // unhook this function so it doesn't loop infinitely
        remove_action('save_post', array($this, 'saveCustomMetaData'));
        foreach ($this->status_list as $post_status => $args) {
            $post_key = $args["post"]["key"];
            if (isset($_POST[$post_key])) {
                $value = sanitize_text_field($_POST[$post_key]);
                if ($value != "") {
                    update_post_meta($post_id,  $post_key,  $value);
                    $this->checkPostDateAndUpdateStatus($post_key, $value, $post_id);
                }
            }
        }
        // re-hook this function
        add_action('save_post',  array($this, 'saveCustomMetaData'));
    }
    public function checkPostDateAndUpdateStatus($post_key, $date_value, $post_id)
    {
        if ($this->post_key_expired == $post_key) {
            $expiry_date = $date_value . " 23:59:59";
            if (strtotime($expiry_date) < $this->getTimestamp()) {
                $this->changePostStatus($post_id, $this->post_status_expired);
            }
        }
        if ($this->post_key_scheduled == $post_key) {
            $applicable_date = $date_value . " 00:00:00";
            if (strtotime($applicable_date) > $this->getTimestamp()) {
                $this->changePostStatus($post_id, $this->post_status_scheduled);
            }
        }
    }
    public function addClassExpired($classes)
    {
        global $post;
        if ($post) {
            if ($post->post_status == 'publish') {
                $expired = $this->isPostExpired($post->ID);
                if ($expired) {
                    $this->changePostStatus($post->ID, $this->post_status_expired);
                    $classes[] = 'expired';
                }
            }
            if ($post->post_status == $this->post_status_expired) {
                $classes[] = 'expired';
            }
        }
        return $classes;
    }
    public function isPostExpired($post_id)
    {
        $expired = false;
        if (!$this->activated('cps_manager')) return  $expired;
        $expiry_date = get_post_meta($post_id, $this->post_key_expired, true);
        $expiry_date = $expiry_date . " 23:59:59";
        if ($expiry_date && strtotime($expiry_date) < $this->getTimestamp()) {
            $expired = true;
        }
        return $expired;
    }
    public function changePostStatus($post_id, $new_status)
    {
        // print_r($new_status);
        wp_update_post(array(
            'ID'          => $post_id,
            'post_status' => $new_status
        ));
    }
    public function overlayExpired()
    {
        $expired = false;
        $page_id = isset($_POST['page_id']) ? intval($_POST['page_id']) : 0;
        if ($page_id > 0) {
            $expired = $this->isPostExpired($page_id);
        }
        $message = array("status" =>  $expired);

        wp_send_json_success($message);
    }
    public function scheduleCheckPostExpiry()
    {
        if (!wp_next_scheduled('checkPostDates')) {
            wp_schedule_event(strtotime(date_i18n('Y-m-d') . " 00:00:00"), 'daily', 'checkPostDates');
        }
    }
    public function checkPostDates()
    {
        $this->checkPostDateForExpiry();
        $this->checkPostDateForPublish();
    }
    public function checkPostDateForExpiry()
    {
        $args = array(
            'post_type'      => $this->custom_post_types,
            'post_status'    => $this->post_status_published,
            'meta_query'     => array(
                'relation' => 'AND',
                array(
                    'key'     => $this->post_key_expired,
                    'value'   => $this->getTimestamp(),
                    'compare' => '<',
                    'type'    => 'TEXT',
                ),
                array(
                    'key'     => $this->post_key_expired,
                    'value'   => date('Y-m-d') . " 23:59:59",
                    'compare' => 'EXISTS',
                ),
            ),
            'fields'         => 'ids',
            'no_found_rows'  => true,
            'posts_per_page' => -1,
        );
        $expired_posts = new \WP_Query($args);
        if ($expired_posts->have_posts()) {
            while ($expired_posts->have_posts()) {
                $expired_posts->the_post();
                $this->changePostStatus(get_the_ID(), $this->post_status_expired);
            }
            wp_reset_postdata();
        }
    }
    public function checkPostDateForPublish()
    {
        $args = array(
            'post_type'      => $this->custom_post_types,
            'post_status'    => $this->post_status_scheduled,
            'meta_query'     => array(
                'relation' => 'AND',
                array(
                    'key'     => $this->post_key_scheduled,
                    'value'   => $this->getTimestamp(),
                    'compare' => '<',
                    'type'    => 'TEXT',
                ),
                array(
                    'key'     => $this->post_key_scheduled,
                    'value'   => date('Y-m-d') . " 00:00:00",
                    'compare' => 'EXISTS',
                ),
            ),
            'fields'         => 'ids',
            'no_found_rows'  => true,
            'posts_per_page' => -1,
        );
        $scheduled_posts = new \WP_Query($args);
        if ($scheduled_posts->have_posts()) {
            while ($scheduled_posts->have_posts()) {
                $scheduled_posts->the_post();
                $this->changePostStatus(get_the_ID(), $this->post_status_published);
            }
            wp_reset_postdata();
        }
    }
}
