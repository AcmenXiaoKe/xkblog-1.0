<?php


namespace app\admin\controller;


use think\Controller;
use think\Db;
use think\Request;
use app\admin\model\Categories as CategoriesModel;

class Category extends Controller
{
    public function index(Request $request)
    {
        $catid = $request->get() ? $request->get()['catid'] : null;
        $operation = $catid ? 'update' : 'insert';
        $data = CategoriesModel::name('categories')->where('catid', $catid)->find();
        $CategoriesData =  CategoriesModel::name('categories')->select();
        $this->assign([
            'CategoriesData'   =>  $CategoriesData,
            'operation'        =>  $operation,
            'data'             =>    $data
        ]);
        return $this->fetch('/category');
    }

    public function handle(Request $request)
    {
        $result = $request->post();
        $CategoriesModel = CategoriesModel::name('categories');
        if ($result["operation"] == "update") {
            $CategoriesModel->where('catid', $result['catid'])->update([
                'catname' => $result['catname'],
                'pid'     => $result['pid'],
            ]);
            return res($result, '修改成功！', 200);
        }
        if ($result['operation'] == "insert") {
            $find = $CategoriesModel->where('catname', $result['catname'])->find();
            if ($find) {
                return res(null, '分类已存在！', 400);
            }
            $CategoriesModel->insert([
                'catname' => $result['catname'],
                'pid'     => $result['pid'],
            ]);
            return res(null, '添加成功！', 200);
        }

    }

    public function del(Request $request)
    {
      return  del($request, 'categories', 'catid');
    }
}