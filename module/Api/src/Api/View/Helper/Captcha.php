<?php
namespace Api\View\Helper;
use Zend\View\Helper\AbstractHelper;use Zend\Session\Container;
class Captcha extends AbstractHelper{
	public function capcha() {
		$captcha = new \Zend\Captcha\Image();		$this->remove_allFile(CAPTCHA_PATH.'/image');		$captcha->setImgDir(CAPTCHA_PATH.'/image')        		->setImgUrl(CAPTCHA_PATH.'/image')        		->setFont(CAPTCHA_PATH.'front/TIMESBD.TTF')        		->setWidth(100)        		->setHeight(40)        		->setSuffix('.jpg')        		->setWordlen(5)        		->setDotNoiseLevel(3)        		->setLineNoiseLevel(3)        		->setFontSize(16);		$captcha->generate();		$array['id'] = $captcha->getId();		$array['image'] = $captcha->getId() . $captcha->getSuffix();		return $array;
	}	public function word($id) {		$captcha_session = new Container('Zend_Form_Captcha_'.$id);
		return $captcha_session->word;	}	private function remove_allFile($dir) {
		if ($handle = opendir("$dir")) {
			while (false !== ($item = readdir($handle))) {
				if ($item != "." && $item != "..") {
					if (is_dir("$dir/$item")) {
						remove_directory("$dir/$item");
					} else {
						unlink("$dir/$item");
					}
				}
			}
			closedir($handle);
		}
	}
}