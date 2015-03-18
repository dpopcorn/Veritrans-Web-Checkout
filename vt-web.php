<?php 
/*
Plugin Name: Veritrans Web Checkout
Plugin URI: http://dpopcorn.net/
Description: Get an easy checkout with Veritrans Payment Web Checkout.
Version: 1.0.0
Author: dpopcorn
Author URI: http://dpopcorn.net/
License: GPLv2 or later
*/

require_once(dirname(__FILE__) . '/options.php');

new VT_Web();

class VT_Web {

	function __construct()
	{
		add_filter( 'template_include', array(&$this, 'template_include'), 1 );
	}

	function template_include( $template_path ) {
		/* Custom Handler */
		
	    if( isset( $_GET['vt_web'] )) {
			require_once(dirname(__FILE__) . '/Veritrans.php');

			$id = $_GET['vt_id'];
			$name = $_GET['vt_name'];
			$price = $_GET['vt_price'];
			$qty = $_GET['vt_qty'];

			if( is_array( $name ) ){
				for( $i=0; $i<count($name); $i++){
						
					if( $name[$i] && $price[$i] ){
						$item_details[] = array(
							'name' => $name[$i],
							'price' => $price[$i],
							'id' => $id[$i] ? $id[$i] : ($i + 1),
							'quantity' => $qty[$i] ? $qty[$i] : 1,
						);
					}

				}
			} else {
				$item_details[] = array(
					'name' => $name,
					'price' => is_array( $price ) ? end($price) : $price,
					'id' => is_array( $id ) ? end($id) : 1,
					'quantity' => is_array( $qty ) ? end($qty) : 1,
				);
			}

			$total = 0;

			if( $item_details ) foreach ($item_details as $item) {
				$total = $total + ($item['quantity'] * $item['price']);
			}

			$options = get_option('vt_web_options');

			if( !$options ) {
				echo "Please setting your Veritrans Web Option.";
				return;
			}

			Veritrans_Config::$serverKey = $options['server_key'];

			if( !$options['sandbox_mode'] )
				Veritrans_Config::$isProduction = true;

			// Uncomment for production environment
			// Veritrans_Config::$isProduction = true;

			// Uncomment to enable sanitization
			// Veritrans_Config::$isSanitized = true;

			// Uncomment to enable 3D-Secure
			// Veritrans_Config::$is3ds = true;

			$params = array(
				'transaction_details' => array(
					'order_id' => rand(),
					'gross_amount' => $total,
				),
				'item_details' => $item_details,
			);

			try {
				// Redirect to Veritrans VTWeb page
				header('Location: ' . Veritrans_Vtweb::getRedirectionUrl($params));
			}
			catch (Exception $e) {
				echo $e->getMessage();
			}

			return;
		}
		return $template_path;
	}

}