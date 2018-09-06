<?php
namespace app\common\validate;
use think\Validate;

class AuthGroup extends Validate{
	 protected $rule = [
        ['group_name', 'require'],
    ];
    protected $scene = [
        'add'   =>  ['group_name'],
        'edit'	=>	['group_name'],	 
    ];    
}