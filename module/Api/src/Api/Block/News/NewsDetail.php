<?php

namespace Api\Block\News;

use Zend\View\Helper\AbstractHelper;
use Api\Model\NewsCategory;
use Api\Model\News;

class NewsDetail extends AbstractHelper
{
    public function __invoke($adapter)
    {
        $data = array();
        $arrayParam = $adapter["arrayParam"];
        $data['active'] = "news";
        $newscategory = new NewsCategory($adapter['adapter']);
        $news = new News($adapter["adapter"]);
        $data['info'] = $news->getItem($arrayParam["id"]);
        $data['news2'] = $news->getList(array(
            "list_id_category" => $data['info']["list_id_category"],
            "limit" => 4,
            "offset" => 0,
            "id_khac" => $arrayParam["id"]
        ));
        $category = $newscategory->getList(array(
            "list_id" => $data['info']["list_id_category"]
        ));
        $data['category'] = $category[0];
        if ($data['info']['showview'] == 0) {
            //nếu bài bị ẩn đi
            header("location: " . URL);
            exit();
        }
        /*
         * kiểm tra đúng url hiện tại
         */
        $get_slug = $arrayParam['slug'];
        if (trim($data["info"]["slug"]) != trim($get_slug)) {
            header("location: " . URL);
            exit();
        }
        if (empty($data['info'])) {
            header("location: " . URL);
            exit();
        }

        $visit = $data['info']['visit'] + 1;
        $news->updateItem(['visit' => $visit], $data['info']['id']);
        echo $this->view->partial('news/news_detail', $data);
    }
}