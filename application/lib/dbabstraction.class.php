<?php
/**
 *
 * @Lite weight Database abstraction layer
 *
 * @copyright Copyright (C) 2009 PHPRO.ORG. All rights reserved.
 *
 * @license new bsd http://www.opensource.org/licenses/bsd-license.php
 * @filesource
 * @package Database
 * @Author Kevin Waterson
 *
 */

// namespace web2bb;

class dbAbstraction{

	/*
	 * @the errors array
	 */
	public $errors = array();

	/*
	 * @The sql query
	 */
	private $sql;

	/**
	 * @The name=>value pairs
	 */
	private $values = array();

	/**
	 *
	 * @add a value to the values array
	 *
	 * @access public
	 *
	 * @param string $key the array key
	 *
	 * @param string $value The value
	 *
	 */
	public function addValue($key, $value)
	{
		$this->values[$key] = $value;
	}


	/**
	 *
	 * @set the values
	 *
	 * @access public
	 *
	 * @param array
	 *
	 */
	public function setValues($array)
	{
		$this->values = $array;
	}

	/**
	 *
	 * @delete a recored from a table
	 *
	 * @access public
	 *
	 * @param string $table The table name
	 *
	 * @param int ID
	 *
	 */
	public function delete($table, $id)
	{
		try
		{
			// get the primary key name
			$pk = $this->getPrimaryKey($table);
			$sql = "DELETE FROM $table WHERE $pk=:$pk";
			$db = db::getInstance();
			$stmt = $db->prepare($sql);
			$stmt->bindParam(":$pk", $id);
			$stmt->execute();
		}
		catch(Exception $e)
		{
			$this->errors[] = $e->getMessage();
		}
	}


	/**
	 *
	 * @insert a record into a table
	 *
	 * @access public
	 *
	 * @param string $table The table name
	 *
	 * @param array $values An array of fieldnames and values
	 *
	 * @return int The last insert ID
	 *
	 */
	public function insert($table, $values=null)
	{
		$values = is_null($values) ? $this->values : $values;
		$sql = "INSERT INTO $table SET ";

		$obj = new CachingIterator(new ArrayIterator($values));

		try
		{
			$db = db::getInstance();
			foreach( $obj as $field=>$val)
			{
				$sql .= "$field = :$field";
				$sql .=  $obj->hasNext() ? ',' : '';
				$sql .= "\n";
			}
			$stmt = $db->prepare($sql);

			// bind the params
			foreach($values as $k=>$v)
			{
				$stmt->bindParam(':'.$k, $v);
			}
			$stmt->execute($values);
			// return the last insert id
			return $db->lastInsertId();
		}
		catch(Exception $e)
		{
			$this->errors[] = $e->getMessage();
		}
	}


	/**
	 * @update a table
	 *
	 * @access public
	 * 
	 * @param string $table The table name
	 *
	 * @param int $id
	 *
	 */
	public function update($table, $id, $values=null)
	{
		$values = is_null($values) ? $this->values : $values;
		try
		{
			// get the primary key/
			$pk = $this->getPrimaryKey($table);
	
			// set the primary key in the values array
			$values[$pk] = $id;

			$obj = new CachingIterator(new ArrayIterator($values));

			$db = db::getInstance();
			$sql = "UPDATE $table SET \n";
			foreach( $obj as $field=>$val)
			{
				$sql .= "$field = :$field";
				$sql .= $obj->hasNext() ? ',' : '';
				$sql .= "\n";
			}
			$sql .= " WHERE $pk=$id";
			$stmt = $db->prepare($sql);

			// bind the params
			foreach($values as $k=>$v)
			{
				$stmt->bindParam(':'.$k, $v);
			}
			// bind the primary key and the id
			$stmt->bindParam($pk, $id);
			$stmt->execute($values);

			// return the affected rows
			return $stmt->rowCount();
		}
		catch(Exception $e)
		{
			$this->errors[] = $e->getMessage();
		}
	}


	/**
	 * @get the name of the field that is the primary key
	 *
	 * @access private
	 *
	 * @param string $table The name of the table
	 *
	 * @return string
	 *
	 */
	private function getPrimaryKey($table)
	{
		try
		{
			// get the db name from the config.ini file
			$config = configuration::getInstance();
			$db_name = $config->config_values['database']['db_name']; 

			$db = db::getInstance();
			$sql = "SELECT
				k.column_name
				FROM
				information_schema.table_constraints t
				JOIN
				information_schema.key_column_usage k
				USING(constraint_name,table_schema,table_name)
				WHERE
				t.constraint_type='PRIMARY KEY'
				AND
				t.table_schema='{$db_name}'
				AND
				t.table_name=:table";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':table', $table, PDO::PARAM_STR);
			$stmt->execute();
			return $stmt->fetchColumn(0);
		}
		catch(Exception $e)
		{
			$this->errors[] = $e->getMessage();
		}
	}


	/**
	 *
	 * Fetch all records from table
	 *
	 * @access public
	 *
	 * @param $table The table name
	 *
	 * @return array
	 *
	 */
	public function query()
	{
		$res = db::getInstance()->query($this->sql);
		return $res;
	}

	/**
	 *
	 * @select statement
	 *
	 * @access public
	 *
	 * @param string $table
	 *
	 */
	public function select($table)
	{
		$this->sql = "SELECT * FROM $table";
	}

	/**
	 * @where clause
	 *
	 * @access public
	 *
	 * @param string $field
	 *
	 * @param string $value
	 *
	 */
	public function where($field, $value)
	{
		$this->sql .= " WHERE $field=$value";
	}

	/**
	 *
	 * @set limit
	 *
	 * @access public
	 *
	 * @param int $offset
	 *
	 * @param int $limit
	 *
	 * @return string
	 *
	 */
	public function limit($offset, $limit)
	{
		$this->sql .= " LIMIT $offset, $limit";
	}

	/**
	 *
	 * @add an AND clause
	 *
	 * @access public
	 *
	 * @param string $field
	 *
	 * @param string $value
	 *
	 */
	public function andClause($field, $value)
	{
		$this->sql .= " AND $field=$value";
	}


	/**
	 *
	 * Add and order by
	 *
	 * @param string $fieldname
	 *
	 * @param string $order
	 *
	 */
	public function orderBy($fieldname, $order='ASC')
	{
		$this->sql .= " ORDER BY $fieldname $order";
	}
} // end of class

?>
