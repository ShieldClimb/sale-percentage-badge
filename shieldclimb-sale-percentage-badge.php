<?php

/**
 * Plugin Name: ShieldClimb – Sale Percentage Badge for WooCommerce
 * Plugin URI: https://shieldclimb.com/free-woocommerce-plugins/sale-percentage-badge/
 * Description: Add sale percentage badges to your WooCommerce store. Increase sales and conversions by showcasing clear discount percentages on products.
 * Version: 1.0.1
 * Requires Plugins: woocommerce
 * Requires at least: 5.8
 * Tested up to: 6.7
 * WC requires at least: 5.8
 * WC tested up to: 9.7.1
 * Requires PHP: 7.2
 * Author: shieldclimb.com
 * Author URI: https://shieldclimb.com/about-us/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

add_filter( 'woocommerce_sale_flash', 'shieldclimb_sale_percentage_badge', 20, 3 );
function shieldclimb_sale_percentage_badge( $html, $post, $product ) {

  if( $product->is_type('variable')){
      $percentages = array();

      // Get all variation prices
      $prices = $product->get_variation_prices();

      // Loop through variation prices
      foreach( $prices['price'] as $key => $price ){
          // Only on sale variations
          if( $prices['regular_price'][$key] !== $price ){
              // Calculate and set in the array the percentage for each variation on sale
              $percentages[] = round( 100 - ( floatval($prices['sale_price'][$key]) / floatval($prices['regular_price'][$key]) * 100 ) );
          }
      }
      // We keep the highest value
      $percentage = max($percentages) . '% OFF';

  } elseif( $product->is_type('grouped') ){
      $percentages = array();

      // Get all variation prices
      $children_ids = $product->get_children();

      // Loop through variation prices
      foreach( $children_ids as $child_id ){
          $child_product = wc_get_product($child_id);

          $regular_price = (float) $child_product->get_regular_price();
          $sale_price    = (float) $child_product->get_sale_price();

          if ( $sale_price != 0 || ! empty($sale_price) ) {
              // Calculate and set in the array the percentage for each child on sale
              $percentages[] = round(100 - ($sale_price / $regular_price * 100));
          }
      }
      // We keep the highest value
      $percentage = max($percentages) . '% OFF';

  } else {
      $regular_price = (float) $product->get_regular_price();
      $sale_price    = (float) $product->get_sale_price();

      if ( $sale_price != 0 || ! empty($sale_price) ) {
          $percentage    = round(100 - ($sale_price / $regular_price * 100)) . '% OFF';
      } else {
          return $html;
      }
  }
  return '<span class="onsale">' . esc_html__( 'SALE', 'shieldclimb-sale-percentage-badge' ) . ' ' . $percentage . '</span>';
}

function shieldclimb_sale_badge_load_textdomain() {
    load_plugin_textdomain( 'shieldclimb-sale-percentage-badge', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'shieldclimb_sale_badge_load_textdomain' );

?>