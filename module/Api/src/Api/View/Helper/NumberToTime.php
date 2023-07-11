<?php
namespace Api\View\Helper;
use Zend\View\Helper\AbstractHelper;

class NumberToTime extends AbstractHelper{
	public function __invoke($time = null, $text = false){
		$time  = intval($time);
		$sl = array();
		if ($text == true) {
			$sl = array(" giờ ", " phút ", " giây");
		} else {
			$sl = array(":", ":", "");
		}
		$b1 ="";
		$b2 = 0;
		$b2 = $this->pad(floor($time / 3600),2);
		$b1 = $b1 . ($b2 . $sl[0]);
		$time = $time - 3600 * $b2;
		if ($b1 == "0" . $sl[0]){
			$b1 = "";
		}
		$b1 = $b1 . ($this->pad(floor( $time / 60), 2)) . $sl[1];
		if ($b1 == "0" . $sl[1]){
			$b1 = "";
		}
		$b1 = $b1 . ($this->pad(floor($time % 60), 2)) . $sl[2];
		
		return $b1;
	}
	private function pad($s1, $s2)
	{
		while (strlen($s1) < $s2) {
			$s1 = "0".$s1;
		}
		return $s1;
	}
}
