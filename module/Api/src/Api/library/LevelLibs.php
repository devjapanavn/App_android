<?php

namespace Api\library;

use Api\Model\Level;
use Api\Model\Points;
use Zend\Mvc\Controller\AbstractActionController;

class LevelLibs extends AbstractActionController
{

    private $adapter;

    public function __construct($adapter)
    {
        $this->adapter = $adapter;
    }
    
    function checkUseVIP()
    {
        $model_points = new Points($this->adapter);
        $LevelSetting = $model_points->getItemLevelSetting();
        $ap_dung_vip = 0;
        $today = date("Y-m-d");
        if (!empty($LevelSetting)) {
            if ($LevelSetting['discount_level'] == 1 && $LevelSetting['discount_time'] == 0) {
                $ap_dung_vip = 1;
            } elseif ($LevelSetting['discount_level'] == 1 && $LevelSetting['discount_time'] == 1) {
                $date_from_apdung = $LevelSetting['start_date'];
                $ap_dung_vip = 0;
                /*ngay ap dung nho hon ngay hien tai*/
                if (strtotime($date_from_apdung) <= strtotime($today)) {
                    $ap_dung_vip = 1;
                }
                /*co check ngay ket thuc*/
                if (empty($LevelSetting['check_end_date'])) {
                    $date_to_apdung = $LevelSetting['end_date'];
                    /*den ngay ap dung lon hon ngay hien tai*/
                    $ap_dung_vip = 0;
                    if (strtotime($date_to_apdung) >= strtotime($today) && strtotime($date_from_apdung) <= strtotime($today)) {
                        $ap_dung_vip = 1;
                    }
                }
            }
        }
        return $ap_dung_vip;
    }

    function getIdLevel($revenue_total)
    {
        $model_levels = new Level($this->adapter);
        $listLevel = $model_levels->getList();
        foreach ($listLevel as $item) {
            $condition = $item['condition'];
            $condition_to = $item['condition_to'];
            if ($revenue_total >= $condition && $revenue_total < $condition_to) {
                return $item['id'];
            } elseif ($revenue_total >= $condition && $revenue_total <= $condition_to) {
                /*level chinh no*/
                return $item['id'];
            } elseif ($revenue_total >= $condition && $condition_to == 0) {
                return $item['id'];
            }
        }
        return 0;
    }
}

?>