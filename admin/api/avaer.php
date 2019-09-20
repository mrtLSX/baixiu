<?php

/**
 * @Author: sxli
 * @Date:   2019-04-10 07:06:44
 * @Last Modified by:   sxli
 * @Last Modified time: 2019-04-10 14:46:08
 */
//接受客户端传递过来的eamil,判断有无接收到数据
require_once '../../config.php';
//
if (empty($_GET['email'])) {
	return;
}
$email=$_GET['email'];


//2.查询对应的头像
//创建连接
$conn=mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);

if (!$conn) {
	exit('连接数据库失败');
}

//创建查询
$res=mysqli_query($conn,"select avatar from users where email='{$email}' limit 1;");

if (!$res) {
	exit('查询失败');
}

$row=mysqli_fetch_assoc($res);

echo $row['avatar'];


