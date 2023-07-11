<?php
namespace Api\Block\Slider;
use Zend\View\Helper\AbstractHelper;
use Api\Model\Blockpage;

class SliderHome extends AbstractHelper{
    public function __invoke($array)
    {
        $data = array();
        $blockpage = new Blockpage($array["adapter"]);
        $data["blockpage"] = $blockpage->getItem(array(
            "id" => $array["id_block_page"]
        ));
        $data["multi_images"] = json_decode($data["blockpage"]["multi_images"],true);
        echo $this->view->partial('slider/slider_home',$data);
    }
}