<?php
if (!function_exists("getValidate")) {
    function getValidate($w, $h, $key)
    {
        ob_start();
        $img = imagecreatetruecolor($w, $h);
        $gray = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, rand(0, 200), rand(0, 200), rand(0, 200));
        $red = imagecolorallocate($img, 255, 0, 0);
        $white = imagecolorallocate($img, 255, 255, 255);
        $green = imagecolorallocate($img, 0, 255, 0);
        $blue = imagecolorallocate($img, 0, 0, 255);
        imagefilledrectangle($img, 0, 0, 210, 70, $black);
        for ($i = 0; $i < 80; $i++) {
            imagesetpixel($img, rand(0, $w), rand(0, $h), $gray);
        }
        $num1 = mt_rand(51, 100);
        $num2 = mt_rand(1, 50);
        $rand = getRand();
        $ttf = base_path('public/asset/Moderan.ttf');
        imageTtfText($img, 20, rand(-45, 45), 20, rand(30, 50), $red, $ttf, $num1);
        imageTtfText($img, 20, 0, 65, rand(30, 50), $white, $ttf, $rand);
        imageTtfText($img, 20, rand(-45, 45), 100, rand(30, 50), $green, $ttf, $num2);
        imageTtfText($img, 20, 0, 135, rand(30, 50), $blue, $ttf, "=");
        imageTtfText($img, 20, 0, 185, rand(30, 50), $red, $ttf, "?");
        imagepng($img);
        imagedestroy($img);
        $content = ob_get_clean();
        if ($rand == "+") {
            //加
            $result = $num1 + $num2;
        } else {
            //减
            $result = $num1 - $num2;
        }
        $expiresAt = \Carbon\Carbon::now()->addMinutes(2);
        \Illuminate\Support\Facades\Cache::put('image:' . $key, $result, $expiresAt);
        return response($content)->header('Content-Type', 'image/png');
    }
}

if(!function_exists('getRand')){
    function getRand(){
        $code = mt_rand(0,1);
        switch ($code) {
            case 0:
                return "+";
                break;
            case 1:
                return "-";
                break;
            default:
                # code...
                break;
        }
    }
}

if(!function_exists('time_tranx')){
    function time_tranx($the_time){
        $now_time = time();
        $dur = $now_time - $the_time;
        if($dur <= 0){
            return '刚刚';
        }else{
            if($dur < 60){
                return $dur.'秒前';
            }else{
                if($dur < 3600){
                    return floor($dur/60).'分钟前';
                }else{
                    if($dur < 86400){
                        return floor($dur/3600).'小时前';
                    }else{
                        if($dur < 259200){ //3天内
                            return floor($dur/86400).'天前';
                        }else{
                            return date("Y-m-d H:i:s",$the_time);
                        }
                    }
                }
            }
        }
    }
}
