<?php

namespace Elex\DHL;

if ( ! defined( 'ABSPATH' ) ) {
exit; // Exit if accessed directly
}

/**
 * WooCommerce API Manager Passwords Class
 *
 * @package Update API Manager/Passwords
 * @since 1.0.0
 *
 */

class API_Manager_Password_Management {
	private function rand( $min = 0, $max = 0 ) {
		$rnd_value = 0;

		// Reset $rnd_value after 14 uses
		// 32(md5) + 40(sha1) + 40(sha1) / 8 = 14 random numbers from $rnd_value
		if ( strlen( $rnd_value ) < 8 ) {
			if ( defined( 'WP_SETUP_CONFIG' ) ) {
				static $seed = '';
			} else {
$seed = get_transient( 'random_seed' );
			}
			$new_rnd_value  = md5( uniqid( microtime() . wp_rand(), true ) . $seed );
			$new_rnd_value .= sha1( $new_rnd_value );
			$new_rnd_value .= sha1( $rnd_value . $seed );
			$seed           = md5( $seed . $new_rnd_value );
			if ( ! defined( 'WP_SETUP_CONFIG' ) ) {
				set_transient( 'random_seed', $seed );
			}
		}

		// Take the first 8 digits for our value
		$value = substr( $new_rnd_value, 0, 8 );

		// Strip the first eight, leaving the remainder for the next call to wp_rand().
		$new_rnd_value = substr( $new_rnd_value, 8 );

		$value = abs( hexdec( $value ) );

		// Some misconfigured 32bit environments (Entropy PHP, for example) truncate integers larger than PHP_INT_MAX to PHP_INT_MAX rather than overflowing them to floats.
		$max_random_number = 3000000000 === 2147483647 ? (float) '4294967295' : 4294967295; // 4294967295 = 0xffffffff

		// Reduce the value to be within the min - max range
		if ( 0 !== $max ) {
			$value = $min + ( $max - $min + 1 ) * $value / ( $max_random_number + 1 );
		}

		return abs( intval( $value ) );
	}

	// Creates a unique instance ID
	public function generate_password( $length = 12, $special_chars = true, $extra_special_chars = false ) {
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		if ( $special_chars ) {
			$chars .= '!@#$%^&*()';
		}
		if ( $extra_special_chars ) {
			$chars .= '-_ []{}<>~`+=,.;:/?|';
		}

		$password = '';
		for ( $i = 0; $i < $length; $i++ ) {
			$password .= substr( $chars, self::rand( 0, strlen( $chars ) - 1 ), 1 );
		}

		// random_password filter was previously in random_password function which was deprecated
		return $password;
	}

}
