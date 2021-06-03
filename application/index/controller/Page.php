<?php


namespace app\index\controller;


use app\admin\model\Categories as CategoriesModel;
use app\admin\model\Comments as CommentsModel;
use app\admin\model\User as UserModel;
use think\Controller;
use think\facade\Env;
use think\Request;
use app\admin\model\Contents as ContentsModel;

class Page extends Controller
{
    public function index($aid)
    {
        $templatePath = include Env::get('config_path').'siteconfig.php';
        $url = \think\facade\Request::url(true);
        $data  = ContentsModel::name('contents')->where('aid',$aid)->where('type','page')->where('state',true)->find();
        // 如果文章不存在就抛出异常
        if($data === null) {
            throw new \think\Exception('文章不存在！', 404);
        }
        $catid =explode (",",$data['catid']);
        $CategoriesData = [];
        foreach ($catid as $key=>$item) {
            $Categories =  CategoriesModel::name('categories')->where('catid',$item)->find();
            array_push($CategoriesData,$Categories);
        }
        $Comments = CommentsModel::name('comments')->where('aid',$aid)->where('state',true)->select();
        $CommentsData = Rulelayers($Comments);
//        return json($CommentsData);
        $authorInfo = UserModel::name('user')->where('name',$data['author'])->find();
        $this->assign([
            'data'          =>      $data,
            'comments'      =>      $CommentsData,
            'comments_total'=>      count((array)$Comments),
            'Categories'    =>      $CategoriesData,
            'authorInfo'    =>      $authorInfo,
            'url'           =>      $url,
            'title'         =>      $data['title'],
        ]);
        ContentsModel::name('contents')->where('aid',$aid)->where('type','post')->inc('browse',1)->update();
        return $this->fetch(TMPL_PATH.$templatePath['template'].'/article');
    }

}