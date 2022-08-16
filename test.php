<?php

$data_path = realpath(__DIR__ . '/../hortadav/data/calendar.json');
$data = json_decode(file_get_contents($data_path), true);

foreach ($data['VCALENDAR'][0]['VEVENT'] as $event) {
    echo print_r($event);
}
