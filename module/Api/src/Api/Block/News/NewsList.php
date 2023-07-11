<?php

namespace Api\Block\News;

use Zend\View\Helper\AbstractHelper;
use Api\Model\NewsCategory;
use Api\Model\News;

class NewsList extends AbstractHelper
{
    public function __invoke($adapter)
    {
        $data = array();
        $data['active'] = "news";
        $arrayParam = $adapter["arrayParam"];
        $arrayParam["limit"] = 10;
        if (empty($arrayParam['page'])) {
            $arrayParam['page'] = 1;
        }
        if ($arrayParam['page'] != 0) {
            $arrayParam['offset'] = ($arrayParam['page'] - 1) * $arrayParam['limit'];
        } else {
            $arrayParam['offset'] = 0;
        }
        $newscategory = new NewsCategory($adapter['adapter']);
        $news = new News($adapter["adapter"]);
        $data['info'] = $newscategory->getItem(['id' => $arrayParam["id"]]);
        if (empty($data['info'])) {
            header("location: " . URL);
            exit();
        }
        $arrayParam['url'] = URL.$data["info"]["slug"]."-list-".$data["info"]["id"].".jp/p=";
        $data["name_category"] = $data['info']['name'];
        $arrayParam["sort_is_check_cate"] = "1";
        $arrayParam["id_category"] = $arrayParam["id"];
        $data["news"] = $news->getList($arrayParam);
        $countItem = $news->countItem($arrayParam);
        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\NullFill($countItem));
        $paginator->setCurrentPageNumber($arrayParam["page"]);
        $paginator->setItemCountPerPage($arrayParam["limit"]);
        $paginator->setPageRange(3);
        $data["paginator"] = $paginator;
        $data["arrayParam"] = $arrayParam;
        echo $this->view->partial('news/news_list', $data);
    }
}