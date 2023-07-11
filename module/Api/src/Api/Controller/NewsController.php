<?php

namespace Api\Controller;

use Api\library\library;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Api\Model\NewsCategory;
use Api\Model\News;
use Api\Model\Blockpage;
use Api\library\Sqlinjection;


class NewsController extends AbstractActionController
{
    private $library;

    function __construct()
    {
        $this->library = new library();
    }

    private function adapter()
    {
        return $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    }

    public function indexAction()
    {
        $data = array();
        $data["adapter"] = $this->adapter();
        $data['active'] = "news";
        $arrayParam['adapter'] = $data["adapter"];
        $arrayParam = $this->params()->fromRoute();
        $info["meta_web_title"] = "Thông tin tiêu dùng japana";
        $info["meta_web_keyword"] = "Thông tin tiêu dùng japana";
        $info["meta_web_desc"] = "Thông tin tiêu dùng japana";
        $info["og_image"] = "Thông tin tiêu dùng japana";
        $info["og_desc"] = "Thông tin tiêu dùng japana";
        $info["og_title"] = "Thông tin tiêu dùng japana";
        $data["info"] = $info;
        $data["config"]['url'] = URL . "news.jp";
        $blockpage = new Blockpage($data["adapter"]);
        $data["list_block"] = $block = $blockpage->getList(array(
            "id_page" => 14,
            "desktop" => 1
        ));
        foreach ($block as $key => $value) {
            if ($value["css_top"] == 1) {
                $data["block"]["css_top"][] = $value["name_code"];
            }
            if ($value["css_bottom"] == 1) {
                $data["block"]["css_bottom"][] = $value["name_code"];
            }
            if ($value["js_top"] == 1) {
                $data["block"]["js_top"][] = $value["name_code"];
            }
            if ($value["js_bottom"] == 1) {
                $data["block"]["js_bottom"][] = $value["name_code"];
            }
        }
        $sqlin = new Sqlinjection();

        if (!empty($_GET['q'])) {
            $q = $sqlin->Change($_GET['q']);
        } else {
            $q = '';
        }
        $query = $this->getRequest()->getQuery()->toArray();

        $arrayParam['keyword'] = $query['keyword'];
        if (empty($data['info'])) {
            $this->redirect()->toUrl(URL);
        }
        $data["config"]["url"] = URL . "news.jp?q=" . $q;
        $arrayParam["url"] = URL . "news.jp/p=";
        if (!empty($_GET)) {
            $arrayParam["slug_url"] = $sqlin->Change("?" . http_build_query($_GET));
        }
        $data["name_category"] = $data['info']['name'];
        $data["arrayParam"] = $arrayParam;
        return new ViewModel($data);
    }

    public function itemAction()
    {
        $data = array();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $arrayParam = $request->getPost()->toArray();
            $news = new News($this->adapter());
            $info = $news->getItem($arrayParam["id"]);
            $info['desc']="<div>".$info['desc']."</div>";
            $data['info']=$info;
            $data["list_block"] = [];

        }
        return $this->library->returnResponse(200, $data, "success", "");
    }


    public function listAction()
    {
        $data = array();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $arrayParam = $request->getPost()->toArray();

            $blockpage = new Blockpage($data["adapter"]);
            $data["list_block"] = [];

            $query = $this->getRequest()->getQuery()->toArray();
            $arrayParam['keyword'] = $query['keyword'];
            $newscategory = new NewsCategory($this->adapter());
            $data['info'] = $newscategory->getItem(['id' => $arrayParam["id"]]);
            if (empty($data['info'])) {
                $this->redirect()->toUrl(URL);
            }
            $sqlin = new Sqlinjection();
            $arrayParam['url'] = $data['info']["slug"] . "-list-" . $data['info']['id'] . ".jp";
            if (!empty($_GET['q'])) {
                $q = $sqlin->Change($_GET['q']);
                $data["config"]["url"] = $arrayParam['url'] . "?q=" . $q;
                $arrayParam["url"] = $arrayParam['url'] . "?p=";
                $arrayParam["slug_url"] = $sqlin->Change("?" . http_build_query($_GET));
            }
            $data["name_category"] = $data['info']['name'];
        }
        return $this->library->returnResponse(200, $data, "success", "");
    }

}