<?php
namespace Api\library;
use Zend\Mime\Mime;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Api\library\PHPMailer;

class Email
{
    function __construct()
    {
    }

    public function sendmail($arrayParam = null , $body_content , $subject){
        $mail = new PHPMailer();
        //Thiet lap thong tin nguoi gui va email nguoi gui
        $mail->IsSMTP(); // Gọi đến class xử lý SMTP
        $mail->SMTPAuth = true;                  // Sử dụng đăng nhập vào account
        $mail->Host = "mail92208.dotvndns.vn";     // Thiết lập thông tin của SMPT
        $mail->Username = "info@japana.vn"; // SMTP account username
        $mail->Password = '?<>:"//+$%#hndTRHlam0a19pu2ta';            // SMTP account password
        $mail->SetFrom("info@japana.vn", "Siêu thị Nhật Bản Japana");
        //Thiết lập thông tin người nhận
        $mail->AddAddress($arrayParam['emailTo']);

        //$mail->AddAddress($rs_order['email'], $_POST['hoten']);
        $mail->AddBCC('support@japana.vn');
	
        // $mail->AddBCC('ngophunguyen@gmail.com');
        /*$mail->AddBCC('japana1911@gmail.com');
        $mail->AddBCC('ptxuanuyen87@gmail.com');*/

        //Thiết lập email nhận email hồi đáp
        //nếu người nhận nhấn nút Reply
        //$mail->AddReplyTo($userhost, $row_setting['title_vi']);

        /* =====================================
         * THIET LAP NOI DUNG EMAIL
         * ===================================== */

        //Thiết lập tiêu đề
        $mail->Subject = $subject;

        //Thiết lập định dạng font chữ
        $mail->CharSet = "utf-8";

        $mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
        //Thiết lập nội dung chính của email
        $mail->MsgHTML($body_content);
        $contentlog = "";
        if (!$mail->Send()) {
            $contentlog .= "lỗi send don hang" . PHP_EOL;
        }
    } //end func
    public function sendemail_phpmailer($arrayParam = null , $body_content, $subject)
    {
        /*$body = $body_content;
        $content = new \Zend\Mime\Part($body);
        $content->type = Mime::TYPE_HTML;
        $content->charset = "UTF-8";
        $mimeMessage = new \Zend\Mime\Message();
        $mimeMessage->setParts(array($content));
        $message = new Message();
        $message->setTo($arrayParam['emailTo'])
            ->setEncoding('utf-8')
            ->setFrom('info@japana.vn', "info@japana.vn")
            ->setSubject('JAPANA EMAIl')
            ->setBody($mimeMessage);
        $transport = new SmtpTransport();
        $options = new SmtpOptions (array(
            'name' => 'smtp.gmail.com',
            'host' => 'smtp.gmail.com',
            'port' => '587',
            'connection_class' => 'login',
            'connection_config' => array(
                'username' => 'info@japana.vn',
                'password' => 'mwxpmcxsbvcrpiwb',
                'ssl' => 'tls'
            )
        ));
        $transport->setOptions($options);
        $transport->send($message);*/

        $mail = new PHPMailer();
        //Thiet lap thong tin nguoi gui va email nguoi gui
        $mail->IsSMTP(); // Gọi đến class xử lý SMTP
        $mail->SMTPAuth = true;                  // Sử dụng đăng nhập vào account
        $mail->SMTPAutoTLS = true;
        $mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for Gmail
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPOptions = array(
            'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
            )
        );
        $mail->Username = "info@japana.vn";
        $mail->Password = "mwxpmcxsbvcrpiwb";
        $mail->SetFrom("info@japana.vn", "Siêu thị Nhật Bản Japana");
        //Thiết lập thông tin người nhận
        $mail->AddAddress($arrayParam['emailTo']);
        $mail->AddAddress("info@japana.vn");
        //Thiết lập tiêu đề
        $mail->Subject = $subject;

        //Thiết lập định dạng font chữ
        $mail->CharSet = "utf-8";

        $mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
        //Thiết lập nội dung chính của email
        $mail->MsgHTML($body_content);
        $contentlog = "";
        if (!$mail->Send()) {
            $contentlog .= "lỗi send don hang" . PHP_EOL;
        }
       
    } //end func

}