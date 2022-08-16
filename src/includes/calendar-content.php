<?php
get_header(); ?>

<select id="catSelector" multiple></select>
<div id="calendar"></div>

<?php
$args = array(
    'post_type' => 'hortadav_event',
    'numberposts' => -1
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

    $dates = array();
    $meta = get_post_meta($post->ID);
    foreach ($meta as $key => $value) {
        if ($key == 'startdate') {
            $dates['start'] = $value[0];
        } else if ($key == 'enddate') {
            $dates['end'] = $value[0];
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
        var data = <?= json_encode($data) ?>;
        var options = Object.entries(data.reduce(function(acum, ev) {
            acum[ev.plant] = true;
            return acum;
        }, {})).map(function(e) {
            return e[0];
        });
        var events = [];
        var selection = null;

        function parseDate(str) {
            if (!str) return null;
            var year = str.slice(0, 4);
            var month = str.slice(4, 6);
            var day = str.slice(6, 8);
            return new Date(year, month - 1, day);
        }

        var selector = document.getElementById('catSelector');
        options.forEach(function(plant, i) {
            var option = document.createElement('option');
            option.value = i;
            option.innerText = plant;
            selector.appendChild(option);
        });
        selector.addEventListener('change', function(ev) {
            selection = options[ev.target.value];
            events = data.filter(function(ev) {
                return ev.plant === selection;
            });

            calendar.getEvents().forEach(function(ev) {
                ev.remove();
            });

            events.forEach(function(ev) {
                calendar.addEvent({
                    title: ev.title,
                    start: parseDate(ev.dates.start),
                    end: parseDate(ev.dates.end)
                });
            });

            calendar.render();
        });

        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: events.map(function(ev) {
                return {
                    title: ev.title,
                    start: parseDate(ev.dates.start),
                    end: parseDate(ev.dates.end)
                }
            })
        });
        calendar.render();
    });
</script>
<?php get_footer(); ?>
