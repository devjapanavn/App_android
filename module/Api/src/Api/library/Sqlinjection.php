<?php
namespace Api\library;

class Sqlinjection{
	public function Change($string = NULL) {
	    $array = array(
	        "'", '"', "script"
	    );
	    $kq = $string;
	    foreach ($array as $key => $value){
            	$kq = preg_replace("/".$value."/", "", $kq);
	    }
	    if(!empty($kq)){
	       $kq = htmlspecialchars($kq);
		   $kq = addslashes($kq);
	    }
		return $kq;
	}
}