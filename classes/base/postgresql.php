<?php
final class base implements baseInterface
{
	static $connection = null;
	private function connect()
	{
		if (self::$connection)
			return;
		self::$connection = pg_connect('host='.$GLOBALS['db_host'].' port='.$GLOBALS['db_port'].' dbname='.$GLOBALS['db_name'].' user='.$GLOBALS['db_user'].' password='.$GLOBALS['db_pass']);
		if (!self::$connection)
			die('Could not connect to database, please check /config/database.ini');
	}
	private function escape_string($value)
	{
		if (get_magic_quotes_gpc()) 
			$value = stripslashes($value);
		if (!is_numeric($value)) 
			$value = pg_escape_string($value);
		return $value;
	}
	public function select($table, $dist, $sel_expr, $where_arr = '', $where_oper = '', $order_by = 'id', $order_by_sort = 'ASC', $limit_begin = '0', $limit_end = 'NULL', $group_by = '')
	{
		$begin_time = timeMeasure();
		self::connect();
		if(!empty($group_by))
			$group = ' GROUP BY '.$group_by.' ';
		else
			$group = '';
		$table=self::escape_string($table);
		if(!in_array($dist, array('DISTINCT', 'DISTINCTROW', 'ALL'))) 
			$dist = 'ALL';
		else
		{
			if($dist=='DISTINCTROW')
				$dist = 'DISTINCT';
		}
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
		$limit_begin=self::escape_string($limit_begin);
		$limit_end=self::escape_string($limit_end);
		$query = 'SELECT '.$dist.' '.$sel_expr.' FROM '.$table.' '.$where.$group.$order.' OFFSET '.$limit_begin.' LIMIT '.$limit_end;
// 		echo $query.'<br />';
		if($c_res=pg_query($query))
		{
			$ret = array();
			$i = 0;
			while ($arr = pg_fetch_assoc($c_res))
			{
				$ret[$i]=$arr;
				$i++;
			}
		}
		else
			echo pg_last_error();
		echo '<!-- Запрос '.$query.' выполнен за '.round(timeMeasure()-$begin_time, 6).' сек.-->';
		return $ret;
	}
	public function insert($table, $arr)
	{
		$begin_time = timeMeasure();
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
//  				echo $query.'<br />';
			}
			else 
				return -1;
		}
		else 
			 return -1;
                $query_res = pg_query($query);
                echo '<!-- Запрос '.$query.' выполнен за '.round(timeMeasure()-$begin_time, 6).' сек.-->';
		if($query_res)
			return 1;
		else 
			return -1;
	}
	public function update($table, $field, $value, $id_field='id', $id)
	{
		$begin_time = timeMeasure();
		self::connect();
		$table=self::escape_string($table);
		$field=self::escape_string($field);
		$value=self::escape_string($value);
		$id=self::escape_string($id);
		$id_field=self::escape_string($id_field);
		if (!empty($id))
			$query='UPDATE '.$GLOBALS['tbl_prefix'].$table.' SET '.$field.'=\''.$value.'\' WHERE '.$id_field.'=\''.$id.'\' ';
		else 
			 return -1;
//  		echo $query.'<br>';
		if(pg_query($query))
		{
			echo '<!-- Запрос '.$query.' выполнен за '.round(timeMeasure()-$begin_time, 6).' сек.-->';
			return 1;
		}
		else 
			return -1;
	}
	public function delete($table, $id_field='id', $id)
	{
		$begin_time = timeMeasure();
		self::connect();
		$table=self::escape_string($table);
		$id=self::escape_string($id);
		$id_field=self::escape_string($id_field);
		if (!empty($id))
			$query='DELETE FROM '.$GLOBALS['tbl_prefix'].$table.' WHERE '.$id_field.'=\''.$id.'\' ';
		else 
			 return -1;
//  		echo $query.'<br>';
		if(pg_query($query))
		{
			echo '<!-- Запрос '.$query.' выполнен за '.round(timeMeasure()-$begin_time, 6).' сек.-->';
			return 1;
		}
		else 
			return -1;
	}
	public function query($query, $returnas = 'assoc_array', $param_array)
	{
		$begin_time = timeMeasure();
		self::connect();
		for($i=0; $i<count($param_array); $i++)
		{
			$param_array[$i] = self::escape_string($param_array[$i]);
			$query = str_replace('::'.$i.'::', $param_array[$i], $query);
		}
//  		echo $query.'<br>';
		$query = str_replace('[prefix]', $GLOBALS['tbl_prefix'], $query);
		if ($ret_res = pg_query($query))
		{
			$i = 0;
			switch ($returnas)
			{
				case 'array':
					while ($r = pg_fetch_array($ret_res))
					{
						$ret[$i] = $r;
						$i++;
					}
				break;
				case 'assoc_array':
					while ($r = pg_fetch_assoc($ret_res))
					{
						$ret[$i] = $r;
						$i++;
					}
				break;
				case 'object':
					while ($r = pg_fetch_object($ret_res))
					{
						$ret[$i] = $r;
						$i++;
					}
				break;
			}
			echo '<!-- Запрос '.$query.' выполнен за '.round(timeMeasure()-$begin_time, 6).' сек.-->';
			return $ret;
		}
		if(strlen(pg_last_error()) > 0)
		{
			echo '<fieldset><legend>PostgreSQL Error</legend>Error: '.pg_last_error().'<br>In query: '.$query.'<br></fieldset>';
			return -1;
		}
	}
}
?>