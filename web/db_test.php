<?php
/**
 * Created by PhpStorm.
 * User: 111
 * Date: 2018/11/7
 * Time: 上午 07:45
 */

var_dump('this is a db test');

$mysql_connect_str = getenv('CLEARDB_DATABASE_URL');

var_dump($mysql_connect_str);