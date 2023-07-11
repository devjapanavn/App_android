<?php
namespace Api\View\Helper;
use Zend\View\Helper\AbstractHelper;

class YoutubeID extends AbstractHelper{
	public function __invoke($url){
		$url = str_replace(array("/embed/", "/v/", "?"), array("&v=", "&v=" , "&"), $url);
		parse_str(str_replace("?", "&", $url), $arr);
		$id = $arr['v'];
		return $id;
	}
}
