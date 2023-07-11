<?php

namespace Api\Controller;

use Api\library\BlockLibs;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Api\Model\Config;
use Api\Model\Blockpage;
use Api\Model\Page;
use Api\Model\Banner;
use Api\library\library;
use Api\Model\Block;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
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
        $adapter = $this->adapter();
        $libs_block = new BlockLibs($adapter);
        $request = $this->getRequest();
        $id_page = ID_BLOCK_PAGE_HOME;
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            if (!empty($param_post['id_page'])) {
                $id_page = $param_post['id_page'];
            }
        }
        $blockList = $libs_block->getDataBlockPage($id_page);
        return $this->library->returnResponse(200, $blockList, "success", "Thành công");
    }

    public function generateLinkAction()
    {
        $data = array();
        $adapter = $this->adapter();
        $request = $this->getRequest();
        $libs_block = new BlockLibs($adapter);
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            if (!empty($param_post['link']) && $param_post['link']!="#") {
                $link = $param_post['link'];
                $data = $libs_block->getIdPageFromLink($link);
                if(!$data){
                    return $this->library->returnResponse(400, $data, "error", "");//Đường dẫn không khả dụng
                }
            }else{
                return $this->library->returnResponse(400, $data, "error", "");//KHONG SHOW. Đường dẫn không khả dụng
            }
        }
        return $this->library->returnResponse(200, $data, "success", "Thành công");
    }


}