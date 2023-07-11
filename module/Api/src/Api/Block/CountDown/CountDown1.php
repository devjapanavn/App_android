<?php
namespace Api\Block\CountDown;
use Zend\View\Helper\AbstractHelper;
use Api\Model\Blockpage;

class CountDown1 extends AbstractHelper{
    public function __invoke($array)
    {
        $data = array();
        $blockpage = new Blockpage($array["adapter"]);
        $data["blockpage"] = $blockpage->getItem(array(
            "id" => $array["id_block_page"]
        ));
        $data["multi_images"] = json_decode($data["blockpage"]["multi_images"],true);
        echo $this->view->partial('countdown/countdown1',$data);
    }
}