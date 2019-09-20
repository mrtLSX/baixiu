<?php
require_once 'function.php';
/**
 * @Author: sxli
 * @Date:   2019-04-11 14:18:14
 * @Last Modified by:   sxli
 * @Last Modified time: 2019-04-11 14:29:21
 */
$abc=xiu_fetch_all('select * from categories where id =48;');

var_dump($abc);