<?php
namespace Api\Controller;

use Admin\Libs\PhpOffice\PhpSpreadsheet\Helper\Sample;
use Admin\Libs\PhpOffice\PhpSpreadsheet\IOFactory;
use Admin\Libs\PhpOffice\PhpSpreadsheet\Spreadsheet;
use Admin\Libs\PhpOffice\PhpSpreadsheet\Style\Color;
use Api\library\Email;
use Api\Model\Product;
use Zend\Mvc\Controller\AbstractActionController;

class ExportProductController extends AbstractActionController
{
    private function adapter()
    {
        return $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    }

    public function indexAction()
    {
        //Export file excel
        $arrParam = $this->params()->fromQuery();
        $timeEndDate = strtotime($arrParam['date_export']);
        $endDate = date('Y-m-d 23:59:59', $timeEndDate);
        $startDate = date('Y-m-01 H:i:s', $timeEndDate);
        $month = date('m-Y', $timeEndDate);

        if(!isset($arrParam['email']) && $arrParam['email'] == ""){
            echo json_encode(['error_email'=> 'Email không tồn tại']);
            exit;
        }
        switch ($arrParam['type-export']){
            case 'TM':
                $nameProduct = $this->exportDataProduct($startDate, $endDate);
                $nameOrder = $this->exportDataOrder($startDate, $endDate);
                $nameHistory = $this->exportDataHistory($startDate, $endDate);
                $html = $this->messageSendMailTM($nameProduct, $nameOrder, $nameHistory, URL, $month);
                $this->sendMail($arrParam, $html, $month);
                break;
            case 'MT':
                $nameProduct = null;
                $nameOrder = null;
                $nameHistory = null;
                $html = $this->messageSendMailMT($nameProduct, $nameOrder, $nameHistory, URL, $month);
                $this->sendMail($arrParam, $html, $month);
                break;
            case 'KT':
                $nameProduct = null;
                $nameOrder = null;
                $nameHistory = null;
                $html = $this->messageSendMailKT($nameProduct, $nameOrder, $nameHistory, URL, $month);
                $this->sendMail($arrParam, $html, $month);
                break;
        }

    }

    public function downloadAction() {
        $arrParam = $this->params()->fromQuery();

        $pathDir = PATH_EXCEL_EXPORT;

        $fileName = $pathDir.$arrParam['file-name'];

        $response = new \Zend\Http\Response\Stream();
        $response->setStream(fopen($fileName, 'r'));
        $response->setStatusCode(200);

        $headers = new \Zend\Http\Headers();
        $headers->addHeaderLine('Content-Type', 'whatever your content type is')
            ->addHeaderLine('Content-Disposition', 'attachment; filename="' . $arrParam['file-name'] . '"')
            ->addHeaderLine('Content-Length', filesize($fileName));

        $response->setHeaders($headers);
        return $response;
    }

    private function sendMail($arrParam, $htmlBody, $month){
        //Send mail attach file
        $email = new Email();
        $arrEmail = explode(",", $arrParam['email']);
        foreach ($arrEmail as $rowEmail){
            $arrData = ["emailTo" => $rowEmail];
            echo $email->sendemail_phpmailer($arrData, $htmlBody, 'Xuất file định kỳ '.$month);
        }
    }

    private function messageSendMailTM($nameProduct, $nameOrder, $nameHistory, $urlExport, $monthExport){
        return <<<HTML
        <p>Xuất các file định kỳ tháng {$monthExport} cho bên thu mua</p>
        <p>-Xuất file product</p>
        <p>Link download: </p>
        <p><a href="{$urlExport}export-product/download?type=product&file-name={$nameProduct}">{$urlExport}export-product/download?type=product&file-name={$nameProduct}</a></p>
        <p>-Xuất file tình trạng đơn hàng</p>
        <p>Link download: </p>
        <p><a href="{$urlExport}export-product/download?type=order&file-name={$nameOrder}">{$urlExport}export-product/download?type=order&file-name={$nameOrder}</a></p>
        <p>-Xuất file lịch sử nhập hàng</p>
        <p>Link download: </p>
        <p><a href="{$urlExport}export-product/download?type=history&file-name={$nameHistory}">{$urlExport}export-product/download?type=history&file-name={$nameHistory}</a></p>
        
HTML;

    }

    private function messageSendMailMT($nameProduct, $nameOrder, $nameHistory, $urlExport, $monthExport){
        return <<<HTML
        <p>Xuất các file định kỳ tháng {$monthExport} cho bên Marketing</p>
HTML;

    }

    private function messageSendMailKT($nameProduct, $nameOrder, $nameHistory, $urlExport, $monthExport){
        return <<<HTML
        <p>Xuất các file định kỳ tháng {$monthExport} cho bên Kế toán</p>
HTML;

    }

