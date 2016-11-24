<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

//Route::rule(['/', '/index.php', '/index.php/'], 'index/index/index');
Route::rule('/', 'index/index/index');

Route::rule([
    '/test' => 'index/Index/test',
    '/category/onceList' => 'index/Category/onceList',
    '/category/secondList' => 'index/Category/secondList',
    '/user/sendNoteCode' => 'index/User/sendNoteCode',
    '/user/register' => 'index/User/register',
    '/user/login' => 'index/User/login',
    '/area/city' => 'index/Area/city',
    '/area/district' => 'index/Area/district',
    '/area/trading' => 'index/Area/tradingArea',
    '/store/list' => 'index/Store/getList',
    '/store/detail' => 'index/Store/detail',
    '/store/sort' => 'index/store/getSort',
    '/collect/store' => 'index/Collect/store',
    '/collect/coupon' => 'index/Collect/coupon',
    '/coupon/detail' => 'index/coupon/detail',
], '');

/*Route::pattern([
    'name' => '\w+',
    'id' => '\d+',
]);*/

/*return [
    '__pattern__' => [
        'name' => '\w+',
        'id' => '\w+',
    ],
    '[hello]' => [
        ':id' => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],
];*/

/*\think\Route::rule([
    '/test/:id' => 'index/test',
    //'blog/:id' => ['Blog/update', ['ext' => 'shtml'], ['id' => '\d{4}']],
], '', 'GET', [], ['id' => '\d+']);*/

/*return [
    //'new/:id' => 'index/test',
    'test/:id' => ['app/index/controller/index/test', ['method' => 'post|put'], ['id' => '\d+']],
];*/