<?php
interface baseInterface
{
	public function select($table, $dist, $sel_expr, $where_arr = '', $where_oper = '', $order_by = 'id', $order_by_sort = 'ASC', $limit_begin = '0', $limit_end = 'NULL', $group_by = '');
	public function insert($table, $arr);
	public function update($table, $field, $value, $id_field='id', $id);
	public function delete($table, $id_field='id', $id);
	public function query($query, $returnas = 'assoc_array', $param_array = array());
}
?>