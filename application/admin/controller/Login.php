<?php


namespace app\admin\controller;


use app\admin\model\User as UserModel;
use think\Controller;
use think\facade\Cookie;
use think\facade\Env;
use think\facade\Validate;
use think\Request;

class Login extends Controller
{
    public function index()
    {
        $web_options = include Env::get('config_path').'siteconfig.php';
        $this->assign('web_options',$web_options);
        return $this->fetch('/login');
    }
    public function check(Request $request)
    {
        $data = $request->post();
        $user =  UserModel::name('user')->where('username',$data['username'])->find();
        if(!$user) {
           return json(res(null,'账号或者密码错误！',400));
        }

        if(!password_verify($data['password'],$user['password'])) {
            return json(res(null,'账号或者密码错误！',400));
        }
        Cookie::clear('user');
        Cookie::set('user',$data['username']);
        $edit = UserModel::name('user')->where('username',$data['username'])->update([
            'logged'    =>  date('Y-m-d H:i:s')
        ]);
        return json(res(null,'登陆成功！',200));

    }
    public function Userexit () {
        Cookie::clear('user');
        return json(res(null,'退出成功！',200));
    }



}