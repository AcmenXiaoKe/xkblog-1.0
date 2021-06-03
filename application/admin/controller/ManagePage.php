<?php


namespace app\admin\controller;


use app\admin\model\Contents as ContentsModel;
use think\Controller;
use think\Request;

class ManagePage extends Controller
{
    public function index()
    {
        $data = ContentsModel::name('contents')->where('type','page')->order('aid desc')->paginate(10);

        $this->assign('data',$data);
        return $this->fetch('/manage-page');
    }
    public function edit(Request $request){
        return  editState($request,'contents','aid');
    }



}