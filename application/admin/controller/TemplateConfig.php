<?php


namespace app\admin\controller;


use think\Controller;
use think\facade\Env;
use think\Request;

class TemplateConfig extends Controller
{
    public function index()
    {
        $templatePath = include Env::get('config_path').'siteconfig.php';
        $templateList = [];
        $templateConfigData = [];
        $file = scandir('static/template/');
        unset($file[0]);
        unset($file[1]);
        foreach ($file as $key=>$item) {
            try {
                $fileStr = 'static/template/'.$item.'/package.php';
                $templateConfig = include $fileStr;
                if(strtolower($templateConfig['name']) == strtolower($templatePath['template'])) {
                    $templateConfig = [
                        'name'      =>  $templateConfig['name'],
                        'preview'   =>  $templateConfig['preview'],
                        'author'    =>  $templateConfig['author'],
                        'abstract'  =>  $templateConfig['abstract'],
                        'status'    =>  true
                    ];
                    $templateConfigData = include Env::get('config_path').'template/'.strtolower($templatePath['template']).'.php';

                }
                array_push($templateList,$templateConfig);
            } catch (\Exception $e) {

            }
        }
        rsort($templateList);
        $this->assign([
            'template'              =>  $templateList,
            'templateConfigData'    =>  $templateConfigData
        ]);
        return $this->fetch('/template-config');
    }
    public function edit(Request $request)
    {
        $result=  $request->post();
        $data = include Env::get('config_path').'siteconfig.php';
        $code = <<<INFO
<?php

return [
    'name'          =>      '{$data['name']}',
    'url'           =>      '{$data['url']}',
    'description'   =>      '{$data['description']}',
    'keywords'      =>      '{$data['keywords']}',
    'template'      =>      '{$result['name']}'
];
INFO;
        file_put_contents(Env::get('config_path').'siteconfig.php', $code);
        return res(null,'切换成功',200);
//        return redirect('/admin/template_config');
    }
    public function config(Request  $request)
    {
        $result = $request->post();
        $templatePath = include Env::get('config_path').'siteconfig.php';
        $data = include Env::get('config_path').'template/'.strtolower($templatePath['template']).'.php';
        $code = <<<INFO
<?php
{$result['str']}
INFO;
        file_put_contents(Env::get('config_path').'template/'.strtolower($templatePath['template']).'.php', $code);
        return res(null,'保存成功',200);
    }
}