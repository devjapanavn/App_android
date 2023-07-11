<?php

namespace Api\Block\News;

use Zend\View\Helper\AbstractHelper;
use Api\Model\NewsCategory;
use Api\Model\Brand;
use Api\library\Promotion;

class SideNews extends AbstractHelper
{
    public function __invoke($adapter)
    {
        $data = array();
        
        $promotion = new Promotion();
        $data = $promotion->listPromotion($adapter["adapter"]);
        
        $arrayParam = $adapter["arrayParam"];
        $data["keyword"] = $arrayParam['keyword'];
        $newscategory = new NewsCategory($adapter["adapter"]);
        $brand = new Brand($adapter["adapter"]);
        $data['info'] = $newscategory->getItem(['slug' => $arrayParam["slug"]]);
        if(!empty($arrayParam['url'])){
            $data["url"] = $arrayParam['url'];
        }else{
            $data["url"] = URL."news.jp";
        }
        $data["name_category"] = $data['info']['name'];
        $data["listbrand"] = $brand->getList(array(
            "hot" => 1,
            "showview" => 1,
            "column" => array("name_vi","slug_vi","images")
        ));
        $arrayParam["id"] = $data['info']['id'];
        $sql = "SELECT jp_news_content_category.*, 
(select count(*) FROM jp_sort_news_category 
INNER JOIN jp_news_content ON jp_sort_news_category.id_news=jp_news_content.id 
WHERE id_news_category= jp_news_content_category.id AND jp_news_content.showview=1) as count_new
                FROM jp_news_content_category 
                where jp_news_content_category.showview=1 AND jp_news_content_category.parents >0
                ORDER BY sort ASC";
        $data['list_category'] = $newscategory->JQuery($sql);
        
        $sql = "select jp_product.*
FROM jp_cart_detail
join jp_cart on jp_cart.id = jp_cart_detail.id_cart
join jp_product on jp_product.id = jp_cart_detail.id_product
where 
	jp_cart.date_order BETWEEN NOW() - INTERVAL 30 DAY AND NOW()
	and jp_cart_detail.price > 0
    and jp_cart.status_cart NOT IN (12,14)
GROUP BY jp_cart_detail.id_product
ORDER BY sum(jp_cart_detail.qty) desc LIMIT 6";
        $data['best_seller'] = $newscategory->JQuery($sql);
        $arrDataLoadAjax = [
            "best_seller" => $data['best_seller'],
            "km_images" => $data['km_images'],
            "km_images_qt" => $data['km_images_qt'],
        ];

        $data['data_ajax'] = $arrDataLoadAjax;
        echo $this->view->partial('news/side_news', $data);
    }
}