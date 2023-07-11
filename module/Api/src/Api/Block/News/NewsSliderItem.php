<?php
namespace Api\Block\News;
use Zend\View\Helper\AbstractHelper;
use Api\Model\News;
use Api\Model\NewsCategory;

class NewsSliderItem extends AbstractHelper
{
    public function __invoke($array)
    {
        $data = array();
        $arrayParam = $array;
        $newscategory = new NewsCategory($array['adapter']);
        $news = new News($array['adapter']);
        $data['info'] = $newscategory->getItem(['slug' => $arrayParam["slug"]]);
        if (empty($data['info'])) {
            header("location: " . URL);
            exit();
        }
        $data["name_category"] = $data['info']['name'];
        $arrayParam["id"] = $data['info']['id'];
        $sql = "SELECT jp_news_content_category.*,
                (
                	select GROUP_CONCAT(jp_news.id) 
                	from jp_news_content_category as jp_news 
                	where jp_news.left > jp_news_content_category.`left` 
                	AND jp_news.right < jp_news_content_category.`right` 
                ) as list_id
                FROM jp_news_content_category 
                where jp_news_content_category.showview=1 AND  jp_news_content_category.parents = " . $data['info']['id'] . "
                ORDER BY sort ASC";
        $data['list_category'] = $newscategory->JQuery($sql);
        /* lay tat ca danh muc cap con - bai viet dm cap con */
        $list_id = "";
        if (isset($data['list_category']) && !empty($data['list_category'])) {

            $list_category = $newscategory->getList(array(
                "left" => $data['info']["left"],
                "right" => $data['info']["right"]
            ));
            foreach ($list_category as $item) {
                if (empty($list_id)) {
                    $list_id = $item["id"];
                } else {
                    $list_id .= "," . $item["id"];
                }
            }
            $data['hot'] = $news->getList(array(
                "list_id_category" => $list_id,
                "keyword" => $arrayParam['keyword'],
                "is_check" => 1,
                "limit" => 5,
                "offset" => 0,
                "time_limit" => 1,
                "order" => "jp_news_content.sort asc"
            ));
            
            if (count($data['hot']) < 5) {
                $new_limit = 5 - count($data['hot']);
                $list_bosung = $news->getList(array(
                    "list_id_category" => $list_id,
                    "limit" => $new_limit,
                    "offset" => 0,
                    "order" => "jp_news_content.sort asc"
                ));
                if (empty($data['hot']) && !empty($list_bosung)) {
                    $data['hot'] = $list_bosung;
                } else if (!empty($list_bosung)) {
                    $data['hot'] = array_merge($data['hot'], $list_bosung);
                }
            }
        }
        echo $this->view->partial('news/news_slider_item', $data);
    }
}