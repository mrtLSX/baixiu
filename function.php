<?php
require_once 'config.php';
/**
 *
 * 封装公用的函数
 */
require_once 'function.php';
session_start();
function xiu_get_current_user(){
	//session中获取登录信息,没有获取跳到登录页面
	if (empty($_SESSION['current_login_user'])) {
		header('Location:/admin/login.php');
		exit(); //没有必要执行后面的代码
	}
	//返回值用户的登录信息
	return $_SESSION['current_login_user'];
}

//通过数据库查询获取数据
//获取所有数据
function xiu_fetch_all($sql){
	$conn=mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);
	if(!$conn){
		exit('连接失败');
	}
			
	$query= mysqli_query($conn,$sql);
		if (!$query) {
			//查询失败
			return false;
		}
	$result=[];
	while ($row=mysqli_fetch_assoc($query)) {
		$result[]=$row;
	}
	mysqli_free_result($query);
	mysqli_close($conn);

	return $result;
}

//筛选一条数据
function xiu_fetch_one($sql){
	$ret = xiu_fetch_all($sql);
	return isset($ret[0])? $ret [0] : null;

}

//增修改功能的数据库查寻,返回影响的行数

function xiu_execute ($sql) {
	
		$conn=mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);
		if(!$conn){
			exit('连接失败');
		}
		//执行增加操作
		$query= mysqli_query($conn,$sql);
		if (!$query) {
			//查询失败
			return false;
		}
			//拿到变化的行数
		$affected_rows=mysqli_affected_rows($conn);

		
		
		mysqli_close($conn);

		return $affected_rows;
}



