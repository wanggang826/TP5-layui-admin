<?php
namespace extend;
use think\Config;
use think\Request;

/**
*
*/
class Upload
{
    private static $uploadPath,$msg,$info,$imgInfo;
    private static function _init($config){

    }

/*
 * 上传图片
 * @param  str   $uploadPath 上传路径统一在upload 下面;
 * */
    public static function uploadImg($uploadPath=''){
        $img_config= Config::get('upload');
        $size = $img_config['maxSize'];//配置文件要求的图片最大值
        $types = explode(',',$img_config['exts']);//配置文件要求的图片后缀
        $request = Request::instance()->param(true);
        if($request){
            $img = $request['uploadImg'];
        }
        if (!isset($img)) {//检测图片数据
            if($_FILES) {
                foreach ($_FILES as $key => &$val) {
                    $files[$key] = self::UploadFile($key, $uploadPath);
                }
                self::_setInfo($files);
            }else{
                self::_setMsg('数据格式错误');
                return false;
            }
        }else{
            foreach ($img as $k =>$v){
                foreach ($v as $key => $val) {
                    $base64_img = $val;
                    $up_dir = "./public/upload/$uploadPath/";//存放目录public/upload文件夹下
                    if (!file_exists($up_dir)) {
                        mkdir($up_dir, 0777, true);
                    }
                    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_img, $result)) {//匹配base64编码
                        $type = $result[2];//图片格式
                        if (in_array($type, $types)) {//遍历图片格式
                            $length = strlen(file_get_contents($base64_img));
                            if ($length <= $size) {
                                $new_file = $up_dir . date(time())."_" . $k . $key . '.' . $type;  //time()时间戳拼接数组的键作为图片名字 ,$key不能删除,用于反馈图片位置.
                                if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_img)))) {//图片上传成功
                                    $imgs[] = str_replace("$up_dir", '', $new_file);//用作保存的图片路径和名字
                                }else{
                                    self::_setMsg('图片上传失败!');
                                    return false;
                                }
                            } else {
                                self::_setMsg('图片大小不正确!');
                                return false;
                            }
                        } else {
                            self::_setMsg('图片格式不正确!');
                            return false;
                        }
                    }
                }
            }
            self::_setInfo($imgs);
        }
        return new self();
    }

    /*
     * file上传文件/图片
     * */
    public static  function UploadFile($filename,$uploadPath=''){
        $file = request()->file($filename);
        if(is_array($file)){
            foreach($file as $file){
                $info = $file->move(ROOT_PATH . 'public/upload' . DS . $uploadPath);
                if($info){
                    $images[] =$info->getSaveName();
                }else{
                    return false;
                }
            }
            return $images;
        }else{
            $info = $file->move(ROOT_PATH . 'public/upload' . DS . $uploadPath);
            if($info){
                return $info->getSaveName();
            }else{   
                return false;
            }
        }
    }

     /*
     * file上传文件/图片
     * 单文件多张/单张上传
     * */
    public static  function UploadFileOne($filename,$uploadPath=''){
        $file = request()->file($filename);
        if(is_array($file)){
            foreach($file as $file){
                $info = $file->move(ROOT_PATH . 'public/upload' . DS . $uploadPath);
                if($info){
                    $images = $info->getSaveName();
                }else{
                    return false;
                }
            }
            return $images;
        }else{
            $info = $file->move(ROOT_PATH . 'public/upload' . DS . $uploadPath);
            if($info){
                return $info->getSaveName();
            }else{   
                return false;
            }
        }
    }

    public static function move_uploaded_file($filename,$uploadPath=''){
        $file = request()->file($filename);
        $info = $file->move(ROOT_PATH . 'public/upload' . DS . $uploadPath);
        if($info){
            return $info->getSaveName();
        }else{   
            return false;
        }
    }
    
    /*
     * 调路径
     * */
    public static function getInfo(){
        return self::$info;
    }

    /*
     * 状态信息
     * */
    private static function _setMsg($msg){
        self::$msg = $msg;
        return true;
    }
    /*
     * 存路径
     *
     * */
    private static function _setInfo($info){
        self::$info = $info;
        return true;
    }

}