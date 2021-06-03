<?php


namespace app\admin\controller;


use app\admin\model\Contents as ContentsModel;
use think\Controller;
use think\Db;
use app\admin\model\Categories as CategoriesModel;

class ManageCategories extends Controller
{
    public function index()
    {
        $data = CategoriesModel::name('categories')->select();
        $Data = [];
        for($i=0; $i < count((array)$data); $i++) {
            $total = ContentsModel::name('contents')->where('catid','like',('%'.$data[$i]['catid'].'%'))->count();
            $Obj = [
                'catid'  =>  $data[$i]['catid'],
                'name'  =>  $data[$i]['catname'],
                'total' =>  $total
            ];
            array_push($Data,$Obj);
        }

      $this->assign('data',$Data);
        return $this->fetch('/manage-categories');
    }
}