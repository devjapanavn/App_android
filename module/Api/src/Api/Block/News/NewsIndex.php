<?php

namespace Api\Block\News;

use Zend\View\Helper\AbstractHelper;
use Api\Model\News;

class NewsIndex extends AbstractHelper
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
        $news = new News($adapter["adapter"]);
        $arrayParam['url'] = URL."news/p=";
        $arrayParam["sort_is_check"] = "1";
        $arrayParam["is_check"] = 0;
        $data["news"] = $news->getList($arrayParam);
        $countItem = $news->countItem($arrayParam);
        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\NullFill($countItem));
        $paginator->setCurrentPageNumber($arrayParam["page"]);
        $paginator->setItemCountPerPage($arrayParam["limit"]);
        $paginator->setPageRange(3);
        $data["paginator"] = $paginator;
        $data["arrayParam"] = $arrayParam;
        echo $this->view->partial('news/news_index', $data);
    }
}