<?php
namespace app\admin\model;
use think\Model;
use traits\model\SoftDelete;
/**
 * 收益记录模型
 * @author 
 * @version wanggang 2017/8/29
 */
class Config extends Model{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $readonly = [];//只读字段

}