<?php
namespace app\api\controller;

use dapphp\Securimage;

class Captcha
{
    public function index()
    {
        $img = new Securimage();
		$img->show();
    }
	
	function refresh(){
		$options = array('database_driver' => Securimage::SI_DRIVER_SQLITE3, 'no_session' => true);
		$captcha = Securimage::getCaptchaId(true, $options);
		$data    = array('captchaId' => $captcha);
		
		echo json_encode($data);
		exit;
	}
	
	function display(){
		$options = array('database_driver' => Securimage::SI_DRIVER_SQLITE3, 'no_session' => true);	
		$captchaId = input('get.captchaId');
		$options['captchaId']  = $captchaId;
    
		$captcha = new Securimage($options);

		// show the image, this sends proper HTTP headers
		$captcha->show();
		exit;
	}	
}
