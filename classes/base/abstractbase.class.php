<?php
interface baseInterface
{
	function select($table, $dist, $sel_expr, $where_arr, $where_oper, $order_by, $order_by_sort, $limit_begin, $limit_end, $group_by);
	function insert($table, $arr);
	function update($table, $field, $value, $id_field, $id);
	function delete($table, $id_field, $id);
	function query($query, $returnas, $param_array);
}
?>