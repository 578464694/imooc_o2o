<?php
namespace app\api\controller;
use think\Controller;
use think\File;
use think\Request;
class Image extends Controller
{
    public function upload()
    {
        $file = Request::instance()->file('file');
        // 给定一个目录
        $info = $file->move('upload');  //public.upload
        //　双传成功
        if($info && $info->getPathname()) {
            return show(1, 'success', '\\'.$info->getPathname());
        }
        return show(0,'upload error');
    }
}