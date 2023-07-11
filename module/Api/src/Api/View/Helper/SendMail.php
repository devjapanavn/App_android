<?php
namespace Api\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\Mime\Mime;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
class SendMail extends AbstractHelper{
	public function __invoke($arrayParam = null) {		$body = '<div style="width:600px; float:left; border-radius:3px; padding:20px; border:#CCC solid 1px;">					<div style="width:600px; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:20px; float:left;">					  <div style="width:600px; float:left;">					    <p style="margin-top:10px; border-radius: 3px; padding:5px; text-align:center; font-weight:bold;">Xin chào Quý khách</p>					    <p style="margin-top:10px;">Quý khách đã nhận được một yêu cầu liên hệ mới từ '.DOMAIN.'</p>';					if(isset($arrayParam['post']['title_email']) == true && $arrayParam['post']['title_email'] != ''){			$body .= '<div style="width:600px; margin-top:10px; float:left;">					      <div style="width:100px; font-weight:bold; float:left;">Tiêu đề liên hệ: </div>					      <div style="width:428px; height:22px; line-height:22px; text-indent:10px; margin-left:20px; float:left;">'.$arrayParam['post']['title_email'].'</div>					  </div>';		}		if(isset($arrayParam['post']['name']) == true && $arrayParam['post']['name'] != ''){			$body .= '<div style="width:600px; margin-top:10px; float:left;">					      <div style="width:100px; font-weight:bold; float:left;">Khách hàng: </div>					      <div style="width:428px; height:22px; line-height:22px; text-indent:10px; margin-left:20px; float:left;">'.$arrayParam['post']['name'].'</div>					  </div>';		}		if(isset($arrayParam['post']['telephone']) == true && $arrayParam['post']['telephone'] != ''){
		    $body .=  '<div style="width:600px; margin-top:10px; float:left;">
					      <div style="width:100px; font-weight:bold; float:left;">Điện thoại: </div>
					      <div style="width:428px; height:22px; line-height:22px; text-indent:10px; margin-left:20px; float:left;">'.$arrayParam['post']['telephone'].'
					      </div>
					    </div>';
		}		if(isset($arrayParam['post']['email']) == true && $arrayParam['post']['email'] != ''){
		    $body .= '<div style="width:600px; margin-top:10px; float:left;">
					      <div style="width:100px; font-weight:bold; float:left;">Email: </div>
					      <div style="width:428px; height:22px; line-height:22px; text-indent:10px; margin-left:20px; float:left;">'.$arrayParam['post']['email'].'</div>
					    </div>';
		}		if(isset($arrayParam['post']['address']) == true && $arrayParam['post']['address'] != ''){
		    $body .= '<div style="width:600px; margin-top:10px; float:left;">
						      <div style="width:100px; font-weight:bold; float:left;">Địa chỉ: </div>
						      <div style="width:428px; height:22px; line-height:22px; text-indent:10px; margin-left:20px; float:left;">
							'.$arrayParam['post']['address'].'</div>
						    </div>';
		}		if(isset($arrayParam['post']['comment']) == true && $arrayParam['post']['comment'] != ''){		$body .=  '<div style="width:600px; font-weight:bold; margin:20px 0px 20px 0px; float:left;">Nội dung liên hệ: </div>					    <div style="width:600px; margin-bottom:30px; color:#333; float:left;">					      '.$arrayParam['post']['comment'].'					    </div>					  </div>';		}		$body .'</div></div>';
		$content 			= new \Zend\Mime\Part($body);
		$content->type 		= Mime::TYPE_HTML;
		$content->charset 	= "UTF-8";
		$mimeMessage = new \Zend\Mime\Message();
		$mimeMessage->setParts(array($content));
		$message = new Message();
		$message->setTo($arrayParam['emailTo'])
				->setEncoding ( 'utf-8' )
				->setFrom( 'notify@saigonhitech.com.vn',"notify@saigonhitech.com.vn")
				->setSubject (DOMAIN.' thông báo' )
				->setBody ($mimeMessage);
		$transport = new SmtpTransport();
		$options = new SmtpOptions ( array (				'name' => 'email-smtp.us-east-1.amazonaws.com',				'host' => 'email-smtp.us-east-1.amazonaws.com',				'port' => '587',				'connection_class' => 'login',				'connection_config' => array (						'username' => 'AKIAJ6CONRJIV7YOERLA',						'password' => 'Amc4iO/CjPgDH3UeGh6J9E5ekW/3XvE8ARE7QpHUrkk2',						'ssl' => 'tls'				)		));
		$transport->setOptions ( $options );
		$transport->send ( $message );
	}
}