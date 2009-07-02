<?php

error_reporting(E_ALL && E_STRICT);


// $feed = new Pixelpost_Feed;
$feed_array = include('example_media_array.php');

// var_dump($feed);

// echo $feed->build();


echo Pixelpost_Feed::build($feed_array,'    ');

// echo($feed->xml);

class Pixelpost_Feed
{
	public static $indent;
	
	
	public static function build($feed=array(), $indent="\t")
	{
		self::$indent = $indent;
		
		return self::encode($feed);
	}
	
	private static function encode($array=array(), $level=0, $parent=false)
	{
		// Initialize Variables
		$xml    = '';
		$indent = '';
		$level++;
		
		
		if (self::$indent)
			for ($i=1; $i < $level; $i++) { $indent .= self::$indent; }
		
		foreach( $array as $key => $value )
		{
			$attributes = '';
			$key        = self::escape($key);

			// Make sure we don't create tags for attributes
			if ( substr( $key, -5) == '_attr' )
				continue;
			
			if ( self::is_sequential($value) ) {
				$xml .= self::encode($value,$level-1,$key);
				continue;
			}
			
			if ( array_key_exists( "{$key}_attr", $array) ) {
				foreach ((array) $array["{$key}_attr"] as $attr => $attr_value)
					$attributes .= ' ' . self::escape($attr) . '="' . self::entities($attr_value) . '"';
			}
			
			if ($parent)
				$key = $parent;
			
			if (empty($value)) {
				$xml .= "$indent<$key$attributes/>\n";
				
			} elseif (is_array($value)) {
				$xml .= "$indent<$key$attributes>\n" . self::encode($value,$level) . "$indent</$key>\n";
				
			} else {
				$xml .= "$indent<$key$attributes>" . self::entities($value) . "</$key>\n";
				
			}
		
		}
		
		return $xml;
	}

	private static function is_sequential($array)
	{
		return ( is_array($array) && !empty($array) && 0 == count(array_diff_key($array,array_keys(array_keys($array)))) );
	}

	private static function escape($value)
	{
		return preg_replace( '/[^a-z0-9\-\_\.\:]/i', '', $value );
	}

	private static function entities($value)
	{
		return htmlentities( $value, ENT_QUOTES, 'UTF-8' );
	}
}

?>