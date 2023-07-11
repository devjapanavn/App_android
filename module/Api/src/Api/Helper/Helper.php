<?php
namespace Api\Helper;

use Zend\Session\Container;

class Helper
{
    public function setSession()
    {

    }

    public function getSession($key)
    {
        $session = new Container($key);
        $session->setExpirationSeconds(TIME_SESSION);
        return $session;
    }

    public function totalPriceCart($type ="")
    {
        $session = self::getSession("cart");
        $list = json_decode($session->arrayCart,true);

        $total = 0;
        if($list)
        {
            foreach ($list as $val){
                $total += $val["price_market"]*$val["sl"];
            }
        }
       return $total;
    }

    public function totalQuantityCart($id = "")
    {
        $session = self::getSession("cart");
        $list = json_decode($session->arrayCart,true);

        $total = 0;
        if($list)
        {
            if($id)
            {
                foreach ($list as $val){
                    if ($val["id"] === $id)
                        $total += $val["sl"];
                }
            }
            else {
                foreach ($list as $val){
                    $total += $val["sl"];
                }
            }

        }
        return $total;
    }

    public function totalCart()
    {
        $session = self::getSession("cart");
        $list = json_decode($session->arrayCart,true);
        return  $list ? count($list) : 0;
    }

    public function issetCartId($id)
    {
        $session = self::getSession("cart");
        $list = json_decode($session->arrayCart,true);
        $issetIDCart=0;
        if(!empty($list) && !empty($id))
        {
            foreach ($list as $val){
                if ($val["id"] === $id){
                    $issetIDCart=1;
                    break;
                }
            }
        }
        return $issetIDCart;
    }

    public function resizeImages($pathImages, $path, $widthPar, $heightPar){
        $fileImages = $_SERVER['DOCUMENT_ROOT'].$pathImages;
        $imageType = image_type_to_mime_type(exif_imagetype($fileImages));
        $baseName = basename($fileImages);

        // Get new sizes
        list($width, $height) = getimagesize($fileImages);

        $original_aspect = $width / $height;
        $thumb_aspect = $widthPar / $heightPar;

        if ( $original_aspect >= $thumb_aspect )
        {
            // If image is wider than thumbnail (in aspect ratio sense)
            $newHeight = $heightPar;
            $newWidth = $width / ($height / $heightPar);
        }
        else
        {
            // If the thumbnail is wider than the image
            $newWidth = $widthPar;
            $newHeight = $height / ($width / $widthPar);
        }

        $dst_x = 0 - ($newWidth - $widthPar) / 2;
        $dst_y = 0 - ($newHeight - $heightPar) / 2;

        //Load Image
        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        switch ($imageType){
            case 'image/jpeg':
                $source = @imagecreatefromjpeg($fileImages);
                @imagecopyresampled($thumb, $source, $dst_x, $dst_y, 0, 0, $newWidth, $newHeight, $width, $height);
                imagejpeg($thumb, $path.$widthPar."x".$heightPar."-".$baseName, 100);
                break;
            case 'image/png':
                $source = @imagecreatefrompng($fileImages);
                @imagecopyresampled($thumb, $source, $dst_x, $dst_y, 0, 0, $newWidth, $newHeight, $width, $height);
                imagepng($thumb, $path.$widthPar."x".$heightPar."-".$baseName, 9);
                break;
        }
        imagedestroy($thumb);
        return true;
    }
}