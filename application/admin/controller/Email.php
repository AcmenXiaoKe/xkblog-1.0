<?php


namespace app\admin\controller;


use think\Controller;
use think\facade\Env;
use think\Request;

class Email extends Controller
{
    public function index()
    {
        $Data = include Env::get('config_path').'Email.php';
        $this->assign([
            'data'  =>  $Data
        ]);
        return $this->fetch('/email');
    }
    public function edit(Request $request)
    {
        $data = $request->param();
        $code = <<<INFO
<?php
return [
    'Host'      =>  '{$data['Host']}',          // SMTP 服务器
    'Port'      =>  '{$data['Port']}',          // SMTP服务器的端口号
    'SMTPSecure'=>  '{$data['SMTPSecure']}',                  // 安全协议
    'Username'  =>  '{$data['Username']}',    // SMTP服务器用户名
    'Password'  =>  '{$data['Password']}',     // SMTP服务器密码
    'Name'      =>  '{$data['Name']}',            //  发送者名称
];
INFO;
        file_put_contents(Env::get('config_path').'Email.php', $code);
        return redirect('/admin/email');
    }
}