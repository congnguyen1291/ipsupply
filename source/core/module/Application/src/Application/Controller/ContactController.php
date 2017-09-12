<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Application\Form\ContactForm;
use Application\Model\Contact;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\View\Model\ViewModel;

class ContactController extends FrontEndController
{
    public function indexAction(){
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
		$translator = $this->getServiceLocator()->get('translator');
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($translator->translate('title_site'));
        $renderer->headMeta()->appendName('description', $translator->translate('description_site'));
        $renderer->headMeta()->appendName('keyword', $translator->translate('keyword_site'));
    	$form = new ContactForm();
        $request = $this->getRequest();
    	if ($request->isPost ()) {
			$name = $request->getPost("fullname");
            $title = $request->getPost("title");
            $content = $request->getPost("content");
            $phone = $request->getPost("phone");
            $email = $request->getPost("email");
            $error = array();
            if (empty($name)) {
                $error['fullname'] = $translator->translate('txt_chua_nhap_ten');
            }
            if (empty($title)) {
                $error['title'] = $translator->translate('txt_chua_nhap_tieu_de');
            }
            if (empty($phone) || !is_numeric($phone)) {
                $error['phone'] = $translator->translate('txt_nhap_so_dien_thoai_chua_dung');
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error['email'] = $translator->translate('REPORT_EMAIL_FAIL');
            }
            if (empty($content)) {
                $error['content'] = $translator->translate('txt_ban_chua_nhap_noi_dung');
            }
            if (empty($error)) {
                $contact = new Contact();
                $contact->title = $title;
                $contact->full_name = $name;
                $contact->email = $email;
                $contact->phone = $phone;
                $contact->type = 'contact';
                $contact->content = $content;
                $contact->date_create = date('Y-m-d H:i:s');
                $contact->is_viewed = 0;
                
        		if ($this->getModelTable ( 'ContactTable' )->save ( $contact )) {
    				$html = "<table><tr><td>".$translator->translate('chao')." Admin</td></tr>
					<tr><td>Có một liên hệ cho admin từ Shop: ".$this->website['website_name']." </td></tr>
					<tr><td>Người liên hệ: {$name}</td></tr>
					<tr><td> ".$translator->translate('so_dien_thoai')." : {$phone}</td></tr>
					<tr><td>Email: {$email}</td></tr>
					<tr><td>Tiêu đề: {$title}</td></tr>
    				<tr><td>Nội dung: {$content}</td></tr></table>";
    				$html = new MimePart($html);
					$html->type = "text/html";
					$body = new MimeMessage();
					$body->setParts(array($html));
    				$message = new Message();
    				$message->addTo($this->website['website_email_admin'],"Admin website")
    				->addFrom(EMAIL_ADMIN_SEND)
					->addReplyTo($email, $name)
    				->setSubject($this->website['website_name']." - ".$title)
    				->setBody($body)
    				->setEncoding("UTF-8");  
    				// Setup SMTP transport using LOGIN authentication
    				$transport = new SmtpTransport();
    				$options   = new SmtpOptions(array(
    						'name'              => HOST_MAIL,
    						'host'              => HOST_MAIL,
                            'port'              => 25,
    						'connection_class'  => 'login',
    						'connection_config' => array(
    								'username' => USERNAME_HOST_MAIL,
    								'password' => PASSWORD_HOST_MAIL,
    						),
    				));
				
    				$transport->setOptions($options);
                    try {
                        $transport->send($message);
                    } catch(\Zend\Mail\Exception $e) {
                        $error['exception'] = $e->getMessage();
                    }catch(\Exception $ex) {
                        $error['exception'] = $e->getMessage();
                    }
                    if(!isset($error['exception'])){
                        return $this->redirect()->toRoute($this->getUrlRouterLang().'contact', array(
                            'action' =>  'thanks'
                        ));
                    }
        		}
            }
            $this->data_view['error'] = $error;
    	}
        $this->data_view['form'] = $form;
    	return $this->data_view;
    }
    
