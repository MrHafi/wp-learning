<?php
add_shortcode( 'get_boiler_api', 'get_boiler_api' );

function get_boiler_api() {

  ob_start();
?>

<!-- FRONTENDD COPARE CONTAINER -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div id="compare" class="container my-4">
  <div class="row g-3 align-items-stretch">
    
    <div class="col-md-6">
      <div id="box1" class="border h-100 p-4 text-center">
        <h5>Option A</h5>
        <p>Empty</p>
      </div>
    </div>

    <div class="col-md-6">
      <div id="box2" class="border h-100 p-4 text-center">
        <h5>Option B</h5>
        <p>Empty</p>
      </div>
    </div>

  </div>



<?php
  $url = 'https://boilers.reliantenergysolutions.co.uk/api/boilers';

  $args = [
    'method'    => 'GET',
    'timeout'   => 15,
    'sslverify' => false, // learning only
  ];

  $response = wp_remote_get( $url, $args );

  // ERROR CHECK
  if ( is_wp_error( $response ) ) { 
    return $response->get_error_message(); 
  }

  $body = wp_remote_retrieve_body( $response );
  $data = json_decode( $body, true );

  ///////////////// FRONTEDN ////////////////////

  $items = $data['data']['data'] ?? [];  // 2D ARRAY
  if ( empty($items) ) return 'No data';
?>

<div class="list-group">
<?php foreach ( $items as $item ) :

  // EXTRA DETAILS nested json (needs to decode)
  $extra = json_decode($item['extra_data'] ?? '', true); ///DECODING EXTRA DETAILS
  if ( ! is_array($extra) ) $extra = [];
?>
  <div class="list-group-item">
    <div class="fw-bold"><?php echo esc_html($item['name'] ?? ''); ?></div>

   <div class="small mb-2">
  Fuel: <?php echo esc_html($item['fuel'] ?? '-'); ?> |
  Type: <?php echo esc_html($item['main_type'] ?? '-'); ?>
</div>


    <button onclick='addCompare(<?php echo json_encode($item); ?>)'> <!-- SENDING FUILL ITEM WITH  BUTTON  -->
      Add to compare
    </button>
  </div>
<?php endforeach; ?>
</div>
<script>
 let box1 = document.getElementById('box1'); // first box
let box2 = document.getElementById('box2'); // second box

let c = []; 

function addCompare(item) { // NEW CLICK FOR COMPARE
  if (c.length === 2) c.shift(); // keep 2 only
  c.push(item); 
  show();  
}

function show() { // show data
  box1.innerHTML = c[0] ? show_in_container(c[0]) : 'Empty';
  box2.innerHTML = c[1] ? show_in_container(c[1]) : 'Empty';
}

// HTML FOR SENDING SHOWING DATA IN COMPARE CONTAINER
function show_in_container(item) { 
  let extra = item.extra_data ? JSON.parse(item.extra_data) : {}; // json to js obj (like decode in php)

  return `
  <div class="card mb-2 shadow-sm">
    <div class="card-body p-2">
      <h6 class="card-title mb-2">${item.name}</h6>

      <p class="small mb-1"><strong>Fuel:</strong> ${item.fuel || '-'}</p>
      <p class="small mb-1"><strong>Type:</strong> ${item.main_type || '-'}</p>
      <p class="small mb-1"><strong>Winter Eff:</strong> ${item.s_a_p_winter_seasonal_efficiency || '-'}</p>
      <p class="small mb-1"><strong>Summer Eff:</strong> ${item.s_a_p_summer_seasonal_efficiency || '-'}</p>

      <hr class="my-2">

      <p class="small mb-1"><strong>Brand:</strong> ${extra.brand || '-'}</p>
      <p class="small mb-1"><strong>Status:</strong> ${extra.status || '-'}</p>
      <p class="small mb-1"><strong>Category:</strong> ${extra.efficiency_category || '-'}</p>
      <p class="small mb-1"><strong>Manufacturer:</strong> ${extra.original_manufacturer_name || '-'}</p>
      <p class="small mb-0"><strong>Final Year:</strong> ${extra.final_year_of_manufacture || '-'}</p>
    </div>
  </div>
`;

}


show();
</script>

<?php
  return ob_get_clean();
}