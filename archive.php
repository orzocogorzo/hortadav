<?php

/**
 * Template Name: HortaDAV Calendar
 *
 * @package HortaDAV
 */

get_header();
?>

<main class="site-content">
    <?php
    if (is_archive()) : ?>
        <header class="page-header">
        <h1 class="page-title"><?php echo __('Calendari de l\'Hort', 'hortadav'); ?></h1>
        </header>
    <?php endif; ?>
    <p><?php echo __("Benvinguda al calendari de cultius de l'hort. Aquí podràs trobar informació d'algunes de les plantes que pots
tenir plantades al teu hort. <b>Utilitza el selector per visualitzar aquelles plantes que vulguis conèixer</b>.
Un cop tinguis seleccionades totes les que t'interessin, navegant pel calendari podràs consultar quines són
les èpoques de plantació, floració i collita de cada espècie. Finalment, fent clic al botó <b>\"Descarregar\"</b>,
podràs obtenir un arxiu per importar a l'aplicació de calendari que desitgis i endur-te aquesta informació
al teu calendari personal.", 'hortadav'); ?></p>
    <div id="calendar"></div>
</main>

<?php
$args = array(
    'post_type' => 'hortadav_event',
    'posts_per_page' => -1
);

$posts = get_posts($args);
$data = array();

foreach ($posts as $post) {
    $terms = wp_get_post_terms(
        $post->ID,
        array(
            'family',
            'category',
            'lifecycle',
            'seeding',
            'germination',
            'frame',
            'location'
        )
    );

    $event = array(
        'title' => $post->post_title,
        'description' => $post->post_content,
    );

    foreach ($terms as $term) {
        $event[$term->taxonomy] = $term->name;
    }

    $current_year = date('Y');
    $dates = array();
    $meta = get_post_meta($post->ID);
    foreach ($meta as $key => $value) {
        if ($key == 'startdate') {
            $dates['start'] = $current_year . substr($value[0], 4);
        } elseif ($key == 'enddate') {
            $dates['end'] = $current_year . substr($value[0], 4);
        } else {
            $event['plant'] = $value[0];
        }
    }
    $event['dates'] = $dates;

    array_push($data, $event);
}
?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendar = new HortadavCalendar({
            el: 'calendar',
            data: <?= json_encode($data) ?>
        });
    });
</script>
<?php
get_footer();
?>
