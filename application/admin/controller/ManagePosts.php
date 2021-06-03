<?php


namespace app\admin\controller;


use think\Controller;
use app\admin\model\Contents as ContentsModel;
use app\admin\model\Categories as CategoriesModel;
use think\Request;

class ManagePosts extends Controller
{
    public function index(){
        $data = ContentsModel::name('contents')->where('type','post')->order('aid desc')->paginate(10);

         $this->assign('data',$data);
         return $this->fetch('/manage-posts');
    }
    public function edit(Request $request){
        return  editState($request,'contents','aid');
    }
}

//        $categories_name_List = [];
//        for ($i=0; $i< count((array)$data);$i++) {
//            $categories_name = CategoriesModel::name('categories')->where('crid',$data[$i]['crid'])->find();
//            array_push($categories_name_List,$categories_name);
//        }
//         $this->assign('categories_name_List',$categories_name_List);