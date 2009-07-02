<?php

/**
 * Array to XML Class
 * Used to generate RSS/ATOM feeds.
 * 
 * Usage:
 *     echo Pixelpost_Feed::build($array);
 * 
 *
 * @package Pixelpost
 * @author Jay Williams
 */
class Pixelpost_Feed
{
	/**
	 * Indentation Character(s)
	 * 
	 * Tab by default, but could be set to three or four spaces if desired.
	 * If no intent is preferred, simply set the variable to false.
	 *
	 */
	public static $indent;
	
	/**
	 * Turn a properly formatted array into standards compliant XML.
	 *
	 * @param array $feed The array containing all of the XML tags
	 * @param string $indent (optional) Preferred indentation character(s)
	 * @return string XML Output
	 */
	public static function build($feed=array(), $indent="\t")
	{
		self::$indent = $indent;
		
		return '<?xml version="1.0" encoding="utf-8"?>' . "\n". self::encode($feed);
	}
	
	/**
	 * Convert a multi-dimensional array to XML.
	 *
	 * @param array $array source array (recurring)
	 * @param int $level the indent level to start at
	 * @param bool|string $parent manually specify the tag
	 * @return string $xml
	 */
	private static function encode($array=array(), $level=0, $parent=false)
	{
		// Initialize Variables
		$xml    = '';
		$indent = '';
		$level++;
		
		/**
		 * Check if indenting is enabled
		 */
		if (self::$indent)
			for ($i=1; $i < $level; $i++) { $indent .= self::$indent; }
		
		
		foreach( $array as $tag => $value )
		{
			// Remove any non-safe XML characters from the tag
			$tag = self::escape_key($tag);
			
			// If the tag contains attributes, we will store them here
			$attributes = '';
			
			// Make sure we don't create tags for attribute arrays
			if ( substr( $tag, -5) == '_attr' )
				continue;
			
			/**
			 * Sequential, numerically keyed arrays use their parent key as their tag.
			 * 
			 * For example, if you have an array named 'item' which contains a numerical 
			 * sequential array, it will respond like this:
			 * 
			 * Input: 
			 *     $feed['item'] = array('one','two,'three');
			 * 
			 *  Result:
			 *     <item>one</item>
			 *     <item>two</item>
			 *     <item>three</item>
			 */
			if ( self::is_sequential($value) ) {
				$xml .= self::encode($value,$level-1,$tag);
				continue;
			}
			
			/**
			 * Attributes for tags are set via arrays on the same level and named the same 
			 * as the tag, but with the suffix "_attr" added to the end of the tag name.
			 * 
			 *  Input:
			 *     $feed['atom:link'] = '';
			 *     $feed['atom:link_attr'] = array('href'=>'http://example.com','rel'=>'self','type'=>'application/rss+xml');
			 * 
			 *  Result:
			 *     <atom:link href="http://example.com" rel="self" type="application/rss+xml"/>
			 */
			if ( array_key_exists( "{$tag}_attr", $array) ) {
				
				foreach ((array) $array["{$tag}_attr"] as $attribute => $attribute_value)
					$attributes .= ' ' . self::escape_key($attribute) . '="' . self::escape_value($attribute_value) . '"';
					
			}
			
			if ($parent)
				$tag = $parent;
			
			if (empty($value)) {
				// If the tag doesn't contain a value, self-close it:
				$xml .= "$indent<$tag$attributes/>\n";
			
			} elseif (is_array($value)) {
				// If the tag has sub-elements, we need to process those as well:
				$xml .= "$indent<$tag$attributes>\n" . self::encode($value,$level) . "$indent</$tag>\n";
			
			} else {
				// Output the tag, and the value:
				$xml .= "$indent<$tag$attributes>" . self::escape_value($value) . "</$tag>\n";
				
			}
		
		}
		
		return $xml;
	}
	
	/**
	 * Check if the array has only sequentially numbered keys, i.e. not associative.
	 *
	 * @param array $array The array to check
	 * @return bool true if sequential only
	 */
	private static function is_sequential($array)
	{
		return ( is_array($array) && !empty($array) && 0 == count(array_diff_key($array,array_keys(array_keys($array)))) );
	}

	/**
	 * Removes any non-XML safe characters from tags and attribute names
	 *
	 * @param string $value Tag/Attribute name
	 * @return string XML-Safe name
	 */
	private static function escape_key($value)
	{
		return preg_replace( '/[^a-z0-9\-\_\.\:]/i', '', $value );
	}

	/**
	 * Convert all applicable characters to their XML-safe equivalents
	 * 
	 * @todo Escape attribute values differently than regular tag values
	 * @todo use CDATA if possible for tag values
	 *
	 * @param string $value unsafe string
	 * @return string XML-Safe string
	 */
	private static function escape_value($value)
	{
		return htmlentities( $value, ENT_QUOTES, 'UTF-8' );
	}
}

?>