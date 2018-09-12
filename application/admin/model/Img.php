<?php
namespace app\admin\model;
use think\Model;
use traits\model\SoftDelete;
use extend\UploadImg;

/**
 * Created by PhpStorm.
 * User: yiming
 * Date: 18-9-8
 * Time: 下午3:57
 */
class Img extends Model{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $readonly = [];//只读字段

    public function select_img($data,$where=[]){
        if(isValue($data,'status')){
            $where['status'] =$data['status'];
        }
        if(isValue($data,'type')){
            $where['type'] =$data['type'];
        }
        $query = $data;
        $list  = $this->where($where)->order('id asc')->paginate('10',false,['query' => $query]);
        resultToArray($list);
        return $list;
    }

    public function add_img($data){
        if(isset($data['uploadImg'])){
            $year = date('Y/m',time());
            $re   = UploadImg::upload("banners/$year")->getMsg();
            $data['img'] = "/banners/".$year."/".$re['info']['image'][0];
            unset($data['uploadImg']);
        }
        $result = $this->save($data);
        if($result === false){
            return $this->getError();
        }else{
            return $result;
        }
    }

    public function edit_img($data){
        if(isset($data['uploadImg'])){
            $year = date('Y/m',time());
            $re   = UploadImg::upload("banners/$year")->getMsg();
            $data['img'] = "/banners/".$year."/".$re['info']['image'][0];
            unset($data['uploadImg']);
        }
        $result = $this->save($data,['id'=>$data['id']]);
        if($result === false){
            return $this->getError();
        }else{
            return $result;
        }
    }
}