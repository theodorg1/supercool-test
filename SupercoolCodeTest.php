<?php

$types = array("events", "instances", "venues");
foreach ($types as $type) {
    $url = 'https://supercooldesign.co.uk/api/technical-test/' . $type;
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $result = curl_exec($ch);
    curl_close($ch);
    ${$type . 'Array'} = json_decode($result, true);
}

$newEvents = [];
$finalList = [];
function date_compare($a, $b)
{
    $t1 = strtotime($a['start']);
    $t2 = strtotime($b['start']);
    return $t1 - $t2;
}
usort($instancesArray, 'date_compare');

foreach ($eventsArray as $event) {
    foreach ($instancesArray as $instance) {
        if ($event['id'] == $instance['event']['id']) {
            $event['instances'][] = $instance;
            $event['venue_id'] = $instance['venue']['id'];
        }
    }
    array_push($newEvents, $event);
}

foreach ($venuesArray as $venue) {
    echo '<li>' . $venue['title'];
    echo '<ul>';
    foreach ($newEvents as $event) {
        if ($event['venue_id'] == $venue['id']) {
            echo '<li>';
            echo $event['title'] . '<br>';
            echo 'id: ' . filter_var($event['id'], FILTER_SANITIZE_NUMBER_INT) . '<br>';
            echo 'First instance: ' . $event['instances'][0]['start'] . '<br>';
            echo 'Next instance: ' . $event['instances'][1]['start'] . '<br>';
            echo 'Instance count: ' . count($event['instances']) . '<br>';
        }
    }
    echo '</ul>';
    echo '</li>';
}
