<?php


namespace app\admin\controller;


use app\admin\model\Comments as CommentsModel;
use think\Controller;
use think\Request;
use app\admin\model\Contents as  ContentsModel;

class WritePage extends Controller
{
    public function index(Request $request)
    {
        $aid = $request->get() ? $request->get()['aid'] : null;
        $operation = $aid ? 'update' : 'insert';
        $this->assign([
           'operation'  =>  $operation
        ]);
        return $this->fetch('/write-page');
    }
    public function handle(Request $request)
    {
        $result = $request->post();
        $Data = [
            'title'            =>  $result['title'],
            'author'           =>  $result['author'],
            'content'          =>  $result['content'],
            'release_date'     =>  $result['release_date'],
            'browse'           =>  array_key_exists('browse',$result) ? $result['browse'] : 0,
            'praise'           =>  array_key_exists('praise',$result) ? $result['praise'] : 0,
            'state'           =>  array_key_exists('state',$result) ? $result['state'] : true,
            'type'            =>  $result['type'],
        ];
        if($result['operation'] == 'update') {
            $create = ContentsModel::name('contents')->where('aid',$result['aid'])->update($Data);

        }
        if($result['operation'] == 'insert') {
            $create = ContentsModel::name('contents')->insert($Data);
        }
        if($create == 0) {
            return json(res(null,'发布失败！',400));
        }
        return json(res(null,'发布成功！',200));
    }
    public function del(Request $request){
        $id = $request->post()['id'];
        $del_comments = CommentsModel::name('comments')->where('aid',$id)->delete();
        $del =  ContentsModel::name('contents')->where('aid',$id)->delete();
        if($del == 0) {
            return res(null,'删除失败！',400);
        }
        return res(null,'删除成功！',200);
    }

}