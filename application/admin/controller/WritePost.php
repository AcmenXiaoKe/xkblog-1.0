<?php


namespace app\admin\controller;


use app\admin\model\Contents as ContentsModel;
use think\Controller;
use think\Db;
use think\Request;
use app\admin\model\Label as LabelModel;
use app\admin\model\Comments as CommentsModel;


class WritePost extends Controller
{
    public function index(Request $request){
      $categories_list = Db::name('categories')->select();
      $aid = $request->get() ? $request->get()['aid'] : null;
      $operation = $aid ? 'update' : 'insert';

      if($operation == 'update') {
          $data = ContentsModel::name('contents')->where('aid',$aid)->find();
          $is_categoriest = explode (",",$data['catid']);;
          $label = explode('|',$data['label'])[0] == ''  ? null  : explode('|',$data['label']) ;
          $this->assign([
              'article_title'   =>  $data['title'],
              'content' =>  $data['content'],
              'label'   =>  $label,
              'release_date'   =>   $data['release_date'],
              'aid'            =>   $aid
          ]);
          // 处理是否选中的分类
          foreach ($categories_list as $key=>$item) {
              foreach($is_categoriest as $key2=>$item2) {
                  if($item['catid'] == $item2) {
                      $categories_list[$key] = [
                          'catid'   =>  $item['catid'],
                          'pid'     =>  $item['pid'],
                          'catname' =>  $item['catname'],
                          'flag'    =>  true
                      ];
                      break;
                  }
              }
          }
      }
        $this->assign([
            'operation'  =>  $operation,
            'categories_list'  =>  $categories_list
        ]);
      return  $this->fetch('/write-post');
    }
    public function create(Request $request){
        $data = $request->post();
        $is_label = array_key_exists('label',$data) ?  $data['label'] : null;
        $mark1 =  getNeedBetween($data['content'],'![](',')');
        $mark2 =  getNeedBetween($mark1,'(',')');
        $cover =   substr(rtrim($mark2, ")"), 1);
        $Date = [
            'title'            =>  $data['title'],
            'author'           =>  $data['author'],
            'content'          =>  $data['content'],
            'release_date'     =>  $data['release_date'],
            'catid'            =>  array_key_exists('catid',$data) ?  $data['catid'] : null,
            'label'            =>  $is_label == '' ? null : $is_label,
            'browse'           =>  array_key_exists('browse',$data) ? $data['browse'] : 0,
            'praise'           =>  array_key_exists('praise',$data) ? $data['praise'] : 0,
            'top'              =>  array_key_exists('top',$data) ? $data['top'] : false,
            'recommended'     =>  array_key_exists('recommended',$data) ? $data['recommended'] : false,
            'state'           =>  array_key_exists('state',$data) ? $data['state'] : true,
            'cover'            =>   $cover ? $cover : null
        ];
         if($data['operation'] == 'update') {
             $create = ContentsModel::name('contents')->where('aid',$data['aid'])->update($Date);

         }
         if($data['operation'] == 'insert') {
             $create = ContentsModel::name('contents')->insert($Date);
         }

        if($create == 0) {
            return json(res($Date,'发布失败！',400));
        }
        $label = explode('|',$Date['label']);
        $label =   $label[0] != '' ? $label : [];
        // 判断是否添加了标签
        if($label != 0) {
            $find = [];
            // 添加了标签判断一下判断是否已经存在
            for ($i = 0; $i < count((array)$label)  ; $i++) {
                $find = LabelModel::name('label')->where('name',$label[$i])->find();
                // 不存在进行添加标签
                if(count((array)$find) == 0 ) {
                    $labelDate = [
                        'name'  =>  $label[$i]
                    ];
                    LabelModel::name('label')->insert($labelDate);
                }
            }
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