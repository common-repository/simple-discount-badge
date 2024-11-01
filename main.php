<?php
/*
Plugin Name: Simple Discount Badge
Plugin URI: https://handyshout.com/simple-discount-badge
Description: Add a simple discount badge to woocommerce powered website.
Author name : Satnam Singh
Version: 1.0.1
Author URI: https://profiles.wordpress.org/satnam9/ 

WC requires at least: 2.2
WC tested up to: 5.2.2

*/

//import settings
include(plugin_dir_path(__FILE__) . 'settings.php');
$options = get_option( 'sdb_setting_page_settings' );

// Plugin action link to Settings page
if ( ! function_exists('simple_discount_badge_action_links') ) {
   function simple_discount_badge_action_links( $links ) {
   
       $settings_link = '<a href="options-general.php?page=simple_discount_badge">' .
           esc_html( __('Settings', 'simple_discount_badge' ) ) . '</a>';
   
       return array_merge( array( $settings_link), $links );
       
   }
   add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'simple_discount_badge_action_links' );
   }
 
//intial setup

if ($options == false) {
	register_activation_hook(__FILE__, 'sdb_setthedefaults');
	function sdb_setthedefaults()
	{
		$defaults = array(
			'sdb_setting_page_default_badge' => '1',
			'sdb_setting_page_productpage_badge' => '1',
			'sdb_setting_page_text_afternumber' => '% off',
			'sdb_setting_page_background' => '#D9534F',
			'sdb_setting_page_text_color' => '#ffffff'
		);
		$setdefault = wp_parse_args(update_option('sdb_setting_page_settings', $defaults), $defaults);
	}
}

//show badge
function sdb_show_sale_percentage_all() {
   global $product;
   if ( ! $product->is_on_sale() ) return;
   if ( $product->is_type( 'simple' ) ) {
      $max_percentage = ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100;
   } elseif ( $product->is_type( 'variable' ) ) {
      $max_percentage = 0;
      foreach ( $product->get_children() as $child_id ) {
         $variation = wc_get_product( $child_id );
         $price = $variation->get_regular_price();
         $sale = $variation->get_sale_price();
         if ( $price != 0 && ! empty( $sale ) ) $percentage = ( $price - $sale ) / $price * 100;
         if ( $percentage > $max_percentage ) {
            $max_percentage = $percentage;
         }
      }
   }
   if ( $max_percentage > 0 ) {
      $options = get_option( 'sdb_setting_page_settings' );
$background=$options['sdb_setting_page_background'];
$textcolor=$options['sdb_setting_page_text_color'];
$text=$options['sdb_setting_page_text_afternumber'];

//style
echo "<style>
.percentagebadge {
    background:$background;
    display: inline-block;
    margin-bottom:.5em ;
    padding: .2em .6em .3em;
    font-size: .8em;
    font-weight: bold;
    color:$textcolor;
    text-align: center;
    border-radius: .2em;
    }
    </style>";

    //display
   echo "<div class='percentagebadge'>".round($max_percentage)."$text</div>"; 
   }
}
add_action( 'woocommerce_before_shop_loop_item_title', 'sdb_show_sale_percentage_all', 25 );

//hide sale badge
$hidedafaultsalebadge =$options['sdb_setting_page_default_badge'];
if($hidedafaultsalebadge==='1')
{
add_filter('woocommerce_sale_flash', 'sdb_hide_sale_flash');
function sdb_hide_sale_flash()
{
return false;
}
}

//product page badge
function sdb_product_page_badge() {
global $product;
   if( $product->is_type('simple') || $product->is_type('external') || $product->is_type('grouped') ) {
   $regular_price = get_post_meta( $product->get_id(), '_regular_price', true );
   $sale_price = get_post_meta( $product->get_id(), '_sale_price', true );
   if( !empty($sale_price) ) {
   $amount_saved = $regular_price - $sale_price;
   $currency_symbol = get_woocommerce_currency_symbol();
   $percentage = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
   $options = get_option( 'sdb_setting_page_settings' );
$background=$options['sdb_setting_page_background'];

//stying
echo "<style>
.prodpage {
    color:$background;
    display: inline-block;
    font-size: 1em;
    font-weight: bold;
    }
    </style>";

//displaying 
 echo '<p class="prodpage"><b>You Save: $'.number_format($amount_saved,2,".", ""). ' (' .number_format($percentage,0,"","").'%)</b></p>';
   }
   }
}


//show/hide badge on plugin page
$showonproductpage =$options['sdb_setting_page_productpage_badge'];
   if($showonproductpage==='1')
   { 
      add_action( 'woocommerce_single_product_summary', 'sdb_product_page_badge', 11 ); 
}
?>