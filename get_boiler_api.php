<?php
add_shortcode( 'get_boiler_api', 'get_boiler_api' );

function get_boiler_api() {

  $url = 'https://boilers.reliantenergysolutions.co.uk/api/boilers';

  $args = [
    'method'    => 'GET',
    'timeout'   => 15,
    'sslverify' => false, // learning only
  ];

  $response = wp_remote_get( $url, $args );

  if ( is_wp_error( $response ) ) {
    return $response->get_error_message();
  }

  $body = wp_remote_retrieve_body( $response );
  $data = json_decode( $body, true );

  ///////////////// FRONTEDN ////////////////////

  $items = $data['data']['data'] ?? [];  // 2D ARRAY
  if ( empty($items) ) return 'No data';

  ob_start(); ?>

  <div class="list-group">
    <?php foreach ( $items as $item ) :

      // EXTRA DETAILS ARE IN NEW TEXT FORMM
      $extra = json_decode($item['extra_data'] ?? '', true); ///DECODING EXTRA DETAILS
      if ( ! is_array($extra) ) $extra = [];
    ?>
      <div class="list-group-item">
        <div class="fw-bold"><?php echo esc_html($item['name'] ?? ''); ?></div>

        <div class="small mb-2">
          Fuel: <?php echo esc_html($item['fuel'] ?? '-'); ?> |
          Type: <?php echo esc_html($item['main_type'] ?? '-'); ?> |
          Winter: <?php echo esc_html($item['s_a_p_winter_seasonal_efficiency'] ?? '-'); ?> |
          Summer: <?php echo esc_html($item['s_a_p_summer_seasonal_efficiency'] ?? '-'); ?> |
          Brand: <?php echo esc_html($extra['brand'] ?? '-'); ?>
        </div>

        <button class="btn btn-sm btn-outline-primary compare-btn"
          data-id="<?php echo esc_attr($item['id']); ?>">
          Add to compare
        </button>
      </div>
    <?php endforeach; ?>
  </div>

  <?php
  return ob_get_clean();
}
