<?php

/**
 * Plugin Name: HortaDAV
 * Plugin URI: https://github.com/codeccoop/hortadav
 * Description: Exportació dels esdeveniments del calendari de sembra d'hortalises compatible amb sistemes CalDAV
 * Version: 1.0.0
 * Author: Còdec Cooperativa
 * Author URI: https://www.codeccoop.org
 * License: GPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: hortadav
 */


if (!class_exists('HortaDAV')) {

    class HortaDAV
    {
        function __construct()
        {
            register_activation_hook(__FILE__, array('HortaDAV', 'activate'));
            register_deactivation_hook(__FILE__, array('HortaDAV', 'deactivate'));
            register_uninstall_hook(__FILE__, array('HortaDAV', 'uninstall'));

            add_action('init', array(&$this, 'init'));

            add_filter('the_content', array(&$this, 'set_archive_template'), 1);
            add_filter('template_include', array(&$this, 'include_archive_template'));

            if (!is_admin()) {
                add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));
            }
        }

        static function activate()
        {
            require_once __DIR__ . '/post_types/horta_event.php';
            hortadav_register_event();

            $data_path = plugin_dir_url(__FILE__) . 'data/calendar.json';
            $data = json_decode(file_get_contents($data_path), true);

            foreach ($data['VCALENDAR'][0]['VEVENT'] as $event) {
                hortadav_register_term($event['TAXONOMIES']['FAMILY'], 'family');
                hortadav_register_term($event['TAXONOMIES']['LIFECYCLE'], 'lifecycle');
                hortadav_register_term($event['TAXONOMIES']['HARVEST_TIME'], 'harvest_time');
                hortadav_register_term($event['TAXONOMIES']['SEEDING_DEPTH'], 'seeding');
                hortadav_register_term($event['TAXONOMIES']['GERMINATION_DAYS'], 'germination');
                hortadav_register_term($event['TAXONOMIES']['PLANTING_FRAME'], 'frame');
                hortadav_register_term($event['TAXONOMIES']['LOCATION'], 'location');

                hortadav_create_event(array(
                    'post_title' => $event['SUMMARY'],
                    'post_content' => $event['DESCRIPTION'],
                ), array(
                    # 'event' => $event['TAXONOMIES']['EVENT'],
                    'family' => $event['TAXONOMIES']['FAMILY'],
                    'lifecycle' => $event['TAXONOMIES']['LIFECYCLE'],
                    'harvest_time' => $event['TAXONOMIES']['HARVEST_TIME'],
                    'seeding' => $event['TAXONOMIES']['SEEDING_DEPTH'],
                    'germination' => $event['TAXONOMIES']['GERMINATION_DAYS'],
                    'frame' => $event['TAXONOMIES']['PLANTING_FRAME'],
                    'location' => $event['TAXONOMIES']['LOCATION'],
                ), array(
                    'startdate' => $event['DTSTART;VALUE=DATE'],
                    'enddate' => $event['DTEND;VALUE=DATE'],
                    'plant' => $event['PLANT']
                ));
            }

            wp_insert_term('hortadav_archive', 'category');
            hortadav_create_archive();
        }

        static function deactivate()
        {
            require_once __DIR__ . '/post_types/horta_event.php';

            hortadav_delete_events();
            hortadav_unregister_event();
            hortadav_delete_archive();
            wp_delete_category('hortadav_archive');
        }

        static function uninstall()
        {
        }

        function init()
        {
            require_once __DIR__ . '/post_types/horta_event.php';
            hortadav_register_event();

            register_taxonomy_for_object_type('category', 'page');
        }

        function enqueue_scripts()
        {
            if ((is_archive() && get_post_type() === 'hortadav_event') || has_category('hortadav_archive')) {
                wp_enqueue_script(
                    'fullcalendar-js',
                    'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js',
                    array('jquery'),
                    '5.11.3',
                    true
                );
                wp_enqueue_script(
                    'fullcalendar-ca',
                    'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/ca.js',
                    array('fullcalendar-js'),
                    '5.11.3',
                    true
                );
                wp_enqueue_style(
                    'fullcalendar-css',
                    'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css',
                    array(),
                    '5.11.3',
                    'all'
                );
                /* wp_enqueue_script( */
                /*     'uuid-js', */
                /*     // 'https://cdn.jsdelivr.net/npm/uuid@9.0.0/dist/index.min.js', */
                /*     'https://cdn.jsdelivr.net/npm/uuid@9.0.0/dist/native-browser.js', */
                /*     array(), */
                /*     '9.0.0', */
                /*     true */
                /* ); */

                wp_enqueue_script(
                    'hortadav_calendar_js',
                    plugin_dir_url(__FILE__) . 'js/calendar.js',
                    array('fullcalendar-js'),
                    '1.0.0',
                    true
                );
                wp_enqueue_style(
                    'hortadav_calendar_css',
                    plugin_dir_url(__FILE__) . 'js/calendar.css',
                    array(),
                    '1.0.0'
                );
            }
        }

        function set_archive_template($content)
        {
            if (has_category('hortadav_archive')) {
                include WP_PLUGIN_DIR . '/' . plugin_basename(dirname(__FILE__)) . '/archive.php';
                exit;
            }
        }

        function include_archive_template($template)
        {
            if (is_archive() && get_post_type() === 'hortadav_event') {
                return WP_PLUGIN_DIR . '/' . plugin_basename(dirname(__FILE__)) . '/archive.php';
            }

            return $template;
        }
    }


    new HortaDAV();
}
