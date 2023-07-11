<?php
namespace Api\Block\Banner;
use Zend\View\Helper\AbstractHelper;
use Api\Model\Blockpage;

class Banner4 extends AbstractHelper{
    public function __invoke($array)
    {
        $data = array();
        
        $blockpage = new Blockpage($array["adapter"]);
        $data["blockpage"] = $blockpage->getItem(array(
            "id" => $array["id_block_page"]
        ));
        $data["images"] = json_decode($data["blockpage"]["images"],true);
        
        echo $this->view->partial('banner/banner4',$data);
    }
}