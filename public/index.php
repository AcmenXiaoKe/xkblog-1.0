<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;

// 加载基础文件
use think\facade\Env;

require __DIR__ . '/../thinkphp/base.php';

if( PHP_VERSION >= 7.4 || PHP_VERSION <= 7.1) {
    echo  '请使用 7.1 ~ 7.3 之间的PHP版本';
    exit();
}

// 支持事先使用静态方法设置Request对象和Config对象
// 定义模板路径
define('TMPL_PATH','themes/');

// 执行应用并响应
Container::get('app')->run()->send();