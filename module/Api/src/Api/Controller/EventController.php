<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Api\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Api\Model\Blockpage;
use Api\Model\Page;

class EventController extends AbstractActionController
{
    private function adapter()
    {
        return $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    }
    
    public function indexAction()
    {
        $data = array();
        $adapter = $data["adapter"] = $this->adapter();
        $data['active'] = "event";
        $arrayParam = $this->params()->fromRoute();
        $server = $_SERVER["REQUEST_URI"];
        $server = explode("?", $server);
        $array = explode("/", $server[0]);
        if(!strpos($array[1], '.jp') && strpos($array[1], 'event')){
            header("HTTP/1.1 301 Moved Permanently");
            if(!empty($server[1])){
                header("location: ".URL.$arrayParam["slug"]."-event.jp?".$server[1]);
            }else{
                header("location: ".URL.$arrayParam["slug"]."-event.jp");
            }
            exit();
        }
        if(empty($arrayParam["page"])){
            $data["page"] = 1;
        }else{
            $data["page"] = $arrayParam["page"];
        }
        $page = new Page($adapter);
        $detail_page = $page->getDetail($arrayParam);
        if(empty($detail_page)){
            $this->redirect()->toUrl(URL);
            return false;
        }
        $blockpage = new Blockpage($adapter);
		
        $block = $blockpage->getList(array(
            "id_page" => $detail_page["id"],
            "desktop" => 1,
            "check_time" => 1
        ));
		
        $data["list_block"] = $block;
        foreach ($block as $key => $value){
            if($value["css_top"] == 1){
                $data["block"]["css_top"][] = $value["name_code"];
            }
            if($value["css_bottom"] == 1){
                $data["block"]["css_bottom"][] = $value["name_code"];
            }
            if($value["js_top"] == 1){
                $data["block"]["js_top"][] = $value["name_code"];
            }
            if($value["js_bottom"] == 1){
                $data["block"]["js_bottom"][] = $value["name_code"];
            }
        }
        $data["config"] = $detail_page;
        return new ViewModel($data);
    }
}