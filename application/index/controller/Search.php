<?php


namespace app\index\controller;


use app\admin\model\Categories as CategoriesModel;
use app\admin\model\Comments as CommentsModel;
use think\Controller;
use think\facade\Env;
use think\Request;
use app\admin\model\Contents as ContentsModel;

class Search extends Controller
{
    public function index($val){
        $templatePath = include Env::get('config_path').'siteconfig.php';
      $data = ContentsModel::name('contents')->where('state',true)->where('type','post')->where('content','like',('%'.$val.'%'))->select();
      $Data = [];
       for ($i=0;$i< count((array)$data);$i++) {
           $catid =explode (",",$data[$i]['catid']);
           foreach ($catid as $key=>$item) {
               $Categories = CategoriesModel::name('categories')->where('catid',$item)->select();
           }
            $comments_total = CommentsModel::name('comments')->where('aid',$data[$i]['aid'])->count();
            $obj = [
                'aid'           =>  $data[$i]['aid'],
                'title'         =>  $data[$i]['title'],
                'release_date'  =>  $data[$i]['release_date'],
                'content'       =>  $data[$i]['content'],
                'label'         =>  $data[$i]['label'],
                'browse'        =>  $data[$i]['browse'],
                'praise'        =>  $data[$i]['praise'],
                'top'           =>  $data[$i]['top'],
                'recommended'   =>  $data[$i]['recommended'],
                'status'        =>  $data[$i]['state'],
                'categories'    =>  $Categories,
                'comments_total'=>  $comments_total,
                'cover'         =>  $data[$i]['cover']
            ];
            array_push($Data,$obj);
        }
      $this->assign([
          'data'        =>      $Data,
          'val'         =>      $val,
          'data_total'   =>  count((array)$data)
      ]);
      return  $this->fetch(TMPL_PATH.$templatePath['template'].'/search');
    }
    public function category($id){
        $templatePath = include Env::get('config_path').'siteconfig.php';
        $Categories_data = CategoriesModel::name('categories')->where('catid',$id)->find();
        $data = ContentsModel::name('contents')->where('state',true)->where('type','post')->where('catid','like',('%'.$id.'%'))->select();
        $Data = [];
        for ($i=0;$i< count((array)$data);$i++) {
            $catid =explode (",",$data[$i]['catid']);
            foreach ($catid as $key=>$item) {
                $Categories = CategoriesModel::name('categories')->where('catid',$item)->select();
            }
            $comments_total = CommentsModel::name('comments')->where('aid',$data[$i]['aid'])->count();
            $obj = [
                'aid'           =>  $data[$i]['aid'],
                'title'         =>  $data[$i]['title'],
                'release_date'  =>  $data[$i]['release_date'],
                'content'       =>  $data[$i]['content'],
                'label'         =>  $data[$i]['label'],
                'browse'        =>  $data[$i]['browse'],
                'praise'        =>  $data[$i]['praise'],
                'top'           =>  $data[$i]['top'],
                'recommended'   =>  $data[$i]['recommended'],
                'status'        =>  $data[$i]['state'],
                'categories'    =>  $Categories,
                'comments_total'=>  $comments_total,
                'cover'         =>  $data[$i]['cover']
            ];
            array_push($Data,$obj);
        }
        $this->assign([
            'data'        =>      $Data,
            'val'         =>      $Categories_data['catname'],
            'data_total'   =>  count((array)$data)
        ]);
        return  $this->fetch(TMPL_PATH.$templatePath['template'].'/search');
    }
    public function tag($val){
        $templatePath = include Env::get('config_path').'siteconfig.php';
        $Categories_data = CategoriesModel::name('label')->where('name',$val)->find();
        $data = ContentsModel::name('contents')->where('state',true)->where('type','post')->where('label','like',('%'.$val.'%'))->select();
        $Data = [];
        for ($i=0;$i< count((array)$data);$i++) {
            $catid =explode (",",$data[$i]['catid']);
            foreach ($catid as $key=>$item) {
                $Categories = CategoriesModel::name('categories')->where('catid',$item)->select();
            }
            $comments_total = CommentsModel::name('comments')->where('aid',$data[$i]['aid'])->count();
            $obj = [
                'aid'           =>  $data[$i]['aid'],
                'title'         =>  $data[$i]['title'],
                'release_date'  =>  $data[$i]['release_date'],
                'content'       =>  $data[$i]['content'],
                'label'         =>  $data[$i]['label'],
                'browse'        =>  $data[$i]['browse'],
                'praise'        =>  $data[$i]['praise'],
                'top'           =>  $data[$i]['top'],
                'recommended'   =>  $data[$i]['recommended'],
                'status'        =>  $data[$i]['state'],
                'categories'    =>  $Categories,
                'comments_total'=>  $comments_total,
                'cover'         =>  $data[$i]['cover']
            ];
            array_push($Data,$obj);
        }
        $this->assign([
            'data'        =>      $Data,
            'val'         =>      $Categories_data['name'],
            'data_total'   =>  count((array)$data)
        ]);
//       return  json($Data);
        return  $this->fetch(TMPL_PATH.$templatePath['template'].'/search');
    }
}