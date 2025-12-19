<?php 

add_shortcode('get_boiler_api_sidebar','get_boiler_api_sidebar');

function get_boiler_api_sidebar() {

    ob_start();

    $url = 'https://boilers.reliantenergysolutions.co.uk/api/boilers?per_page=3'; // FETCH SINGLE PAGE ONLY

    $res = wp_remote_get($url, [
        'timeout' => 15,
        'sslverify' => false // LEARNING ONLY
    ]);

    if (is_wp_error($res)) return $res->get_error_message(); // ERROR CHECK

    $body = json_decode(wp_remote_retrieve_body($res), true);

    $items = $body['data']['data'] ?? []; // GET ITEMS FROM SINGLE PAGE

    if (empty($items)) return 'No data';

   // LOOP - full boiler data
foreach ($items as $item) {

    echo '<pre>';
    print_r($item); // FULL BOILER DATA
    echo '</pre>';

    $extra = json_decode($item['extra_data'] ?? '', true); // DECODE EXTRA DATA

    if (empty($extra)) continue;

    echo '<ul>';
    foreach ($extra as $key => $value) {
        echo '<li><strong>' . esc_html($key) . ':</strong> ' . esc_html($value) . '</li>';
    }
    echo '</ul>';
}


    return ob_get_clean();
}
?>