    private function exportDataOrder($startDate, $endDate){
        $arrData = $this->getDataStatusOrder($startDate, $endDate);
        $name = "order-".date("d-m", strtotime($startDate))."->".date("d-m-Y", strtotime($endDate));
        $nameFile = "xuat-order-".date("d-m-Y-H-i", time()).".xlsx";
        $arraytitle = array('Cart ID', "SKU",  "Tên sản phẩm", "Số lượng", "Thành tiền", "Tình trạng","Ngày tình trạng số lượng", "Deadline");
        $column_key = array("id_cart", "sku", "name", "qty", "total", "name_status", "log_date_status_qty","Deadline");
        $this->exportFileData($arrData, $arraytitle, $column_key, $name, $nameFile, 1);
        return $nameFile;
    }

    private function exportDataProduct($startDate, $endDate){
        $arrData = $this->getDataProduct($startDate, $endDate);
        $name = "product-".date("d-m", strtotime($startDate))."->".date("d-m-Y", strtotime($endDate));
        $nameFile = "xuat-product-".date("d-m-Y-H-i", time()).".xlsx";
        $arraytitle = array('ID', "SKU",  "Tên sản phẩm", "Thương hiệu", "Sản xuất tại", "Quy cách","Giá sau KM", "Giá", "Hiển thị", "Tình trạng", "Danh mục", "Ngày cập nhật", "Ngày showview", "Link", "Link Image");
        $column_key = array("id", "sku", "name_vi", "ThuongHieu", "SanXuatTai", "QuyCach", "GiaSauKM","price", "HienThi", "TinhTrang", "DanhMucCap1", "date_update", "date_showview", "Link", "link_image");
        $this->exportFileData($arrData, $arraytitle, $column_key, $name, $nameFile, 0);
        return $nameFile;
    }

    private function exportDataHistory($startDate, $endDate){
        $arrData = $this->getDataHistory($startDate, $endDate);
        $name = "lich-su-".date("d-m", strtotime($startDate))."->".date("d-m-Y", strtotime($endDate));
        $nameFile = "xuat-lich-su-".date("d-m-Y-H-i", time()).".xlsx";
        $arraytitle = array("SKU",  "Tên sản phẩm", "Giá bán", "Giá mua", "Lợi nhuận");
        $column_key = array("sku", "name_vi", "giaban", "giamua", "loinhuan");
        $this->exportFileData($arrData, $arraytitle, $column_key, $name, $nameFile, 2);
        return $nameFile;
    }

    /**
     * Get data list product
     * @return array
     */
    public function getDataProduct($startDate, $endDate){
        $products = new Product($this->adapter());
        $arrProducts = $products->getListProductForExport($startDate, $endDate);
        return $arrProducts;
    }

    /**
     * Get data list status order
     * @return array
     */
    public function getDataStatusOrder($startDate, $endDate){
        $products = new Product($this->adapter());
        $arrProducts = $products->getListStatusOrderForExport($startDate, $endDate);
        return $arrProducts;
    }

    /**
     * Get data list status order
     * @return array
     */
    public function getDataHistory($startDate, $endDate){
        $products = new Product($this->adapter());
        $arrProducts = $products->getListHistoryInputForExport($startDate, $endDate);
        return $arrProducts;
    }

    private function exportFileData($data, $arraytitle, $column_key=array(), $nameExport, $nameFileExport, $typeExport = 0)
    {
        set_time_limit(0);
        $helper = new Sample();
        if ($helper->isCli()) {
            $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
            return;
        }

        $pathExport = PATH_EXCEL_EXPORT;

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Japana')
            ->setLastModifiedBy('Japana')
            ->setTitle('Office 2007 XLSX Export')
            ->setSubject('Office 2007 XLSX Export')
            ->setDescription('Database table document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Database table');
        $alpha = 'A';

        if(!empty($data)){
            $spreadsheet->setActiveSheetIndex(0);
            for ($k = 0; $k < count($arraytitle); $k++) {
                $spreadsheet->getActiveSheet()->getStyle($alpha . (1))
                    ->getFont()->setBold("bold");
                $spreadsheet->getActiveSheet()->setCellValue($alpha . (1), $arraytitle[$k]);
                $alpha++;
            }
            $ind = 2;
            foreach ($data as $key => $value) {
                $alpha = 'A';
                $spreadsheet->setActiveSheetIndex(0);
                $cCountColumn = count($column_key);
                for ($i = 0; $i <= $cCountColumn; $i++) {

                    if($column_key[$i] == 'price'){
                        $value[$column_key[$i]] = round($value[$column_key[$i]], 2);
                    }

                    if($column_key[$i] == 'date_update' || $column_key[$i] == 'date_showview' ){
                        $value[$column_key[$i]] = date('d-m-Y H:i:s', strtotime($value['date_update']));
                    }

                    $spreadsheet->getActiveSheet()->setCellValue($alpha . ($ind), $value[$column_key[$i]]);
                    $spreadsheet->getActiveSheet()->getColumnDimension($alpha)->setAutoSize(true);
                    $alpha++;
                }
                $ind++;
            }
        }

        $spreadsheet->getActiveSheet()->setTitle($nameExport);
        $spreadsheet->setActiveSheetIndex(0);
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($pathExport . $nameFileExport);
        return true;
    }


}