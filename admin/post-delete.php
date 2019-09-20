<?php

/**
 * @Author: sxli
 * @Date:   2019-04-10 21:41:13
 * @Last Modified by:   sxli
 * @Last Modified time: 2019-05-14 00:21:08
 */
//获取页面提交的数据
require_once '../function.php';
if (empty($_GET['id'])) {
	exit('缺少必要参数');
}
	$id=$_GET['id'];

	// $rows=xiu_execute('delete from categories where id in('.$id.');');
	// 批量删除id in ('. $id .')
	$rows = xiu_execute('delete from posts where id in ('. $id .');');
	var_dump($rows);

	// var_dump($id);
	// if ($rows>0) {}
	// header('Location:/admin/categories.php');
	// .$_SERVER['HTTP_REFER'];用来标识请求源,他的url里面的数据不会改变
	// 这里直接返回请求过来的页面
	header('Location: ' . $_SERVER['HTTP_REFERER']);