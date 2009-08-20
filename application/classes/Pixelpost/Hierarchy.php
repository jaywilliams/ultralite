<?php

/**
 * This class allows you to work with trees in the database.
 * This can be used for example for menu items, categories or 
 * tags and supports unlimited sub items.
 *
 * @author Dennis Mooibroek (Pixelpost Development Team)
 * @version 0.1
 * @since Version 2.0 (Alpha 1)
 * @package pixelpost
 * @subpackage metadata
 * @url http://www.sitepoint.com/article/hierarchical-data-database/
 * @url http://www.phpro.org/tutorials/Managing-Hierarchical-Data-with-PHP-and-MySQL.html
 **/

class Pixelpost_Hierarchy
{

	public function __construct($tablename)
	{
		$this->tablename = Pixelpost_DB::escape($tablename);
	}

	/**
	 * return the full tree
	 * @return array
	 */
	public function getTree()
	{
		$result = $this->getNodeDepth();
		return $this->nestify($result, 'depth');
	}
	
	/**
	 * Convert that data from a flat array into a multi-dimensional array
	 * @param array $arrs
	 * @param string $depth_key
	 * @return array
	 * @url http://semlabs.co.uk/journal/converting-nested-set-model-data-in-to-multi-dimensional-arrays-in-php
	 */
	protected function nestify( $arrs, $depth_key = 'depth' )
	{
		$nested = array();
		$depths = array();
		foreach( $arrs as $key => $arr ) 
		{
			/**
			 * the array containing the nodes can contain objects
			 * We have to transform them to an array
			 */
		 	if (is_object($arr))
				$arr = $this->objectToArray($arr);
			if( $arr[$depth_key] == 0 ) 
			{
				$nested[$key] = $arr;
				$depths[$arr[$depth_key] + 1] = $key;
			}
			else 
			{
				$parent =& $nested;
				for( $i = 1; $i <= ( $arr[$depth_key] ); $i++ ) 
				{
					$parent =& $parent[$depths[$i]];
				}
				$parent[$key] = $arr;
				$depths[$arr[$depth_key] + 1] = $key;
			}
		}
		return $nested;
	}
	
	/**
	 *
	 * Convert an object to an array
	 * @access protected
	 * @param object
	 * @return array
	 */
	protected static function objectToArray( $object )
    {
        if( !is_object( $object ) && !is_array( $object ) )
        {
            return $object;
        }
        if( is_object( $object ) )
        {
            $object = get_object_vars( $object );
        }
        return array_map( 'Pixelpost_Hierachy::objectToArray', $object );
    }

	/**
	 *
	 * Find all leaf nodes
	 * @access public
	 * @return array
	 */
	public function leafNodes()
	{
		$sql = "SELECT name FROM " . $this->tablename . " WHERE right_node = left_node + 1";
		$result = (array) Pixelpost_DB::get_results($sql);
		return $result;
	}

	/**
	 * Retrieve a single path
	 * @access public
	 * @param $node_name
	 * @return array
	 */
	public function singlePath($node_name)
	{
		$sql = "SELECT parent.name FROM " . $this->tablename . " AS node, 
			" . $this->tablename . " AS parent
			WHERE node.left_node BETWEEN parent.left_node AND parent.right_node 
		 	AND node.name = '" . Pixelpost_DB::escape($node_name) . "' ORDER BY node.left_node";
	 	$result = (array) Pixelpost_DB::get_results($sql);
		return $result;
	}


	/**
	 * Retrieve a depth of nodes
	 * @access public
	 * @param $node_name
	 * @return array
	 */
	public function getNodeDepth()
	{
		$sql = "SELECT node.name AS name, (COUNT(parent.name) - 1) AS depth 
			FROM " . $this->tablename . " AS node, 
			" . $this->tablename . " AS parent 
			WHERE node.left_node BETWEEN parent.left_node 
			AND parent.right_node GROUP BY node.name ORDER BY node.left_node";
		$result = (array) Pixelpost_DB::get_results($sql);
		return $result;
	}

	/**
	 * Retrieve a subTree depth
	 * @access public
	 * @param $node_name
	 * @return array
	 */
	public function subTreeDepth($node_name)
	{
		$sql = "SELECT node.name AS name, (COUNT(parent.name) - 1) AS depth 
			FROM " . $this->tablename . " AS node, 
			" . $this->tablename . " AS parent 
			WHERE node.left_node BETWEEN parent.left_node 
			AND parent.right_node AND node.name = '" . Pixelpost_DB::escape($node_name) . "' 
			GROUP BY node.name 
			ORDER BY node.left_node";
		$result = (array) Pixelpost_DB::get_results($sql);
		return $result;
	}

