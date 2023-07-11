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
use Api\Model\Tags;
use Admin\Model\LinkHistory;
class TagsController extends AbstractActionController
{
    private function adapter()
    {
        return $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    }
    
    public function indexAction()
    {
        $server = $_SERVER[REQUEST_URI];
        $array = explode(".", $server);
        $linkHistory = new LinkHistory($this->adapter());
        if(empty($array[1])){
            header("HTTP/1.1 301 Moved Permanently");
            header("location: ".URL.trim($array[0],"/").".jpa");
            exit();
        }
        $data = array();
        $adapter = $data["adapter"] = $this->adapter();
        $data['active'] = "index";
        $arrayParam = $this->params()->fromRoute();
        if($arrayParam["slug"] == ADMINCP){
            $this->redirect()->toUrl(URL."admincp/login/index");
           return;
        }
        $tags = new Tags($adapter);
        $data["list"] = $tags->getDetail($arrayParam);
        if(empty($data["list"])){
            $this->redirect()->toUrl(URL);
        }
        $page = new Page($adapter);
        $config = $page->getDetail(array(
            "id" => 17
        ));
        //redirect Old link to new
        $linkCheck= $linkHistory->getItem(array('link'=>$arrayParam['slug'],'type'=>2));
        //$linkCheck = $linkHistory->getItem(array('id_item'=>$arrayParam['id'],'type'=>2));
        if($linkCheck){
            $productRedirect = $tags->getItem(array('jp_tags.id'=>$linkCheck['id_item'],'columns'=>array('id','slug_vi')));
            $link =$productRedirect['slug_vi']."-tags.jp";
            $this->redirect()->toUrl($link);
            $this->redirect()->toRoute($link);
        }
        
        if($arrayParam["slug"] == ADMINCP){
            $this->redirect()->toUrl(URL."admincp/login/index");
            return;
        }
        $blockpage = new Blockpage($adapter);
        $block = $blockpage->getList(array(
            "id_page" => 17,
            "desktop" => 1
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
        $data['slug_title'] = "Japana";
        $data['slug_desc'] = "Japana";
        $data['meta_desc'] = "";
        foreach ($data["list"] as $val){
            if($val['slug_vi'] == $arrayParam["slug"]){
                $data['slug_title'] = $val["name_vi"]." ".$config["meta_web_title"];
                $data['slug_desc'] = $val["name_vi"]." ".str_replace("tag",$val["name_vi"],$config["meta_web_desc"]) ;
                $data['meta_desc'] = $val['meta_web_desc'];
                $data['meta_titles'] = $val['meta_web_title'];
            }
        }
        $data['meta_desc'] = empty($data['meta_desc'])?$data['slug_desc']:$data['meta_desc'];
        $data['meta_titles'] = empty($data['meta_titles'])?$data['slug_title']:$data['meta_titles'];
        $data["config"] = $config;
        return new ViewModel($data);
    }
}