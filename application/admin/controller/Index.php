<?php


namespace app\admin\controller;


use app\admin\model\Contents as ContentsModel;
use think\Controller;
use think\facade\Cookie;
use app\admin\model\Comments as CommentsModel;
use app\admin\model\Link as LinkModel;

class Index extends Controller
{
    public function index()
    {
        // 获取官方公告
        $notice_url = "http://gw.xkbk.top/notice";
        $notice = json_decode($this->curl_get($notice_url),true);
        // 获取最新文章
        $article  =   ContentsModel::name('contents')->limit(10)->order("aid desc")->select();
        //获取最新评论
        $comments =    ContentsModel::name('comments')->limit(10)->order("cid desc")->select();
        // 待审评论
        $pdg_comments_total = CommentsModel::name('comments')->where('state',false)->count();
        // 文章草稿箱
        $pfg_article  =   ContentsModel::name('contents')->where('state',false)->count();
        $this->assign([
            'article_total'         =>  getArticleTotal(),
            'browse_total'          =>  getBrowseTotal(),
            'comments_total'        =>  getCommentsTotal(),
            'categories_total'      =>  getCateGoriesTotal(),
            'article'               =>  $article,
            'comments'              =>  $comments,
            'pdg_comments_total'    =>  $pdg_comments_total,
            'pfg_article'           =>  $pfg_article,
            'notice'                =>  $notice
        ]);
        return $this->fetch('/index');

    }
    protected function curl_get($url)
    {
        $info = curl_init();
        curl_setopt($info,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($info,CURLOPT_HEADER,0);
        curl_setopt($info,CURLOPT_NOBODY,0);
        curl_setopt($info,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($info,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($info,CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($info,CURLOPT_URL,$url);
        $output = curl_exec($info);
        curl_close($info);
        return $output;
    }
}