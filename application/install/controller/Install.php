<?php


namespace app\install\controller;


use think\Controller;
use think\Db;
use think\Exception;
use think\facade\Env;
use think\Request;

class Install extends Controller
{
    public function index()
    {
        if(file_exists('install.lock')) {
            return redirect('/install');
        }
        return $this->fetch('/install');
    }
    public function handle(Request $request)
    {
        $data = $request->post();
        $result=  $request->post();
        $db =  Db::connect([
            'type'      =>  'mysql',
            'hostname'  =>  $result['hostname'],
            'database'  =>  $result['database'],
            'username'  =>  $result['username'],
            'password'  =>  $result['password'],
            'hostport'  =>  3306
        ]);
        try {
            $db->execute('select version()');
        } catch (Exception $e) {

        }
        $code = <<<INFO
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
return [
    // 数据库类型
    'type'            => 'mysql',
    // 服务器地址
    'hostname'        => '{$result['hostname']}',
    // 数据库名
    'database'        => '{$result['database']}',
    // 用户名
    'username'        => '{$result['username']}',
    // 密码
    'password'        => '{$result['password']}',
    // 端口
    'hostport'        => 3306,
    // 连接dsn
    'dsn'             => '',
    // 数据库连接参数
    'params'          => [],
    // 数据库编码默认采用utf8
    'charset'         => 'utf8',
    // 数据库表前缀
    'prefix'          => 'xk_',
    // 数据库调试模式
    'debug'           => false,
    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'deploy'          => 0,
    // 数据库读写是否分离 主从式有效
    'rw_separate'     => false,
    // 读写分离后 主服务器数量
    'master_num'      => 1,
    // 指定从服务器序号
    'slave_no'        => '',
    // 是否严格检查字段是否存在
    'fields_strict'   => false,
    // 数据集返回类型
    'resultset_type'  => 'array',
    // 自动写入时间戳字段
    'auto_timestamp'  => false,
    // 时间字段取出后的默认时间格式
    'datetime_format' => 'Y-m-d H:i:s',
    // 是否需要进行SQL性能分析
    'sql_explain'     => false,
    // Builder类
    'builder'         => '',
    // Query类
    'query'           => '\\think\\db\\Query',
]; 
INFO;
        file_put_contents(Env::get('config_path').'database.php', $code);
        echo '数据库配置生成成功！';
        echo '进行表的安装';
        $xk_group_table_sql = 'create table xk_group (
                          gid int primary key auto_increment,
                          name varchar(50) not null unique,
                          permissions varchar(255)
        );';
        $xk_label_table_sql = 'create table xk_label (
                          laid int primary key auto_increment,
                          name varchar(50)
);';
        $xk_contents_table_sql = 'create table xk_contents(
                           aid int primary key auto_increment,
                           title varchar (150) not null,
                           author  varchar (15) not null,
                           release_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                           content text not null,
                           class varchar (255),
                           label varchar (255),
                           browse int default 0 ,
                           praise int default 0,
                           top bool default false,
                           recommended bool default false ,
                           state bool default true,
                           cover varchar(255) DEFAULT NULL,
                           catid varchar(255) DEFAULT NULL,
                           type varchar (64) DEFAULT "post",
                           template varchar (64) DEFAULT null 
);';
        $xk_comments_table_sql = 'create table xk_comments(
                            cid int primary key auto_increment,
                            aid int  not null,
                            author varchar (20) not null,
                            email varchar (50) not null,
                            content varchar (255) not null,
                            pid int,
                            state bool default true,
                            date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            foreign key( aid ) references xk_contents (aid)
);';

        $xk_user_table_sql = 'create table xk_user (
                         uid int primary key auto_increment,
                         username varchar (20) not null unique ,
                         password varchar (255) not null,
                         email varchar (50) unique,
                         name varchar (15) not null,
                         logged timestamp,
                         permissions varchar(255),
                         foreign key( permissions ) references xk_group (name)
);';
        $xk_categories_table_sql  = 'create table xk_categories (
    catid int primary key auto_increment,
    pid int default 0,
    catname varchar (25) not null unique
);';

        Db::execute($xk_group_table_sql);
        echo 'xk_group创建成功！';


        Db::execute($xk_label_table_sql);
        echo 'xk_label创建成功！';

        Db::execute($xk_contents_table_sql);
        echo 'xk_article创建成功！';

        Db::execute($xk_comments_table_sql);
        echo 'xk_comments创建成功！';


        Db::execute($xk_categories_table_sql);
        echo 'xk_categories创建成功！';


        Db::execute($xk_user_table_sql);
        echo 'xk_user创建成功！';
        echo '*******************************************';
        echo '插入初始数据';
        $group = "insert into xk_group(name) values ('Super');";
        Db::execute($group);
        Db::name('categories')->insert([
            'catid'      =>  1,
            'catname'      =>  '默认分类'
        ]);
        Db::name('user')->insert([
            'username'          =>  $data['admin_username'],
            'password'          =>  password_hash($data['admin_password'], PASSWORD_DEFAULT),
            'email'             =>  $data['admin_email'],
            'name'              =>  $data['admin_name'],
            'permissions'       =>  'Super'
        ]);
        Db::name('contents')->insert([
            'aid'           =>      1,
            'title'         =>      '欢迎使用 Xkblog',
            'author'        =>      $data['admin_name'],
            'content'       =>      '如果您看到这篇文章,表示您的 blog 已经安装成功.',
            'catid'          =>      "['1']"
        ]);
        Db::name('comments')->insert([
            'aid'           =>       1,
            'author'        =>      'Xkblog',
            'content'       =>      '欢迎加入 Xkblog 大家族',
            'email'         =>       $data['admin_email'],
        ]);
        fopen("install.lock", "w");
    }
}