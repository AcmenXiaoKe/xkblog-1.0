<?php

namespace app\http\middleware;

use app\admin\model\Contents as ContentsModel;
use app\admin\model\Label as LabelModel;
use think\Controller;
use think\Db;
use think\exception\ErrorException;
use think\facade\Cookie;
use think\facade\Env;
use think\facade\Session;
use app\admin\model\Comments as CommentsModel;
use app\admin\model\Categories as CategoriesModel;

class Check extends Controller
{

    public function handle($request, \Closure $next)
    {
        if($request->controller() == 'Install') {
            return  $next($request);
        }
        if ($request->module() == 'install') {
                return $next($request);
        }
        if(!file_exists('install.lock')) {
            return redirect('/install');
        } else {
            $web_options = include Env::get('config_path').'siteconfig.php';
            // 判断是否是登陆页面的 控制器 如果是 直接返回
            if($request->controller() == 'Login') {
                return  $next($request);
            }
            // 判断当前模块是否是 index
            if ($request->module() == 'index') {
                $templatePath = include Env::get('config_path').'siteconfig.php';
                $TemplateConfig = include Env::get('config_path').'template/'.strtolower($templatePath['template']).'.php';
                $labelDate  = LabelModel::name('label')->select();
                $NewComments = CommentsModel::name('comments')->limit(5)->order("cid desc")->select();
                $this->assign('web_options',$web_options);
                // 获取热门文章
                $hot = ContentsModel::name('contents')->where('state',true)->where('type','post')->order('browse desc')->limit(3)->select();
                // 获取随机文章
                $randomArticle  = [];
                $allArticle = ContentsModel::name('contents')->where('state',true)->where('type','post')->select();

                for($i=0; $i  < 4;$i++) {
                    try {
                        array_push($randomArticle,$allArticle[(mt_rand(0, getArticleTotal() - 1) )]);

                    } catch (ErrorException $e) {

                    }


                }
                // 获取页面
                $pages = ContentsModel::name('contents')->where('state',true)->where('type','page')->select();
                // 获取分类
              //  $categories = CategoriesModel::name('categories')->select();
                // 公共的变量
                $categoriesData = Db::name('categories')->select();
                $categories = Rulelayer($categoriesData);
                $this->assign([
                    'article_total'    =>  getArticleTotal(),
                    'public_comments_total'   =>  getCommentsTotal(),
                    'name'             =>  Db::name('user')->where('permissions' ,'Super')->find()['name'],
                    'email'            =>  Db::name('user')->where('permissions' ,'Super')->find()['email'],
                    'labelDate'        =>  $labelDate,
                    'NewComments'      =>  $NewComments,
                    'hot'              =>  $hot,
                    'randomArticle'    =>  $randomArticle,
                    'TemplateConfig'   =>  $TemplateConfig,
                    'categories'       =>  $categories,
                    'categoriesData'  =>   $categoriesData,
                    'pages'            =>   $pages
                ]);
            }
            // 判断当前模块是否是 admin
            if($request->module() == 'admin') {
                // 公共的变量
                $this->assign([
                    'name'  =>  getUserInfo()['name'],
                    'title' =>  $web_options['name']
                ]);
                // 判断 user 字段的 cookie 存在 通过
                if(Cookie::get('user') ) {
                    return  $next($request);
                }
                // 如果不存在 直接跳到登陆页面
                return redirect('/admin/login');
            }
            // 默认让他通过
            return  $next($request);

        }
    }
}