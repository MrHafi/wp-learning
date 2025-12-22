<?php
// SHORTCODE (HTML ONLY)
add_shortcode('search_boiler_api', function () {

  return '
    <div class="mb-3">
      <input type="text" id="boiler-search" class="form-control" placeholder="Search boiler">
    </div>
    <div id="search-results"></div>
  ';
});


// AJAX HANDLER (GLOBAL)
function boiler_search() {

  $keywords = strtolower($_GET['data'] ?? ''); // USER KEYWORDS

  $response = wp_remote_get(
    'https://boilers.reliantenergysolutions.co.uk/api/boilers',
    ['timeout' => 15, 'sslverify' => false]
  );

  if (is_wp_error($response)) {
    wp_send_json([]);
  }

  $json = json_decode(wp_remote_retrieve_body($response), true);
  $items = $json['data']['data'] ?? [];

  $results = [];

  foreach ($items as $item) {
    if (
        !empty($item['name']) &&
        stripos($item['name'], $keywords) !== false //strpos : MATCHING BOTH KEYWORDS & ITEMS
    ) {
        $results[] = ['name' => $item['name']];
    }
}


  wp_send_json($results);
}
?>

<?php 
add_action('wp_ajax_boiler_search', 'boiler_search');
add_action('wp_ajax_nopriv_boiler_search', 'boiler_search');


