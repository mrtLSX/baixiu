<?php
//接收客户端的ajax请求,返回评论数据
//


//载入所有封装的定义函数
require_once '../../function.php';


//数据库查询到所偶有的数据
//lngth
$page = empty($_GET['page'])? 1 : intval($_GET['page']);

$length= 5;

$offset=($page - 1) * $length;
//得到字符串
$sql = sprintf('select 
comments.*,
posts.title as post_title
from comments
inner join posts on posts.id=comments.post_id
order by comments.created desc
limit %d,%d;',$offset,$length);

// 查询所有评论数据
$comments=xiu_fetch_all($sql);

//查询数量总评论
$total_count= xiu_fetch_one('select count(1)  as count
	from comments
	inner join posts on posts.id=comments.post_id;')['count'];

$total_pages=ceil($total_count/$length);
// var_dump($total_count);
//返回的数据类型是float ,但数字一定是整数

//网络连接之间只能是字符串 (也可以2进制)转换成字符串 (序列化)  
// $json=json_encode($comments); //序列化  反序列化decode
// 
// 处理成关联数组
$json=json_encode(array(
'total_pages'=> $total_pages,
'comments'=>$comments

));
//声明返回的格式,默认html 这里声明json
header('Content-Type: application/json');

//响应给客户端
echo $json;