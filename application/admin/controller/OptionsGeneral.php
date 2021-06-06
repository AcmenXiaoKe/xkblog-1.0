<?php


namespace app\admin\controller;


use think\Controller;
use think\Db;
use think\facade\Env;
use think\Request;

class OptionsGeneral extends Controller
{
    public function index(){

        $web_options = include Env::get('config_path').'siteconfig.php';
        $this->assign('options',$web_options);
       return $this->fetch('/OptionsGeneral');
    }
    public function edit(Request  $request){
        $data  = $request->post();
        $web_options = include Env::get('config_path').'siteconfig.php';
        $code = <<<INFO
<?php

return [
    'name'          =>      '{$data['name']}',
    'url'           =>      '{$data['url']}',
    'description'   =>      '{$data['description']}',
    'keywords'      =>      '{$data['keywords']}',
    'template'      =>      '{$web_options['template']}'
];
INFO;
        file_put_contents(Env::get('config_path').'siteconfig.php', $code);
         return redirect('/admin/options_general');

    }

}