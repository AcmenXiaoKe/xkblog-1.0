<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use \think\facade\Route;
// 首页
Route::get('/','index');
// 文章页
Route::get('/article/:id','article/index');
// 友联
Route::get('/link','link/index');
// 友联 申请
Route::post('/link/ApplyFor','index/link/ApplyFor');
// 搜索页
Route::get('/search/:val','Search/index');
// 分类搜索
Route::get('/class/:id','Search/category');
// 标签
Route::get('/tag/:val','Search/tag');
// 页面
Route::get('/page/:aid','Page/index');
//Route::miss('index/Error/index');
// 后台
//Route::get('/admin','/admin/');
//Route::get('/login','/admin/login/');

return [

];