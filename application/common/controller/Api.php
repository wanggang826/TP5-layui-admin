<?php
namespace app\common\controller;
use think\Request;
/**
 * Api基础类
 * by wanggang 2017-05-03
 * $api = new Api();
 * $api->setType('array');
 * 未设置code,调用方式，
 * $api->setApi('title','删除成功')->setApi('url',U('Index/index'))->ApiSuccess();//操作成功
 * $api->setApi('title','删除警告')->setApi('msg','该项目不能删除')->ApiWarning();//失败（警告）
 * $api->setApi('title','删除失败')->ApiError();//失败
 *
 *
 * $api->getApi();//失败
 * $api->setApi('code',0)->getApi();//失败
 * $api->setApi('code',1)->getApi();//成功
 *
 *
 */
class Api extends Base{
    private $code,$tip,$msg,$url,$page,$row,$data,$api,$time;
    private $apiArray  = ['code'=>0,'msg'=>'','url'=>'','wait'=>'3','data'=>[],];
    private $typeArray = ['array','object','json','this','return'];
    private $apiJson;
    private $apiObject;
    private $type = 'return';

    public function __construct($debug = false){
        $module_name     = Request::instance()->module();//当前模块
        $controller_name = Request::instance()->controller();//当前控制器
        $action_name     = Request::instance()->action();//当前方法
        $debug === true && $this->apiArray['api']  = url($module_name.'/'.$controller_name.'/'.$action_name,'',true,true);
        $debug === true && $this->apiArray['time'] = microtime(true);

    }

    public function __get($name){
        if (array_key_exists($name, $this->apiArray)) {
            return $this->apiArray[$name];
        } else {
            return false;
        }
    }
    // public function __set($name,$value){
    //     if (array_key_exists($name, $this->apiArray)) {
    //         $this->apiArray[$name] = $value;
    //     }
    //     return $this;
    // }

    /**
     * 设置数据类型
     * @param string $type 默认为数组，可选有array object json
     * @param bool $setAjax 强制设置为ajax
     */
    public function setType($type = 'return',$setAjax = false){
        $this->type = $type;
        $setAjax = (bool)$setAjax;
        $this->isAjax = $setAjax ?: request()->isAjax();
        return $this;
    }

    /**
     * 设置Api返回值
     * @param string $name 可选 code,msg,url,data,wait
     * code 0-失败，1-成功，2-警告
     * msg  提示的内容
     * url   跳转地址，为空默认刷新当前页
     * data  返回数据
     */
    public function setApi( $name, $value ){
        if (!array_key_exists($name, $this->apiArray) || $name == 'tip' || $name == 'time') return false;
        if ($name == 'code') $value = (int)$value;
        if ($name != 'data') $value = (string)$value;
        $this->apiArray[$name] = $value;
        return $this;
    }

    /**
     * 获取Api返回值
     * 默认为数组，可选有array object json
     */
    public function getApi($type = ''){
        return $this->setParam($type);
    }

    /**
     * 操作成功
     * @param array $data 要返回的数据
     */
    public function ApiSuccess($data=[]){
        $data = $data?:$this->apiArray['data'];
        $this->setApi('code',1)->setApi('data',$data);
        return $this->setParam();
    }

    /**
     * 操作失败
     * @param array $data 要返回的数据
     */
    public function ApiError($data=[]){
        $this->setApi('code',0)->setApi('data',$data);
        return $this->setParam();
    }

    /**
     * 操作警告
     * @param array $data 要返回的数据
     */
    public function ApiWarning($data=[]){
        $this->setApi('code',2)->setApi('data',$data);
        return $this->setParam();
    }

    /**
     * 格式化apiArray，生成json和object
     */
    private function setParam($type=''){
        $apiArray = &$this->apiArray;
        $type = $type ?: $this->type;
        $apiArray['code']   = (int)$apiArray['code'];
        $apiArray['url']    = $apiArray['url']  !== ''    ? $apiArray['url'] : '';

        switch ($apiArray['code']) {
            case '1':
                $apiArray['code']   = 1;
                $apiArray['msg']    = $apiArray['msg']   ?: '您的操作已成功处理';
                break;
            case '2':
                $apiArray['code']   = 2;
                $apiArray['msg']    = $apiArray['msg']   ?: '请谨慎操作！';
                break;
            default:
                $apiArray['code']   = 0;
                $apiArray['msg']    = $apiArray['msg']   ?: '操作失败';
                break;
        }
        $this->apiJson   = json_encode($apiArray);
        $this->apiObject = json_decode($this->apiJson);
        switch ($type) {
            case 'array':
                return $this->apiArray;
                break;
            case 'json':
                return $this->apiJson;
                break;
            case 'object':
                return $this->apiObject;
                break;
            case 'this':
                return $this;
            default:
                return $this->apiEcho();
                break;
        }

    }

    public function apiEcho($type=''){
        $type = $type ?: $this->type;
        if ($this->isAjax || strtolower($type) == 'api' ) {
            echo json_encode($this->apiArray);
            exit();
        } else {
            if ($this->apiArray['code'] == 1) {
                $this->success($this->apiArray['msg'],$this->apiArray['url'],$this->apiArray['data'],$this->apiArray['wait']);
            } else {
                $this->error($this->apiArray['msg'],$this->apiArray['url'],$this->apiArray['data'],$this->apiArray['wait']);
            }
        }
    }
}