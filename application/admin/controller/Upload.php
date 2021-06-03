<?php


namespace app\admin\controller;

use think\Controller;
use think\facade\Request;


class Upload extends Controller
{
    public function index(){
        if(request()->isPost()) {
            // 获取上传的文件 image 表单 name 名
            $file = Request::file('editormd-image-file');
            // 文件存储位置
            $info = $file->move('../public/uploads');
            // 判断上传信息
            if ($info) {
//
                return json([
                    'success' => 1,
                    'message' => '上传成功！',
                    'url' => str_replace('\\', '/', Request::domain() . '/uploads/' .  $info->getSaveName())
                ]);
            } else {
                // 上传失败获取错误信息
                echo $file->getError();
            }
        } else {
            return '非法请求！';
        }

    }

}