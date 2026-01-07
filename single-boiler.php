<?php 

add_shortcode('boiler_detail', 'boiler_detail');
function boiler_detail() {


    
  if ( empty($_GET['id']) ) return 'No boiler selected';

  $id = intval($_GET['id']); //INTERVAL MEANS ALWASY ANUMBER

  $res = wp_remote_get(
    'https://boilers.reliantenergysolutions.co.uk/api/boilers',
    ['timeout'=>15,'sslverify'=>false]
  );

  if ( is_wp_error($res) ) return 'API error';

  $data = json_decode(wp_remote_retrieve_body($res), true);
  $items = $data['data']['data'] ?? []; //API REQUIREMENTT

  foreach ($items as $item) {
    if ($item['id'] == $id) {

      $extra = json_decode($item['extra_data'] ?? '', true);

      return "
      <div class='card p-3'>
        <h3>{$item['name']}</h3>
        <p><b>Fuel:</b> {$item['fuel']}</p>
        <p><b>Type:</b> {$item['main_type']}</p>
        <p><b>Brand:</b> ".($extra['brand'] ?? '-')."</p>
        <p><b>Efficiency:</b> ".($extra['efficiency_category'] ?? '-')."</p>
      </div>
      ";
    }
  }

  return 'Boiler not found';
}
