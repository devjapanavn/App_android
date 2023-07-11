<?php
namespace Api\Block\Banner;
use Zend\View\Helper\AbstractHelper;
use Admin\Model\BlockPage;

class Banner1 extends AbstractHelper{
    public function __invoke($array)
    {
        $data = array();
        $blockpage = new BlockPage($array["adapter"]);
        $data["blockpage"] = $blockpage->getItem(array(
            "id" => $array["id_block_page"]
        ));
        $data["multi_images"] = json_decode($data["blockpage"]["multi_images"],true);
        echo $this->view->partial('banner/banner1',$data);
    }
}