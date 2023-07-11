<?php
namespace Api\Block\Cart;
use Zend\View\Helper\AbstractHelper;

class Cartindex extends AbstractHelper{
    public function __invoke($data)
    {
        echo $this->view->partial('cart/cartindex',$data);
    }
}