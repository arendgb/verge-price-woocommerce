<?php
/*
	Plugin Name: Verge Price
	Version: 1.1
	Description: Get the current price of XVG and display all the products according to correct XVG price instead of USD.
	Author: @arendgb
	Author URI: https://github.com/arendgb
	Plugin URI: https://github.com/arendgb/verge-price-woocommerc
*/

/**
 *  Make sure the plugin is accessed through the appropriate channels
 */
defined( 'ABSPATH' ) || die;

/**
 * The current version of the Plugin.
 */
define( 'OSCP', '1.0.1' );

// Plugin URL
define( 'OSCP_URL', plugin_dir_url( __FILE__ ) );

function sww_remove_wc_currency_symbols( $currency_symbol, $currency ) {
     $currency_symbol = '';
     return $currency_symbol;
}
add_filter('woocommerce_currency_symbol', 'sww_remove_wc_currency_symbols', 10, 2);


function sv_change_product_price_display( $price ) {
    $price .= 'XVG';
    return $price;
}
add_filter( 'woocommerce_get_price_html', 'sv_change_product_price_display' );
add_filter( 'woocommerce_cart_item_price', 'sv_change_product_price_display' );

function verge_usd_converter(){
    $api_return = wp_remote_get( 'https://min-api.cryptocompare.com/data/price?fsym=XVG&tsyms=USD' );
    $price = str_replace("{", "", $api_return['body']);
    $price = str_replace("}", "", $api_return['body']);
    $price_explode = explode(":", $price);
    $final_price = $price_explode[1]; // this is the final price
    return $final_price;
}

function verge_custom_price($price, $product) {
    global $post, $woocommerce;
    $post_id = $post->ID;
    $post_id = $product->id;
    $converted_price = verge_usd_converter();
    $new_price = $price / $converted_price;
    return $new_price;
}
add_filter('woocommerce_get_price', 'verge_custom_price', $product, 2);

function XVG_currency( $currencies ) {
     $currencies['XVG'] = __( 'XVG', 'woocommerce' );
     return $currencies;
}
add_filter( 'woocommerce_currencies', 'XVG_currency' );

function XVG_currency_symbol( $currency_symbol, $currency ) {
     switch( $currency ) {
          case 'XVG': $currency_symbol = ' XVG'; break;
     }
     return $currency_symbol;
}
add_filter('woocommerce_currency_symbol', 'XVG_currency_symbol', 10, 2);
?>