<?php

if (!function_exists('hortadav_register_event')) {
    function hortadav_register_event()
    {
        $labels = array(
            'name'               => __('Calendari de l\'hort', 'hortadav'),
            'singular_name'      => __('Esdeveniment de l\'hort', 'hortadav'),
            'menu_name'          => __('Calendari de l\'hort', 'hortadav'),
        );

        $args = array(
            'labels'        => $labels,
            'description'   => __('Esdeveniment del calendari de llaura i sembra', 'hortadav'),
            'public'        => true,
            'menu_position' => 5,
            'supports'      => array('title', 'autor', 'editor', 'custom-fields'),
            'has_archive'   => true,
        );

        register_post_type('hortadav_event', $args);

        $labels = array(
            'name'              => __('Famílies', 'hortadav'),
            'singular_name'     => __('Familia', 'hortadav'),
        );
        $args   = array(
            /* 'hierarchical'      => false, // make it hierarchical (like categories) */
            'public'            => true,
            'labels'            => $labels,
            /* 'show_ui'           => true, // inherit from public */
            'show_admin_column' => true,
            'query_var'         => true,
            // 'rewrite'           => ['slug' => 'family'],
        );
        register_taxonomy('family', ['hortadav_event'], $args);

        $labels = array(
            'name'              => __('Cicles de vida', 'hortadav'),
            'singular_name'     => __('Cicle de vida', 'hortadav'),
        );
        $args   = array(
            /* 'hierarchical'      => false, // make it hierarchical (like categories) */
            'public'            => true,
            'labels'            => $labels,
            /* 'show_ui'           => true, // inherit from public */
            'show_admin_column' => true,
            'query_var'         => true,
            // 'rewrite'           => ['slug' => 'lifecycle'],
        );
        register_taxonomy('lifecycle', ['hortadav_event'], $args);

        $labels = array(
            'name'              => __('Profunditats de sembra', 'hortadav'),
            'singular_name'     => __('Profunditat de sembra', 'hortadav'),
        );
        $args   = array(
            /* 'hierarchical'      => false, // make it hierarchical (like categories) */
            'public'            => true,
            'labels'            => $labels,
            /* 'show_ui'           => true, // inherit from public */
            'show_admin_column' => true,
            'query_var'         => true,
            // 'rewrite'           => ['slug' => 'seeding'],
        );
        register_taxonomy('seeding', ['hortadav_event'], $args);

        $labels = array(
            'name'              => __('Temps de germinació o brotació', 'hortadav'),
            'singular_name'     => __('Temps de germinació o brotació', 'hortadav'),
        );
        $args   = array(
            /* 'hierarchical'      => false, // make it hierarchical (like categories) */
            'public'            => true,
            'labels'            => $labels,
            /* 'show_ui'           => true, // inherit from public */
            'show_admin_column' => true,
            'query_var'         => true,
            // 'rewrite'           => ['slug' => 'germination'],
        );
        register_taxonomy('germination', ['hortadav_event'], $args);

        $labels = array(
            'name'              => __('Durades fins a recol·lecció', 'hortadav'),
            'singular_name'     => __('Durada fins a recol·lecció', 'hortadav'),
        );
        $args   = array(
            /* 'hierarchical'      => false, // make it hierarchical (like categories) */
            'public'            => true,
            'labels'            => $labels,
            /* 'show_ui'           => true, // inherit from public */
            'show_admin_column' => true,
            'query_var'         => true,
            // 'rewrite'           => ['slug' => 'harvest_time'],
        );
        register_taxonomy('harvest_time', ['hortadav_event'], $args);

        $labels = array(
            'name'              => __('Marcs de plantació', 'hortadav'),
            'singular_name'     => __('Marc de plantació', 'hortadav'),
        );
        $args   = array(
            /* 'hierarchical'      => false, // make it hierarchical (like categories) */
            'public'            => true,
            'labels'            => $labels,
            /* 'show_ui'           => true, // inherit from public */
            'show_admin_column' => true,
            'query_var'         => true,
            // 'rewrite'           => ['slug' => 'frame'],
        );
        register_taxonomy('frame', ['hortadav_event'], $args);

        $labels = array(
            'name'              => __('Localitzacions', 'hortadav'),
            'singular_name'     => __('Localització', 'hortadav'),
        );
        $args   = array(
            /* 'hierarchical'      => false, // make it hierarchical (like categories) */
            'public'            => true,
            'labels'            => $labels,
            /* 'show_ui'           => true, // inherit from public */
            'show_admin_column' => true,
            'query_var'         => true,
            // 'rewrite'           => ['slug' => 'location'],
        );
        register_taxonomy('location', ['hortadav_event'], $args);
    }
}

if (!function_exists('hortadav_unregister_event')) {
    function hortadav_unregister_event()
    {
        unregister_post_type('hortadav_event');
        // unregister_taxonomy('event');
        hortadav_unregister_terms('family');
        unregister_taxonomy('family');
        hortadav_unregister_terms('lifecycle');
        unregister_taxonomy('lifecycle');
        hortadav_unregister_terms('seeding');
        unregister_taxonomy('seeding');
        hortadav_unregister_terms('harvest_time');
        unregister_taxonomy('harvest_time');
        hortadav_unregister_terms('germination');
        unregister_taxonomy('germination');
        hortadav_unregister_terms('frame');
        unregister_taxonomy('frame');
        hortadav_unregister_terms('location');
        unregister_taxonomy('location');
    }
}

if (!function_exists('hortadav_unregister_terms')) {
    function hortadav_unregister_terms($taxonomy)
    {
        $terms = get_terms($taxonomy);
        foreach ($terms as $term) {
            wp_delete_term($term, $taxonomy);
        }
    }
}

if (!function_exists('hortadav_create_event')) {
    function hortadav_create_event($data, $taxonomies, $meta)
    {
        $data['post_type'] = 'hortadav_event';
        $data['post_status'] = 'publish';
        $post_id = wp_insert_post($data);

        foreach ($taxonomies as $taxonomy => $term) {
            // $term = hortadav_create_term($term, $taxonomy, $post_id);
            wp_set_object_terms($post_id, $term, $taxonomy);
            unset($term);
        }

        add_post_meta($post_id, "startdate", $meta['startdate'], true);
        add_post_meta($post_id, "enddate", $meta['enddate'], true);
        add_post_meta($post_id, "plant", $meta['plant'], true);
    }
}

if (!function_exists('hortadav_register_term')) {
    function hortadav_register_term($term, $taxonomy)
    {
        if ($term == '') {
            return;
        }

        $term_or_error = wp_insert_term($term, $taxonomy);
        if (is_wp_error($term_or_error)) {
            if ($term_or_error->get_error_code() != 'term_exists') {
                wp_trigger_error(
                    'hortadav_register_term',
                    __('Error al crear el terme %s a la taxonomia %s', 'hortadav'),
                );
            }
        }

        return $term_or_error;
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
            'post_title'    => __('Calendari de l\'Hort', 'hortadav'),
            'post_content'  => __('Calendari', 'hortadav'),
            'post_status'   => 'publish',
            'post_type'     => 'page',
        );
        $post_id = wp_insert_post($args);

        register_taxonomy_for_object_type('category', 'page');
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
