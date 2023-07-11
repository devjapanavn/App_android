<?php
namespace Api\Block\Slider;
use Zend\View\Helper\AbstractHelper;
use Api\Model\Brand;

class SliderBrand extends AbstractHelper{
    public function __invoke($array)
    {
        $data = array();
        $brand = new Brand($array["adapter"]);
        $data["list"] = $brand->getList(array(
		    "hot" => 1,
            "showview" => 1,
            "column" => array("name_vi","slug_vi","images")
        ));
        echo $this->view->partial('slider/slider_brand',$data);
    }
}