<?php
namespace Api\View\Helper;
use Zend\View\Helper\AbstractHelper;
class Sqlinjection extends AbstractHelper{
	public function __invoke($string = NULL) {
		$kq = htmlspecialchars($string);
		if(is_string($string)){
			$kq = str_replace('"','',$string);
			$kq = str_replace("'","",$string);
		}
		return $kq;
	}
}