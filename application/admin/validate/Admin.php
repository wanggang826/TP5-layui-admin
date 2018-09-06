<?php
namespace app\admin\validate;
use think\Validate;

class Admin extends Validate{
	 protected $rule = [
        ['account',     'require|unique:Admin|alphaDash|length:6,30|regex:^[a-zA-z]+\w+',                        '帐号不能为空|帐号已存在|帐号只允许字母、数字和下划线 破折号|帐号长度为5-50个字符|帐号必须以字母开头'],
        ['nickname',     'length:2,30',                    '昵称长度需在6-30个字符之间'],
        ['password',    'require',                          '密码不能为空'],
        ['phone',       ['regex'=>'/^1[3|4|5|6|7|8][0-9]{9}$/','unique:Admin','require'],    '手机格式错误|手机号已存在|手机号不能为空'],
        
    ];
    protected $scene = [
        'add'   =>  ['account','nickname','password','phone'],
        'edit'	=>	['phone'],
    ];    
}