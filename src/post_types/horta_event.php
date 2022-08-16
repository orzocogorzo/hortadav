<?php
if (!function_exists('hortadav_register_event')) {
    function hortadav_register_event()
    {
        $labels = array(
            'name'               => _x('HortaDAV Esdeveniments', 'post type general name'),
            'singular_name'      => _x('HortaDAV Esdeveniment', 'post type singular name'),
            'menu_name'          => 'HortaDAV'
        );

        $args = array(
            'labels'        => $labels,
            'description'   => 'Esdeveniment del calendari de llaura i sembra',
            'public'        => true,
            'menu_position' => 5,
            'supports'      => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
            'has_archive'   => true,
        );

        register_post_type('hortadav_event', $args);

        /*
        $labels = array(
            'name'              => _x('Esdeveniments', 'taxonomy general name'),
            'singular_name'     => _x('Esdeveniment', 'taxonomy singular name'),
        );
        $args   = array(
            'hierarchical'      => false, // make it hierarchical (like categories)
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'event'],
        );
        register_taxonomy('event', ['hortadav_event'], $args);
         */

        $labels = array(
            'name'              => _x('Families', 'taxonomy general name'),
            'singular_name'     => _x('Familia', 'taxonomy singular name'),
        );
        $args   = array(
            'hierarchical'      => false, // make it hierarchical (like categories)
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'family'],
        );
        register_taxonomy('family', ['hortadav_event'], $args);

        $labels = array(
            'name'              => _x('Categories', 'taxonomy general name'),
            'singular_name'     => _x('Categoria', 'taxonomy singular name'),
        );
        $args   = array(
            'hierarchical'      => false, // make it hierarchical (like categories)
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'category'],
        );
        register_taxonomy('category', ['hortadav_event'], $args);

        $labels = array(
            'name'              => _x('Profunditats de sembra', 'taxonomy general name'),
            'singular_name'     => _x('Profunditat de sembra', 'taxonomy singular name'),
        );
        $args   = array(
            'hierarchical'      => false, // make it hierarchical (like categories)
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'seeding'],
        );
        register_taxonomy('seeding', ['hortadav_event'], $args);

        $labels = array(
            'name'              => _x('Temps de germinació o brotació', 'taxonomy general name'),
            'singular_name'     => _x('Temps de germinació o brotació', 'taxonomy singular name'),
        );
        $args   = array(
            'hierarchical'      => false, // make it hierarchical (like categories)
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'germination'],
        );
        register_taxonomy('germination', ['hortadav_event'], $args);

        $labels = array(
            'name'              => _x('Durades fins a recol·lecció', 'taxonomy general name'),
            'singular_name'     => _x('Durada fins a recol·lecció', 'taxonomy singular name'),
        );
        $args   = array(
            'hierarchical'      => false, // make it hierarchical (like categories)
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'lifecycle'],
        );
        register_taxonomy('lifecycle', ['hortadav_event'], $args);

        $labels = array(
            'name'              => _x('Marcs de plantació', 'taxonomy general name'),
            'singular_name'     => _x('Marc de plantació', 'taxonomy singular name'),
        );
        $args   = array(
            'hierarchical'      => false, // make it hierarchical (like categories)
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'frame'],
        );
        register_taxonomy('frame', ['hortadav_event'], $args);

        $labels = array(
            'name'              => _x('Localitzacions', 'taxonomy general name'),
            'singular_name'     => _x('Localització', 'taxonomy singular name'),
        );
        $args   = array(
            'hierarchical'      => false, // make it hierarchical (like categories)
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'location'],
        );
        register_taxonomy('location', ['hortadav_event'], $args);
    }
}

if (!function_exists('hortadav_unregister_event')) {
    function hortadav_unregister_event()
    {
        unregister_post_type('hortadav_event');
        // unregister_taxonomy('event');
        unregister_taxonomy('family');
        unregister_taxonomy('category');
        unregister_taxonomy('seeding');
        unregister_taxonomy('lifecycle');
        unregister_taxonomy('germination');
        unregister_taxonomy('frame');
        unregister_taxonomy('location');
    }
}

if (!function_exists('hortadav_register_archive')) {
    function hortadav_register_archive()
    {
        register_taxonomy_for_object_type('category', 'page');
        wp_insert_term('hortadav_archive', 'category');
    }
}

if (!function_exists('hortadav_unregister_archive')) {
    function hortadav_unregister_archive()
    {
        wp_delete_category('hortadav_archive');
    }
}

if (!function_exists('hortadav_create_event')) {
    function hortadav_create_event($data, $taxonomies, $meta)
    {
        $data['post_type'] = 'hortadav_event';
        $data['post_status'] = 'publish';
        $post_id = wp_insert_post($data);

        foreach ($taxonomies as $taxonomy => $term) {
            try {
                wp_insert_term($term, $taxonomy);
            } catch (exception $e) {
                // DO NOTHING
            }

            wp_set_object_terms($post_id, $term, $taxonomy);
        }

        add_post_meta($post_id, "startdate", $meta['startdate'], true);
        add_post_meta($post_id, "enddate", $meta['enddate'], true);
        add_post_meta($post_id, "plant", $meta['plant'], true);
    }
}

if (!function_exists('hortadav_delete_events')) {
    function hortadav_delete_events()
    {
        $posts = get_posts(array(
            'numberposts' => -1,
            'post_type' => 'hortadav_event',
            'post_status' => 'any'
        ));

        foreach ($posts as $post) {
            wp_delete_post($post->ID, true);
        }
    }
}

if (!function_exists('hortadav_create_calendar')) {
    function hortadav_create_archive()
    {
        $args = array(
            'post_title'    => 'Calendari de l\'Horta',
            'post_content'  => 'Calendari',
            'post_status'   => 'publish',
            'post_type'     => 'page',
        );
        $post_id = wp_insert_post($args);

        wp_set_object_terms($post_id, 'hortadav_archive', 'category');
    }
}

if (!function_exists('hortadav_delete_archive')) {
    function hortadav_delete_archive()
    {
        $args = array(
            'post_type' => 'page',
            'tax_query' => array(
                array(
                    'taxonomy' => 'category',
                    'field' => 'name',
                    'terms' => 'hortadav_archive'
                ),
            )
        );
        $query = new WP_Query($args);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                wp_delete_post(get_the_ID(), true);
            }
        }
    }
}
