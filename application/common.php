<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


use app\admin\model\User as UserModel;
use app\admin\model\Contents as ContentsModel;
use app\admin\model\Comments as CommentsModel;
use think\facade\Cookie;
use think\facade\Env;
use think\facade\View;
// 默认json返回格式
function res($data, $msg, $status){
    return [
                'data:' =>  $data,
                'meta'  =>  [
                    'msg'   =>     $msg,
                    'status' => $status
                ]
    ];
}
// 获取到网站的配置信息
function web_options () {
    return \think\Db::name('options')->find();
}
// 获取到当前登陆用户的个人信息
function getUserInfo () {
    return UserModel::name('user')->where('username',Cookie::get('user')['username'])->find();
}
// 获取到文章的总数
function getArticleTotal () {
    return ContentsModel::name('contents')->where('state',true)->where('type','post')->count();
}
// 获取分类总数
function getCateGoriesTotal() {
   return \think\Db::name('categories')->count();
}
// 获取评论总数
function getCommentsTotal() {
    return CommentsModel::name('comments')->where('state',true)->count();
}
// 获取文章浏览的总数
function getBrowseTotal () {
    return CommentsModel::name('contents')->sum('browse',true);
}
// 通用删除函数
function del($request,$table,$idName){
    $id = $request->post()['id'];
    $del =  \think\Db::name($table)->where($idName,$id)->delete();
    if($del == 0) {
        return res(null,'删除失败！',400);
    }
    return res(null,'删除成功！',200);
}
// 截取2个指定字符之间的内容
function getNeedBetween($kw1,$mark1,$mark2){
    $kw=$kw1;
    $st =stripos($kw,$mark1);
    $ed =stripos($kw,$mark2);
    if(($st==false||$ed==false)||$st>=$ed)
        return false;
    $kw=substr($kw,($st),($ed-$st + 1));
    return $kw;
}
// 通用修改状态函数
function editState ($request,$table,$idName){
    $result = $request->post();
    $state = $result['state'];
    $del =  \think\Db::name($table)->where($idName,$result['id'])->update([
        'state' =>  eval("return $state;")
    ]);
    if($del == 0) { 
        return res(null,'修改失败！',400);
    }
    return json(res($result,'修改成功！',200));
}

function Rulelayer($rule,$pid=0)
{
    $arr = array();
    foreach ($rule as $v) {
        if($v['pid'] == $pid) {
            $v['child']= Rulelayer($rule,$v['catid']);
            $arr[] = $v;
        }
    }
    return $arr;
}

function Rulelayers($rule,$pid=0)
{
    $arr = array();
    foreach ($rule as $v) {
        if($v['pid'] == $pid) {
            $v['child']= Rulelayers($rule,$v['cid']);
            $arr[] = $v;
        }
    }
    return $arr;
}

/**
 * 系统邮件发送函数
 * @param string $tomail 接收邮件者邮箱
 * @param string $name 接收邮件者名称
 * @param string $subject 邮件主题
 * @param string $body 邮件内容
 * @param string $attachment 附件列表
 * @return boolean
 * @author static7 <static7@qq.com>
 */
function send_mail($tomail, $name, $subject = '', $body = '', $attachment = null) {
    $EmailInfo = include Env::get('config_path').'Email.php';
    $mail = new \PHPMailer\PHPMailer\PHPMailer;           //实例化PHPMailer对象
    $mail->CharSet = 'UTF-8';           //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP();                    // 设定使用SMTP服务
    $mail->SMTPDebug = 0;               // SMTP调试功能 0=关闭 1 = 错误和消息 2 = 消息
    $mail->SMTPAuth = true;             // 启用 SMTP 验证功能
    $mail->SMTPSecure = $EmailInfo['SMTPSecure'];          // 使用安全协议
    $mail->Host = $EmailInfo['Host']; // SMTP 服务器
    $mail->Port = $EmailInfo['Port'];                  // SMTP服务器的端口号
    $mail->Username = $EmailInfo['Username'];    // SMTP服务器用户名
    $mail->Password = $EmailInfo['Password'];     // SMTP服务器密码
    $mail->SetFrom($mail->Username, $EmailInfo['Name']);
    $replyEmail = '';                   //留空则为发件人EMAIL
    $replyName = '';                    //回复名称（留空则为发件人名称）
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject = $subject;
    $mail->MsgHTML($body);
    $mail->AddAddress($tomail, $name);
    if (is_array($attachment)) { // 添加附件
        foreach ($attachment as $file) {
            is_file($file) && $mail->AddAttachment($file);
        }
    }
    return $mail->Send() ? true : $mail->ErrorInfo;
}