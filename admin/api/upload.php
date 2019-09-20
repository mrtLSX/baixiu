<?php

var_dump($_File['avatar']);

//接收文件
//保存文件
////返回这个文件的访问url
if (empty($_File['avatar'])) {
	# code...
	exit('必须上传文件');
}

$avatar= $_File['avatar'];

if($_File['avatar']!== UPLOAD_ERR_OK){
	exit('上传失败');
}

$ext = pathinfo($avatar['name'],PATHINFO_EXTENSION);
$target = '../../static/upload/img-' .uniqid(). $ext;

