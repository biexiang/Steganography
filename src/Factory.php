<?php

namespace Steganography;
use Steganography\Utils\ImageHandler;

class Factory{

    public static function encode($imgPath,$str)
    {
        $im = self::getIm($imgPath);
        if(!$im)
        {
            throw new Exception("Format not supported");
        }

        return (new ImageHandler($im,$str))->encode();
    }

    public static function decode($imgPath)
    {
        $im = self::getIm($imgPath);
        if(!$im)
        {
            return false;
        }
        return (new ImageHandler($im))->decode();
    }

    public static function getIm($imgPath)
    {
        $ext = pathinfo($imgPath)['extension'];
        if(!in_array($ext,array("png","jpg")))
        {
            return false;
        }

        if($ext == "png")
        {
            return @imagecreatefrompng($imgPath);
        }else if($ext == "jpg")
        {
            return @imagecreatefromjpeg($imgPath);
        }
    }
}



?>