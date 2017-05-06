<?php
namespace app\index\validate;
use think\Validate;

class Test extends Validate
{
    protected $rule = [
        'username' => ['unique:test,username'],
        'nimei' => ['unique:test,nimei']
    ];

    protected $scene = [
        'add' => ['nimei','username']
    ];
}