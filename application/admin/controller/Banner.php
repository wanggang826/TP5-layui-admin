<?php
namespace app\admin\controller;
/**
 * Created by PhpStorm.
 * User: yiming
 * Date: 18-9-8
 * Time: 下午3:36
 */
class Banner extends AdminBase{
    public function index(){
        $data = input();
        $lists = model('img')->select_img($data);
        return view('',['lists'=>$lists]);
    }

    public function add(){
        if(request()->isAjax()){
            $data = input();
            $result = model('Img')->add_img($data);
            if($result > 0){
                Api()->setApi('url',url('index'))->ApiSuccess();
            }else{
                Api()->setApi('msg',$result)->setApi('url',0)->ApiError();
            }
        }
        return view();
    }

    public function adds(){
        if(request()->isAjax()){
            $data = input();
            if(isset($data['uploadImg'])){
                $result =  $this->saveAllImg($data,$data['uploadImg']);
            }else{
                $result = model('Img')->add_img($data);
            }
            if($result > 0){
                Api()->setApi('url',url('index'))->ApiSuccess();
            }else{
                Api()->setApi('msg',$result)->setApi('url',0)->ApiError();
            }
        }
        return view();
    }

    public function edit(){
        $data = input();
        $info = model('img')->where('id',input('id'))->find();
        if(request()->isAjax()){
            $result = model('img')->edit_img($data);
            if($result > 0){
                Api()->setApi('url',url('index'))->ApiSuccess();
            }else{
                Api()->setApi('msg',$result)->setApi('url',0)->ApiError();
            }
        }
        return view('',['info'=>$info]);
    }

    public function showImg()
    {
        $banner = model('img')->where('id',input('id'))->value('img');
        return view('',['banner'=>$banner]);
    }

    public function changeStatus(){
        $status = input('status');
        $id = input('id');
        $re = $this->setStatus('img', $status, $id, 'id', 'status');
        if ($re->code == 1) {
            Api()->setApi('url', url('index'))->ApiSuccess($re);
        } else {
            Api()->setApi('url', 0)->ApiError();
        }
    }

    public function changeType(){
        $type = input('type');
        $id = input('id');
        $re = $this->setStatus('img', $type, $id, 'id', 'type');
        if ($re->code == 1) {
            Api()->setApi('url', url('index'))->ApiSuccess($re);
        } else {
            Api()->setApi('url', 0)->ApiError();
        }
    }

    public function del(){
        $time = time();
        $data = input();
        $obj  = $this->setStatus('img',$time,$data['id'],'id','delete_time');
        if(1 == $obj->code){
            Api()->setApi('url',input('location'))->ApiSuccess();
        }else{
            Api()->setApi('url',0)->ApiError();
        }
    }

    public function uploadImg($base64_img){
        if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_img, $result)){
            $type = $result[2];
            if(in_array($type,array('pjpeg','jpeg','jpg','gif','bmp','png'))){
                $path = '/banners/'.date('Y',time()).'/'.date('m',time());
                $save_path = './upload'.$path;
                if(!is_dir($save_path)){
                    mkdir($save_path,0777,true);
                }
                $save_name = md5(time().getRandCode(2));
                $new_file = $save_path.'/'.$save_name.'.'.$type;
                if(file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_img)))){
                    return array('code' => 1, 'msg' => "图片上传成功", 'imgUrl' => $path.'/'.$save_name.'.'.$type);
                }
                return array('code' => 2, 'msg' => "图片上传失败");
            }
            //文件类型错误
            return array('code' => 4, 'msg' => "文件类型错误");
        }
        //文件错误
        return array('code' => 3, 'msg' => "文件错误");
    }

    public function saveAllImg($data,$img_list){
        $new_data = [];$i=0;
        foreach ($img_list['image'] as $k => $v) {
            $img = $this->uploadImg($v);
            $new_data[$i]['title'] = $data['title'].$i;
            $new_data[$i]['img']   = $img['imgUrl'];
            $new_data[$i]['url']   = $data['url'];
            $new_data[$i]['des']   = $data['des'];
            $new_data[$i]['status']= $data['status'];
            $new_data[$i]['type']  = $data['type'];
            $i++;
        }
        return model('img')->saveAll($new_data);
    }

}