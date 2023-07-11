<?php
namespace Api\Block\Product;
use Zend\View\Helper\AbstractHelper;
use Api\Model\Product;

class ProductTime2 extends AbstractHelper{
    public function __invoke($adapter)
    {
        $data = array();
        echo $this->view->partial('product/product_time2',$data);
    }
}