<?php
final class base implements baseInterface
{
	static $connection = null;
	private function connect()
	{
		if (self::$connection)
			return;
		self::$connection = new mysqli($GLOBALS['db_host'].':'.$GLOBALS['db_port'], $GLOBALS['db_user'], $GLOBALS['db_pass'], $GLOBALS['db_name']);
		if (self::$connection)
		{
			self::$connection->query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
		}
		else
			die('Could not connect to database, please check /config/database.ini');
	}
	private function escape_string($value)
	{
		if (get_magic_quotes_gpc()) 
			$value = stripslashes($value);
		if (!is_numeric($value)) 
			$value = self::$connection->real_escape_string($value);
		return $value;
	}
	public function select($table, $dist, $sel_expr, $where_arr = '', $where_oper = '', $order_by = 'id', $order_by_sort = 'ASC', $limit_begin = '0', $limit_end = '', $group_by = '')
	{
		self::connect();
		if(!empty($group_by))
			$group = ' GROUP BY '.$group_by.' ';
		else
			$group = '';
		$table=self::escape_string($table);
		if(!in_array($dist, array('DISTINCT', 'DISTINCTROW', 'ALL'))) 
			$dist = 'ALL';
		$sel_expr=self::escape_string($sel_expr);
		$where_oper=self::escape_string($where_oper);
		$where='WHERE';
		$i=0;
		if(!empty($where_arr))
		{
			while($i<count($where_arr))
			{
				if(in_array($where_arr[$i]['oper'], array('=', '>=', '>', '<=', '<', '<>', '!='))) 
				{
					$key = self::escape_string($where_arr[$i]['key']);
					$value = self::escape_string($where_arr[$i]['value']);
					$where = $where.' '.$key.' '.$where_arr[$i]['oper'].' \''.$value.'\'';
					if($i<count($where_arr)-1)
						$where = $where.' '.$where_oper;
				}
				$i++;
			}
		}
		else
			$where = $where.' true';
		$order = '';
		if(!empty($order_by))
		{
			$order_by=self::escape_string($order_by);	
			if(!in_array( $order_by_sort, array( 'ASC', 'DESC')))
				$order_by_sort = 'ASC';
			$order = ' ORDER BY '.$order_by.' '.$order_by_sort;
		}
		$order_by=self::escape_string($order_by);	
		if(!in_array( $order_by_sort, array( 'ASC', 'DESC')))
			$order_by_sort = 'ASC';
		$lim = '';
		if(!empty($limit_end) && $limit_end != 'NULL')
		{
			$limit_end=self::escape_string($limit_end);
			$lim = ' LIMIT '.$limit_end;
			if(!empty($limit_begin))
			{
				$limit_begin=self::escape_string($limit_begin);
				$lim = $lim.' OFFSET '.$limit_begin;
			}
		}
		$query = 'SELECT '.$dist.' '.$sel_expr.' FROM '.$table.' '.$where.$group.$order.$lim;
// 		echo $query.'<br>';
		if($c_res=self::$connection->query($query))
		{
			$i = 0;
			$ret = array();
			while ($arr = $c_res->fetch_assoc())
			{
				$ret[$i]=$arr;
				$i++;
			}
                        $c_res->free();
			return $ret;
		}
		else
			echo self::$connection->error;
	}
	public function insert($table, $arr)
	{
		self::connect();
		$table=self::escape_string($table);
		$i=0;
		while ($i<count($arr))
		{
			$fields = $fields.', '.self::escape_string($arr[$i][0]);
			$values = $values.', \''.self::escape_string($arr[$i][1]).'\'';
			$i++;
		}
		$fields = substr_replace($fields, '', 0, 1);
		$values = substr_replace($values, '', 0, 1);
		if (!empty($table))
		{
			if (!empty($fields))
			{
				$query='INSERT INTO '.$GLOBALS['tbl_prefix'].$table.'('.$fields.') VALUES( '.$values.');';
// 					echo $query.'<br>';
			}
			else 
				return -1;
		}
		else 
			return -1;
		if(self::$connection->query($query))
			return 1;
		else 
			return -1;
	}
	public function update($table, $field, $value, $id_field='id', $id)
	{
		self::connect();
		$table=self::escape_string($table);
		$field=self::escape_string($field);
		$value=self::escape_string($value);
		$id=self::escape_string($id);
		$id_field=self::escape_string($id_field);
		if (!empty($id))
			$query='UPDATE `'.$GLOBALS['tbl_prefix'].$table.'` SET `'.$field.'`=\''.$value.'\' WHERE `'.$id_field.'`=\''.$id.'\' ';
		else
			return -1;
		if(self::$connection->query($query))
			return 1;
		else 
			return -1;
	}
	public function delete($table, $id_field='id', $id)
	{
		self::connect();
		$table=self::escape_string($table);
		$id=self::escape_string($id);
		$id_field=self::escape_string($id_field);
		if (!empty($id))
			$query='DELETE FROM `'.$GLOBALS['tbl_prefix'].$table.'` WHERE `'.$id_field.'`=\''.$id.'\' ';
		else
			return -1;
		if(self::$connection->query($query))
			return 1;
		else 
			return -1;
	}
	public function query($query, $returnas = 'assoc_array', $param_array)
	{
		self::connect();
		for($i=0; $i<count($param_array); $i++)
		{
			$param_array[$i] = self::escape_string($param_array[$i]);
			$query = str_replace('::'.$i.'::', $param_array[$i], $query);
		}
		$query = str_replace('[prefix]', $GLOBALS['tbl_prefix'], $query);
		if ($ret_res = self::$connection->query($query))
		{
			 if($ret_res->num_rows>0)
			{
				
				$i = 0;
				switch ($returnas)
				{
				case 'array':
					while ($r = $ret_res->fetch_array())
					{
						$ret[$i] = $r;
						$i++;
					}
					break;
				case 'assoc_array':
					while ($r = $ret_res->fetch_assoc())
					{
						$ret[$i] = $r;
						$i++;
					}
					break;
				case 'object':
					while ($r = $ret_res->fetch_object())
					{
						$ret[$i] = $r;
						$i++;
					}
					break;
				}
				$ret_res->free();
				return $ret;
			}
			else
				return 1;
		}
		if(strlen(self::$connection->error) > 0)
			echo '<fieldset><legend>MySQL Error</legend>Error: '.self::$connection->error.'<br>In query: '.$query.'<br></fieldset>';
	}
}
?>