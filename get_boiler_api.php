<?php
add_shortcode( 'get_boiler_api', 'get_boiler_api' );

function get_boiler_api() {

  $url = 'https://boilers.reliantenergysolutions.co.uk/api/boilers';

  $args = [
    'method'    => 'GET',
    'timeout'   => 15,
    'sslverify' => false, // learning only api lsot the SSL so got blocked 
  ];

  $response = wp_remote_get( $url, $args );
  //ERROR
  if ( is_wp_error( $response ) ) {
    return $response->get_error_message();
  }

  $body = wp_remote_retrieve_body( $response );

$data = json_decode( $body, true );


$html = '<pre style="overflow:auto;">';
$html .= print_r( $data, true );
$html .= '</pre>';

return $html;

}
