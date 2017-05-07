<?php
namespace app\index\controller;
use think\Controller;


class Map extends Controller
{
    public function getMapImage($data)
    {
        return \Map::getStaticImage($data);
    }

    public function getMapLngLat($address)
    {
        return \Map::getLngLat($address);
    }
}