	public function sendmailContactFormAction(){      
		$request = $this->getRequest();
    	if ($request->isPost ()) {
			$name = $request->getPost("fullname",'');
            $content = $request->getPost("message",'');
            $phone = $request->getPost("phone",'');
            $email = $request->getPost("email",'');
			$utm_source = $request->getPost("utm_source",'direct');
            $utm_campaign = $request->getPost("utm_campaign",'direct');
            $page_request = urlencode($request->getPost("page_request",''));
			$utm_medium = $request->getPost("utm_medium",'direct');
			$translator = $this->getServiceLocator()->get('translator');
            $error = array();
            if (empty($email)) {
                $error['email'] = $translator->translate('txt_chua_nhap_email');
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error['email'] = $translator->translate('REPORT_EMAIL_FAIL');
            }
            if (empty($content)) {
                $error['content'] = $translator->translate('txt_ban_chua_nhap_noi_dung');
            }
			$result=$this->getModelTable ( 'ContactTable' )->saveContactPopup($name, $content, $phone, $email, $page_request, $utm_source, $utm_campaign, $utm_medium);
			$message=$translator->translate('contactnotsuccess');
			if($result==true){
				$message=$translator->translate('thanksforcontactwithus');
			}
			echo json_encode(array('flag' => $result,'message' => $message));
    	}
		die();
    }
   
	
    public function commentAction(){
    	$form = new ContactForm();
    	$request = $this->getRequest ();
    	if ($request->isPost ()) {
    		$contact = new Contact();
    		$form->setInputFilter ( $contact->getInputFilter () );
    		$form->setData ( $request->getPost () );
    		if ($form->isValid () || count ( $form->getMessages () ) == 0) {
    			$data = $request->getPost ();
    			$data['type'] = 'comment';
    			$contact->exchangeArray ( $data );
    			$data['copy'];die;
    			if ($this->getModelTable ( 'ContactTable' )->save ( $contact )) {
    				$html = "<table><tr><td> ".$translator->translate('chao')." admin , {$contact->full_name} ".$translator->translate('vua_gui_mot_lien_he')." :</td></tr>
    				<tr><td>{$contact->content}</td></tr><tr><td> ".$translator->translate('so_dien_thoai')." : {$contact->phone}</td></tr><tr><td>Email: {$contact->email}</td></tr></table>";
    	
    				$html = new MimePart($html);
    				$html->type = "text/html";
    	
    				$body = new MimeMessage();
    				$body->setParts(array($html));
    	
    	
    				$message = new Message();
    				$message->addTo(EMAIL_ADMIN_RECEIVE)
    				->addFrom($contact->email)
    				->setSubject($translator->translate('thong_tin_lien_he'))
    				->setBody($body)
    				->setEncoding("UTF-8");
    	
    						// Setup SMTP transport using LOGIN authentication
    						$transport = new SmtpTransport();
    				$options   = new SmtpOptions(array(
    				'name'              => HOST_MAIL,
    				'host'              => HOST_MAIL,
    				//'connection_class'  => 'login',
    				'connection_config' => array(
    								'username' => USERNAME_HOST_MAIL,
    								'password' => PASSWORD_HOST_MAIL,
    						),
    				));
    				
    				$transport->setOptions($options);
    				$transport->send($message);
    				
    				if($data['copy'] == 1 ){
    					$html = "<table><tr><td> ".$translator->translate('chao')." {$contact->full_name}, ".$translator->translate('vua_gui_mot_lien_he')." :</td></tr>
    					<tr><td>{$contact->content}</td></tr><tr><td> ".$translator->translate('so_dien_thoai')." : {$contact->phone}</td></tr><tr><td>Email: {$contact->email}</td></tr></table>";
    						
    					$html = new MimePart($html);
    					$html->type = "text/html";
    						
    					$body = new MimeMessage();
    					$body->setParts(array($html));
    						
    					$message = new Message();
    					$message->addTo($contact->email)
    					->addFrom(EMAIL_ADMIN_RECEIVE)
    					->setSubject($translator->translate('thong_tin_lien_he'))
    					->setBody($body)
    					->setEncoding("UTF-8");
    						
    					// Setup SMTP transport using LOGIN authentication
    					$transport = new SmtpTransport();
    							$options   = new SmtpOptions(array(
    					'name'              => HOST_MAIL,
    					'host'              => HOST_MAIL,
    					'connection_class'  => 'login',
    					'connection_config' => array(
    							'username' => USERNAME_HOST_MAIL,
    							'password' => PASSWORD_HOST_MAIL,
    					),
    					));
    						
    					$transport->setOptions($options);
    					$transport->send($message);
    				}
    				$_SESSION['msg'] = $translator->translate('gui_thanh_cong');
					$this->redirect()->toRoute($this->getUrlRouterLang().'contact/comment');
    			}
			}
		}
    						
		return array_merge(array (
            'form' => $form,
            'languages' => $this->languages,
            'current_language' => $this->current_language,
        ), $this->data_view);
    }

    public function sendmailAction(){
    	/*
    	$message = new Message();
    	$message->addTo('thanhngo100@gmail.com')
    	->addFrom(EMAIL_ADMIN)
    	->setSubject('Greetings and Salutations!')
    	->setBody("Sorry, I'm going to be late today!");
    	
    	// Setup SMTP transport using LOGIN authentication
    	$transport = new SmtpTransport();
    	$options   = new SmtpOptions(array(
    			'name'              => HOST_MAIL,
    			'host'              => HOST_MAIL,
    			'connection_class'  => 'login',
    			'connection_config' => array(
    					'username' => USERNAME_HOST_MAIL,
    					'password' => PASSWORD_HOST_MAIL,
    			),
    	));
    	$transport->setOptions($options);
    	$transport->send($message);
    	die;*/
    }

	public function thanksAction(){
		$translator = $this->getServiceLocator()->get('translator');
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($translator->translate('title_site'));
        $renderer->headMeta()->appendName('description', $translator->translate('description_site'));
        $renderer->headMeta()->appendName('keyword', $translator->translate('keyword_site'));
        return $this->data_view;
    }
    
	public function mapAction(){

        $this->viewModel->setTerminal(true);
		$address = $this->params()->fromQuery('address');

		$this->viewModel->__set('address', $address);				
		return $this->viewModel;
    }
}