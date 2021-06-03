<?php


namespace app\admin\controller;


use think\Controller;
use think\Db;
use think\facade\Cookie;
use app\admin\model\User as UserModel;
use think\facade\Validate;
use think\Request;

class PersonalSettings extends Controller
{
    public function index()
    {
        $user = getUserInfo();
        $this->assign('data',$user);
        return $this->fetch('/PersonalSettings');
    }
    // 修改用户昵称，邮箱
    public function user(Request $request) {
        $data  = $request->post();
        $model = Db::name('user')->where('username',Cookie::get('user'))->update($data);
        return redirect('/admin/personal_settings');
    }
    // 修改用户密码
    public function EditPassword(Request $request){
        $data = $request->post();
        if(!Validate::confirm($data['password'],$data['ispassword'])) {
            return json(res(null,'密码不一制!',400));
        }
        $user =getUserInfo();
        $edit = UserModel::name('user')->where('username',Cookie::get('user'))->update([
            'password' =>   password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
        Cookie::clear('user');
        return redirect('/admin/login');
    }


}