<?php

namespace Api\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class HomeNewController extends AbstractActionController
{
    private function adapter()
    {
        return $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    }

    public function viewProductDetailAction()
    {
        $data["adapter"] = $this->adapter();
        $data['active'] = "products";
        return new ViewModel($data);
    }

    public function viewProductCategoryAction()
    {
        $data["adapter"] = $this->adapter();
        $data['active'] = "Productcategory";
        return new ViewModel($data);
    }

    public function viewCartAction()
    {
        $data["adapter"] = $this->adapter();
        $data['active'] = "cart";
        return new ViewModel($data);
    }

    public function viewCheckoutAction()
    {
        $data["adapter"] = $this->adapter();
        $data['active'] = "cart";
        return new ViewModel($data);
    }

//    public function viewHomeAction()
//    {
//        $data["adapter"] = $this->adapter();
//        $data['active'] = "index";
//        return new ViewModel($data);
//    }


}