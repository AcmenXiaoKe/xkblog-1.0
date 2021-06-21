<?php


namespace app\index\controller;


use think\Controller;
use app\admin\model\Contents as ContentsModel;
use app\admin\model\Comments as CommentsModel;
use app\admin\model\Categories as CategoriesModel;
use think\facade\Env;
use think\Request;
use app\admin\model\User as UserModel;

class Article extends Controller
{
    public function index($id){
        $templatePath = include Env::get('config_path').'siteconfig.php';
        $url = \think\facade\Request::url(true);
        $data = ContentsModel::name('contents')->where('aid',$id)->where('state',true)->where('type','post')->find();
        // 如果文章不存在就抛出异常
        if($data === null) {
            throw new \think\Exception('文章不存在！', 404);
        }
        $next   = ContentsModel::name('contents')->where('aid','>',$id)->where('type','post')->order('aid asc')->limit('1')->find();
        $previous  =    ContentsModel::name('contents')->where('aid','<',$id)->where('type','post')->order('aid desc')->limit('1')->find();
        $catid =explode (",",$data['catid']);
        $CategoriesData = [];
        foreach ($catid as $key=>$item) {
            $Categories =  CategoriesModel::name('categories')->where('catid',$item)->find();
            array_push($CategoriesData,$Categories);
        }
        $Comments = CommentsModel::name('comments')->where('aid',$id)->where('state',true)->select();
        $CommentsData = Rulelayers($Comments);
//        return json($CommentsData);
        $authorInfo = UserModel::name('user')->where('name',$data['author'])->find();
        $this->assign([
            'data'          =>      $data,
            'comments'      =>      $CommentsData,
            'comments_total'=>      count((array)$Comments),
            'Categories'    =>      $CategoriesData,
            'next'          =>      $next,
            'previous'      =>      $previous,
            'authorInfo'    =>      $authorInfo,
            'url'           =>      $url,
            'title'         =>      $data['title'],
        ]);
        ContentsModel::name('contents')->where('aid',$id)->where('type','post')->inc('browse',1)->update();
        return $this->fetch(TMPL_PATH.$templatePath['template'].'/article');

    }

    public function comments(Request $request){
        $data = $request->post();
        $insert =  CommentsModel::name('comments')->insert($data);
        if($data['pid'] !== "0") {
            $article = ContentsModel::name('contents')->where('aid',$data['aid'])->where('state',true)->where('type','post')->find();
            $replier =  CommentsModel::name('comments')->where('cid',$data['pid'])->find();
            $EmailInfo = include Env::get('config_path').'Email.php';
            $html = '<div style="margin: 200px auto;width: 500px;height: 100%;background: #F7F8FA;padding: 20px;box-sizing: border-box;">
        <div style="font-weight: 600;font-size: 1.2em;">'.$EmailInfo['Name'].'</div>
        <div style="height: 100%;background: #FFF;margin-top: 15px;border-top: 3px solid #00a4ff; padding: 15px;">
            <div><span style="font-size: 14px;color: #666;">'.$data['author'].'</span><span
                    style="font-size: 14px;margin-left: 15px;color: #666;">'.$data['email'].'</span></div>
            <div style="margin: 15px 0;line-height: 25px;">'.$data['content'].'</div>
        </div>
        <a href="'.$request->scheme().'://'.$request->host().'/article/'.$data['aid'].'"
            style="text-decoration: none;display: block;height: 35px;background: #00a4ff; text-align: center; line-height: 35px;color: #fff;border-radius: 3px;margin-top: 15px;">查看原文</a>
    </div>';
           return json(send_mail($replier['email'],$data['author'],$data['author'].'回复了你在《'.$article['title'].'》'.'下的留言',$html));
        }
        return res(null,'评论成功！',200);
    }
    public function praise(Request $request){
        $data = $request->post();
        $updateArticle =  ContentsModel::name('contents')->where('aid',$data['id'])->inc('praise',1)->update();
        if($updateArticle) {
            return res(null,'点赞成功！',200);
        }
        return res(null,'点赞成失败！',500);


    }

}