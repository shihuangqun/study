<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        phpinfo();66;
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }
}
