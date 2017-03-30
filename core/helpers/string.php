<?php

/**
 * toCamel
 *
 * convert string to camel case style base on string glue
 *
 * @param string $str raw string
 * @param string $glue separator string
 * @return string
 */
if ( ! function_exists( 'toCamel' ) )
{
	function toCamel( $str, $glue = '_' ) 
	{
		// Split string in words.
		$words = explode($glue, strtolower($str));

		$return = '';

		foreach ($words as $word) 
			$return .= ucfirst(trim($word));

		return $return;
	}
}