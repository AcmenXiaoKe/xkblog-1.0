<?php
namespace app\index\controller;

use app\admin\model\Contents as ContentsModel;
use app\admin\model\Comments as CommentsModel;
use think\Controller;
use app\admin\model\Categories as CategoriesModel;
use think\facade\Env;
use think\Request;

include Env::get('config_path').'TemplateConfig.php';

class Index extends Controller
{
    public function index(Request $request)
    {
        $templatePath = include Env::get('config_path').'siteconfig.php';
        // 最新文章
        // 获取最新文章
        $PaginateModel = ContentsModel::name('contents')->where('type','post')->paginate(10);
        $totalPage = ceil($PaginateModel->total() / 10);
        $data = $request->get();
        $size = array_key_exists('size',$data) ?  $data['size'] : 10;
        $page = array_key_exists('page',$data) ?  $data['page'] : 1;
        $article  =  ContentsModel::name('contents')->where('state',true)->where('type','post')->order("aid desc" )->limit(($page -1) * $size ,$size)->select();
        $Data = [];
        for ($i=0;$i< count((array)$article);$i++) {
            $catid =explode (",",$article[$i]['catid']);
            foreach ($catid as $key=>$item) {
                $CategoriesModel = CategoriesModel::name('categories')->where('catid',$item)->select();
            }
            $comments_total = CommentsModel::name('comments')->where('aid',$article[$i]['aid'])->count();
            $obj = [
                'aid'           =>  $article[$i]['aid'],
                'title'         =>  $article[$i]['title'],
                'release_date'  =>  $article[$i]['release_date'],
                'content'       =>  $article[$i]['content'],
                'crid'          =>  $article[$i]['catid'],
                'label'         =>  $article[$i]['label'],
                'browse'        =>  $article[$i]['browse'],
                'praise'        =>  $article[$i]['praise'],
                'top'           =>  $article[$i]['top'],
                'recommended'   =>  $article[$i]['recommended'],
                'status'        =>  $article[$i]['state'],
                'Categories'    =>  $CategoriesModel,
                'comments_total'=>  $comments_total,
                'cover'         =>  $article[$i]['cover'],
            ];
            array_push($Data,$obj);
        }
        $this->assign([
           'articleList'    =>  $Data,
            'totalPage'     =>  $totalPage,
            'page'          =>  $page,
        ]);
        return $this->fetch(TMPL_PATH.$templatePath['template'].'/index');
    }
   }