	/**
	 * Get local sub nodes only
	 * @access public
	 * @param $node_name
	 * @return array
	 */
	public function getLocalSubNodes($node_name)
	{
		$sql = "SELECT node.name AS name, (COUNT(parent.name) - (sub_tree.depth + 1)) AS depth 
			FROM " . $this->tablename . " AS node, 
			" . $this->tablename . " AS parent, 
			categories AS sub_parent,
		    (
    			SELECT node.name AS name, (COUNT(parent.name) - 1) AS depth
    			FROM " . $this->tablename . " AS node,
    			" . $this->tablename . " AS parent
    			WHERE node.left_node BETWEEN parent.left_node AND parent.right_node
    			AND node.name = '" . Pixelpost_DB::escape($node_name) . "'
    			GROUP BY node.name
    			ORDER BY node.left_node
    		)AS sub_tree
			WHERE node.left_node BETWEEN parent.left_node AND parent.right_node
    		AND node.left_node BETWEEN sub_parent.left_node AND sub_parent.right_node
    		AND sub_parent.name = sub_tree.name
			GROUP BY node.name
			HAVING depth <= 1
			ORDER BY node.left_node";
		$result = (array) Pixelpost_DB::get_results($sql);
		//Pixelpost_DB::debug();
		return $result;
	}

	/**
	 * @add a node
	 * @access public
	 * @param string $left_node
	 * @param string $new_node
	 */
	public function addNode($left_node, $new_node)
	{
		try
		{
			$sql = "SELECT right_node FROM " . $this->tablename . " 
				WHERE name = '" . Pixelpost_DB::escape($left_node) . "'";
			$myRight = (int) Pixelpost_DB::get_var($sql);
			/*** increment the nodes by two ***/
			Pixelpost_DB::query("UPDATE " . $this->tablename . " 
				SET right_node = right_node + 2 WHERE right_node > " . $myRight);
			Pixelpost_DB::query("UPDATE " . $this->tablename . " 
				SET left_node = left_node + 2 WHERE left_node > " . $myRight);
			/*** insert the new node ***/
			$new_left = $myRight + 1;
			$new_right = $myRight + 2;
			Pixelpost_DB::query("INSERT INTO " . $this->tablename . "
				(name, left_node, right_node) 
				VALUES('{$new_node}', '{$new_left}','{$new_right}')");
		}
		catch (exception $e)
		{
			throw new Exception($e);
		}
	}


	/**
	 *
	 * @Add child node
	 * @ adds a child to a node that has no children
	 * @access public
	 * @param string $node_name The node to add to
	 * @param string $new_node The name of the new child node
	 * @return array
	 */
	public function addChildNode($node_name, $new_node)
	{
		try
		{
			$sql = "SELECT left_node FROM " . $this->tablename . " 
				WHERE name='" . Pixelpost_DB::escape($node_name) . "'";
			$myLeft = (int) Pixelpost_DB::get_var($sql);
			Pixelpost_DB::query("UPDATE " . $this->tablename . " SET right_node = right_node + 2 WHERE right_node > ".$myLeft);
			Pixelpost_DB::query("UPDATE " . $this->tablename . " SET left_node = left_node + 2 WHERE left_node > ".$myLeft);

			$new_left = $myLeft + 1;
			$new_right = $myLeft + 2;
			Pixelpost_DB::query("INSERT INTO " . $this->tablename . "(name, left_node, right_node) 
				VALUES('{$new_node}', '{$new_left}', '{$new_right}')");
		}
		catch (exception $e)
		{
			throw new Exception($e);
		}
	}

	/**
	 * @Delete a leaf node
	 * @param string $node_name
	 * @access public
	 */
	public function deleteLeafNode($node_name)
	{
		try
		{
			$sql = "SELECT left_node, right_node FROM " . $this->tablename . " 
				WHERE name = '" . Pixelpost_DB::escape($node_name) . "'";
			$result = (array) Pixelpost_DB::get_results($sql);
			$result[0]->width_node = $result[0]->right_node - $result[0]->left_node + 1;
			
			Pixelpost_DB::query("DELETE FROM " . $this->tablename . " 
				WHERE left_node BETWEEN " . $result[0]->left_node . " AND " . $result[0]->right_node);
			Pixelpost_DB::query("UPDATE " . $this->tablename . " 
				SET right_node = right_node - " . $result[0]->width_node . " 
				WHERE right_node > " . $result[0]->right_node);
			Pixelpost_DB::query("UPDATE " . $this->tablename . " 
				SET left_node = left_node - " . $result[0]->width_node . " 
				WHERE left_node > " . $result[0]->right_node);
		}
		catch (exception $e)
		{
			throw new Exception($e);
		}
	}

	/**
	 * @Delete a node and all its children
	 * @access public
	 * @param string $node_name
	 */
	public function deleteNodeRecursive($node_name)
	{
		try
		{
			$sql = "SELECT left_node, right_node FROM " . $this->tablename . " 
				WHERE name = '" . Pixelpost_DB::escape($node_name) . "'";
			$result = (array) Pixelpost_DB::get_results($sql);
			$result[0]->width_node = $result[0]->right_node - $result[0]->left_node + 1;
			
			Pixelpost_DB::query("DELETE FROM " . $this->tablename . " 
				WHERE left_node BETWEEN " . $result[0]->left_node . " 
				AND " . $result[0]->right_node);
			Pixelpost_DB::query("UPDATE " . $this->tablename . " 
				SET right_node = right_node - " . $result[0]->width_node . " 
				WHERE right_node > " . $result[0]->right_node);
			 Pixelpost_DB::query("UPDATE " . $this->tablename . " 
			 	SET left_node = left_node - " . $result[0]->width_node . " 
				 WHERE left_node > " . $result[0]->right_node);
		}
		catch (exception $e)
		{
			throw new Exception($e);
		}
	}
}