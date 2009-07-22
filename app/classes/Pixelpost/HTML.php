<?php

/**
 * Array to HTML form or table
 * Used to transform an array to either a table or a form.
 * 
 * Usage:
 *     echo Pixelpost_HTML::build($array);
 * 
 *
 * @package Pixelpost
 * @author Dennis Mooibroek
 */
class Pixelpost_HTML extends Pixelpost_Feed
{
	/**
	 * Currently this file only tries to describe the possibility of using the
	 * Pixelpost_Feed class for generating valid HTML for either a table or a form
	 * 
	 * First the possibilities of a table are explored.
	 * 
	 * Based upon the notion sequential, numerically keyed arrays use their parent key as their tag,
	 * it should be rather easy to produce a table. For example:
	 * 
	 * Input: 
	 *     $feed['td'] = array('one','two,'three');
	 * 
	 *  Result:
	 *     <td>one</td>
	 *     <td>two</td>
	 *     <td>three</td>
	 * 
	 * Another example, now showing the headers:
	 * 
	 * Input: 
	 *     $feed['th'] = array('heading_one','heading_two,'heading_three');
	 * 
	 *  Result:
	 *     <th>heading_one</th>
	 *     <th>heading_two</th>
	 *     <th>heading_three</th>
	 * 
	 * Both examples should be enclosed in a <tr>..</tr>, but the question remains how to handle 
	 * this specifically without losing the fact the current creation is pretty much independent.
	 * One way to solve this problem is to define specific functions, such as
	 * 
	 * Pixelpost_HTML::buildTable($array)
	 * 
	 * Using this, it is clearly used for building a table and as such we can assume the array 
	 * contains nothing but table elements. For testing purposes such a function is included,
	 * heavily based upon the encode function of the Pixelpost_Feed Class
	 * 
	 * PLEASE NOTE THE CURRENT IS DESIGN IS FLAWED. DESPITE THE FACT THE ARRAY CONTAINS MULTIPLE
	 * TD ITEMS ONLY THE LAST ONE IS PERSERVED. IT WOULD BE BETTER TO PUT THE ROWS IN AN NUMERICAL 
	 * ARRAY
	 * 
	 */

	/**
	 * Turn a properly formatted array into standards compliant table HTML.
	 *
	 * @param array $table The array containing all of the HTML Table tags
	 * @param string $indent (optional) Preferred indentation character(s)
	 * @return string HTML Output
	 */
	public static function buildTable($table = array(), $indent = "\t")
	{
		self::$indent = $indent;
		
		return '<table>' . "\n" . self::encodeHTML($table) . "\n" . '</table>';
	}

	private static function encodeHTML($array = array(), $level = 0, $parent = false)
	{
		// Initialize Variables
		$html = '';
		$indent = '';
		$level++;

		/**
		 * Check if indenting is enabled
		 */
		if (self::$indent)
			for ($i = 1; $i < $level; $i++)
			{
				$indent .= self::$indent;
			}


		foreach ($array as $tag => $value)
		{
			// Remove any non-safe XML characters from the tag
			$tag = self::escape_key($tag);

			// If the tag contains attributes, we will store them here
			$attributes = '';

			// Make sure we don't create tags for attribute arrays
			if (substr($tag, -5) == '_attr') continue;

			/**
			 * Input: 
			 *     $feed['th'] = array('heading_one','heading_two,'heading_three');
			 * 
			 *  Result:
			 *     <th>heading_one</th>
			 *     <th>heading_two</th>
			 *     <th>heading_three</th>

			 */
			if (self::is_sequential($value))
			{
				/**
				 * Here we initiate a switch so we can handle certain tags.
				 * 
				 */
				switch ($tag)
				{
					case 'th':
						// table header row, enclose between <tr>..</tr>
						$html .= '<tr>' . self::encode($value, $level - 1, $tag) . '</tr>';
						break;
					case 'td':
						// table header row, enclose between <tr>..</tr>
						$html .= '<tr>' . self::encode($value, $level - 1, $tag) . '</tr>';
						break;
					default:
						$html .= self::encode($value, $level - 1, $tag);
				}
				continue;
			}
		}
		return $html;
	}
}
?>