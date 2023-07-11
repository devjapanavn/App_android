<?php
namespace Api\Block\Cart;
use Zend\View\Helper\AbstractHelper;

class Reviewcart extends AbstractHelper{
    public function __invoke($data)
    {
        echo $this->view->partial('cart/reviewcart',$data);
    }
}