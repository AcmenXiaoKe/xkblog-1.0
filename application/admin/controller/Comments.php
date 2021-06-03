<?php


namespace app\admin\controller;


use think\Controller;
use app\admin\model\Comments as CommentsModel;
use think\Request;

class Comments extends Controller
{
    public function index()
    {
        $data = CommentsModel::name('comments')->paginate(10);
        $this->assign('data',$data);
        return $this->fetch('/comments');
    }
    public function del(Request $request)
    {
        return  del($request, 'comments', 'cid');
    }
    public function edit(Request $request){
    return    editState($request,'comments','cid');

    }
}