<?php
class base
{
	function select($table, $dist, $sel_expr, $where_arr = '', $where_oper = '', $order_by = 'id', $order_by_sort = 'ASC', $limit_begin = '0', $limit_end = '', $group_by = '')
	{
		if(!empty($group_by))
			$group = ' GROUP BY '.$group_by.' ';
		else
			$group = '';
		$table=mysql_real_escape_string($table);
		if(!in_array($dist, array('DISTINCT', 'DISTINCTROW', 'ALL'))) 
			$dist = 'ALL';
		$sel_expr=mysql_real_escape_string($sel_expr);
		$where_oper=mysql_real_escape_string($where_oper);
		$where='WHERE';
		$i=0;
		if(!empty($where_arr))
		{
			while($i<count($where_arr))
			{
				if(in_array($where_arr[$i]['oper'], array('=', '>=', '>', '<=', '<', '<>', '!='))) 
				{
					$key = mysql_real_escape_string($where_arr[$i]['key']);
					$value = mysql_real_escape_string($where_arr[$i]['value']);
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
			$order_by=mysql_real_escape_string($order_by);	
			if(!in_array( $order_by_sort, array( 'ASC', 'DESC')))
				$order_by_sort = 'ASC';
			$order = ' ORDER BY '.$order_by.' '.$order_by_sort;
		}
		$order_by=mysql_real_escape_string($order_by);	
		if(!in_array( $order_by_sort, array( 'ASC', 'DESC')))
			$order_by_sort = 'ASC';
		$lim = '';
		if(!empty($limit_end))
		{
			$limit_end=mysql_real_escape_string($limit_end);
			$lim = ' LIMIT '.$limit_end;
			if(!empty($limit_begin))
			{
				$limit_begin=mysql_real_escape_string($limit_begin);
				$lim = $lim.' OFFSET '.$limit_begin;
			}
		}
		$query = 'SELECT '.$dist.' '.$sel_expr.' FROM '.$table.' '.$where.$group.$order.$lim;
// 		echo $query.'<br>';
			
		
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) 
		{
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			if($c_res=mysql_query($query))
			{
				$i = 0;
				while ($arr = mysql_fetch_assoc($c_res))
				{
					$ret[$i]=$arr;
					$i++;
				}
			}
			else
				echo mysql_error();
			return $ret;
		}
		else 
			die('Could not connect to database, please check /config/db.inc.php');
	}

	function insert($table, $arr)
	{
		$table=mysql_real_escape_string($table);
		$i=0;
		while ($i<count($arr))
		{
			$fields = $fields.', '.mysql_real_escape_string($arr[$i][0]);
			$values = $values.', \''.mysql_real_escape_string($arr[$i][1]).'\'';
			$i++;
		}
		$fields = substr_replace($fields, '', 0, 1);
		$values = substr_replace($values, '', 0, 1);
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) 
		{
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
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
			if(mysql_query($query))
				return 1;
			else 
				return -1;
		}
		else 
			die('Could not connect to database, please check /config/db.inc.php');
	}

	function update($table, $field, $value, $id_field='id', $id)
	{
		$table=mysql_real_escape_string($table);
		$field=mysql_real_escape_string($field);
		$value=mysql_real_escape_string($value);
		$id=mysql_real_escape_string($id);
		$id_field=mysql_real_escape_string($id_field);
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) 
		{
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			if (!empty($id))
				$query='UPDATE `'.$GLOBALS['tbl_prefix'].$table.'` SET `'.$field.'`=\''.$value.'\' WHERE `'.$id_field.'`=\''.$id.'\' ';
			else
				return -1;
			if(mysql_query($query))
				return 1;
			else 
				return -1;
		}
		else 
			die('Could not connect to database, please check /config/db.inc.php');
	}

	function delete($table, $id_field='id', $id)
	{
		$table=mysql_real_escape_string($table);
		$id=mysql_real_escape_string($id);
		$id_field=mysql_real_escape_string($id_field);
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) 
		{
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			if (!empty($id))
				$query='DELETE FROM `'.$GLOBALS['tbl_prefix'].$table.'` WHERE `'.$id_field.'`=\''.$id.'\' ';
			else
				return -1;
			if(mysql_query($query))
				return 1;
			else 
				return -1;
		}
		else 
			die('Could not connect to database, please check /config/db.inc.php');
	}

	function query($query, $returnas = 'assoc_array', $param_array)
	{
		for($i=0; $i<count($param_array); $i++)
		{
			$param_array[$i] = mysql_real_escape_string($param_array[$i]);
			$query = str_replace('::'.$i.'::', $param_array[$i], $query);
		}
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) 
		{
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			$query = str_replace('[prefix]', $GLOBALS['tbl_prefix'], $query);
			if ($ret_res = mysql_query($query))
			{
				$i = 0;
				switch ($returnas)
				{
					case 'array':
						while ($r = mysql_fetch_array($ret_res))
						{
							$ret[$i] = $r;
							$i++;
						}
					break;
					case 'assoc_array':
						while ($r = mysql_fetch_assoc($ret_res))
						{
							$ret[$i] = $r;
							$i++;
						}
					break;
					case 'object':
						while ($r = mysql_fetch_object($ret_res))
						{
							$ret[$i] = $r;
							$i++;
						}
					break;
				}
				return $ret;
			}
			if(strlen(mysql_error()) > 0)
				echo '<fieldset><legend>MySQL Error</legend>Error: '.mysql_error().'<br>In query: '.$query.'<br></fieldset>';
		}
		else 
			die('Could not connect to database, please check /config/db.inc.php');
	}

}
?>