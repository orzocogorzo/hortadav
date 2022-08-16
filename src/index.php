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

            add_action('init', array(&$this, 'register'));

            // add_action('archive_template', array(&$this, 'event_archive'));
            // add_action('page_template', array(&$this, 'archive_template'));
            add_filter('the_content', array(&$this, 'archive_template'));

            if (!is_admin()) {
                add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));
            }
        }

        static function activate()
        {
            require_once __DIR__ . '/post_types/horta_event.php';

            $data_path = plugin_dir_url(__FILE__) . 'data/calendar.json';
            $data = json_decode(file_get_contents($data_path), true);

            foreach ($data['VCALENDAR'][0]['VEVENT'] as $event) {
                hortadav_create_event(array(
                    'post_title' => $event['SUMMARY'],
                    'post_content' => $event['DESCRIPTION'],
                ), array(
                    # 'event' => $event['TAXONOMIES']['EVENT'],
                    'family' => $event['TAXONOMIES']['FAMILY'],
                    'category' => $event['TAXONOMIES']['CATEGORY'],
                    'lifecycle' => $event['TAXONOMIES']['LIFECYCLE'],
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

            hortadav_create_archive();
        }

        static function deactivate()
        {
            hortadav_delete_events();
            hortadav_unregister_event();
            hortadav_delete_archive();
            hortadav_unregister_archive();
        }

        static function uninstall()
        {
        }

        function register()
        {
            require_once __DIR__ . '/post_types/horta_event.php';
            hortadav_register_event();
            hortadav_register_archive();
        }

        function enqueue_scripts()
        {
            wp_enqueue_script(
                'hortadav_calendar_js',
                // plugin_dir_url(__FILE__) . 'public/js/calendarJS/index.js',
                'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.2/main.min.js',
                array('jquery'),
                '5.11.2',
                true
            );
            wp_enqueue_style(
                'hortadav_calendar_css',
                // plugin_dir_url(__FILE__) . 'public/css/calendarJS/index.css',
                'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.2/main.min.css',
                array(),
                '5.11.2',
                'all'
            );
        }

        function archive_template()
        {
            global $post;

            if (has_category('hortadav_archive')) {
                include __DIR__ . '/includes/calendar-content.php';
            }
        }
    }


    new HortaDAV();
}
