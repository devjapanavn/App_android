<?php
namespace Api\View\Helper;
use Zend\View\Helper\AbstractHelper;
class CutString extends AbstractHelper{
	public function __invoke($string, $len, $type=false) {
		$string = html_entity_decode($string);
		$string = strip_tags($string);
		if(strlen($string)> $len){
			if($type == false){				$t1=substr($string, 0, $len);
				$t2=substr($string, $len, strlen($string));
				$t2=str_replace(strstr($t2, " "), "...", $t2);
				return $t1.$t2;
			} else {
				$t1=substr($string, 0, $len);
				return $t1."...";
			}
		} else return $string;
	}
}