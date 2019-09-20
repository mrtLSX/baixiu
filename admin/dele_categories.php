<?php

/**
 * @Author: sxli
 * @Date:   2019-04-10 21:41:13
 * @Last Modified by:   sxli
 * @Last Modified time: 2019-04-11 11:00:52
 */
//获取页面提交的数据
require_once '../function.php';
if (empty($_GET['id'])) {
	exit('缺少必要参数');
}
	$id=$_GET['id'];

	// $rows=xiu_execute('delete from categories where id in('.$id.');');
	$rows = xiu_execute('delete from categories where id in ('. $id .');');

	// var_dump($id);
	// if ($rows>0) {}
	header('Location:/admin/categories.php');