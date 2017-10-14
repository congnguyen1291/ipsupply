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
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\View\Model\ViewModel;

use Application\Model\Contact;
use Application\Form\ContactForm;
use Application\Model\AnythingContact;

class ContactController extends FrontEndController
{
    public function indexAction(){
        $websitesHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Websites');
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        $translator = $this->getServiceLocator()->get('translator');
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($translator->translate('title_site'));
        $renderer->headMeta()->appendName('description', $translator->translate('description_site'));
        $renderer->headMeta()->appendName('keyword', $translator->translate('keyword_site'));
        $form = new ContactForm();
        $request = $this->getRequest();

        if ($request->isPost ()) {
            $email = $request->getPost ('email', '');
            $first_name = $request->getPost ('first_name', '');
            $middle_name = $request->getPost ('middle_name', '');
            $last_name = $request->getPost ('last_name', '');
            $fullname = $request->getPost ('fullname', '');
            if( !empty($email) && (!empty($fullname) || ( !empty($first_name) && !empty($last_name))) ){
                $contact = new AnythingContact();
                $data = $request->getPost ();
                $contact->exchangeArray ( $data );
                $contact->website_id = $this->website->website_id;
                $contact->fullname = $first_name.' '.$middle_name.' '.$last_name;
                if ( $this->getModelTable ( 'AnythingContactTable' )->saveAnythingContact ( $contact ) ) {
                    $html = "<table>
                                <tr>
                                    <td> 
                                        {$translator->translate('chao')} Admin , 
                                        {$contact->fullname} 
                                        {$translator->translate('vua_gui_mot_lien_he')}:
                                    </td>
                                </tr>
                                <tr>
                                    <td>{$contact->title}</td>
                                </tr>
                                <tr>
                                    <td>{$contact->description}</td>
                                </tr>
                                <tr>
                                    <td> 
                                        {$translator->translate('so_dien_thoai')} : {$contact->telephone}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Email: {$contact->email}</td>
                                </tr>
                            </table>";
        
                    $html = new MimePart($html);
                    $html->type = "text/html";
        
                    $body = new MimeMessage();
                    $body->setParts(array($html));
        
        
                    $message = new Message();
                    $message->addTo($websitesHelper->getEmailSend())
                    ->addFrom($contact->email)
                    ->setSubject($translator->translate('thong_tin_lien_he'.$contact->type))
                    ->setBody($body)
                    ->setEncoding("UTF-8");
                    
                    $transport = new SmtpTransport();
                    $options   = new SmtpOptions(array(
                        'name' => $websitesHelper->getHostMail(),
                        'host' => $websitesHelper->getHostMail(),
                        'port' => $websitesHelper->getPortMail(),
                        'connection_class'  => 'login',
                        'connection_config' => array(
                            'username' => $websitesHelper->getUserNameHostMail(),
                            'password' => $websitesHelper->getPasswordHostMail(),
                        ),
                    ));

                    $transport->setOptions($options);

                    try {
                        $transport->send($message);
                    } catch(\Zend\Mail\Exception $e) {
                    }catch(\Exception $ex) {
                    }
                    return $this->redirect()->toRoute($this->getUrlRouterLang().'contact', array(
                        'action' =>  'thanks'
                    ));
                }
            }
        }
        $this->data_view['form'] = $form;
        return $this->data_view;
    }

    public function sellYourCiscoAction(){
        $websitesHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Websites');
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
		$translator = $this->getServiceLocator()->get('translator');
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($translator->translate('title_site'));
        $renderer->headMeta()->appendName('description', $translator->translate('description_site'));
        $renderer->headMeta()->appendName('keyword', $translator->translate('keyword_site'));
        
    	$form = new ContactForm();
        $request = $this->getRequest();
        if ($request->isPost ()) {
            $email = $request->getPost ('email', '');
            $fullname = $request->getPost ('fullname', '');
            $telephone = $request->getPost ('telephone', '');
            $address = $request->getPost ('address', '');
            $description = $request->getPost ('description', '');

            if( !empty($email) && !empty($fullname) 
                && !empty($telephone) && !empty($address) && !empty($description) ){
                $image = $this->params()->fromFiles('image');

                $file = '';
                $upload_url = '';
                if( !empty($image) ){
                    $websiteFolder = PATH_BASE_ROOT . '/temps';
                    if(!is_dir($websiteFolder)){
                        @mkdir ( $websiteFolder, 0777 );
                    }
                    $size = $image['size'];//max 20M
                    if($size > 20971520)
                    {
                        @unlink($image['tmp_name']);
                    }else{
                        $temp = preg_split ( '/[\/\\\\]+/', $image["name"] );
                        $filename = $temp [count ( $temp ) - 1];
                        if ( !empty($filename) ) {
                            $name = $this->file_name ( $filename );
                            $extention = $this->file_extension ( $filename );
                            if( in_array($extention, array('jpeg','png','jpg','gif','ico')) ){
                                $upload_url = $websiteFolder. "/" . $name.'.'.$extention;
                                if (copy ( $image["tmp_name"], $upload_url )) {
                                    $file = '/temps/' . $name.'.'.$extention;
                                }
                            }
                        }
                    }
                }
                $contact = new AnythingContact();
                $data = $request->getPost ();
                $contact->exchangeArray ( $data );
                $contact->website_id = $this->website->website_id;
                $contact->file = $file;

                if ( $this->getModelTable ( 'AnythingContactTable' )->saveAnythingContact ( $contact ) ) {
                    $html = "<table>
                                <tr>
                                    <td> 
                                        {$translator->translate('chao')} Admin , 
                                        {$contact->fullname} 
                                        {$translator->translate('vua_gui_mot_lien_he')}:
                                    </td>
                                </tr>
                                <tr>
                                    <td>{$contact->description}</td>
                                </tr>
                                <tr>
                                    <td> 
                                        {$translator->translate('so_dien_thoai')} : {$contact->telephone}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Email: {$contact->email}</td>
                                </tr>
                                <tr>
                                    <td>Address: {$contact->address}</td>
                                </tr>
                                ".(!empty($file) ? '<tr><td><img src="'.$file.'" style="max-width:100%" /></td></tr>' : '')."
                            </table>";
        
                    $html = new MimePart($html);
                    $html->type = "text/html";
        
                    $body = new MimeMessage();
                    $body->setParts(array($html));
        
        
                    $message = new Message();
                    $message->addTo($websitesHelper->getEmailSend())
                    ->addFrom($contact->email)
                    ->setSubject($translator->translate('thong_tin_lien_he'.$contact->type))
                    ->setBody($body)
                    ->setEncoding("UTF-8");
                    
                    $transport = new SmtpTransport();
                    $options   = new SmtpOptions(array(
                        'name' => $websitesHelper->getHostMail(),
                        'host' => $websitesHelper->getHostMail(),
                        'port' => $websitesHelper->getPortMail(),
                        'connection_class'  => 'login',
                        'connection_config' => array(
                            'username' => $websitesHelper->getUserNameHostMail(),
                            'password' => $websitesHelper->getPasswordHostMail(),
                        ),
                    ));

                    $transport->setOptions($options);

                    try {
                        $transport->send($message);
                    } catch(\Zend\Mail\Exception $e) {
                    }catch(\Exception $ex) {
                    }
                    return $this->redirect()->toRoute($this->getUrlRouterLang().'contact', array(
                        'action' =>  'thanks'
                    ));
                }
            }
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
        $translator = $this->getServiceLocator()->get('translator');
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

    public function anythingAction(){
        $websitesHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Websites');
        $translator = $this->getServiceLocator()->get('translator');
        $item = array( 'flag' => FALSE, 'msg' => $translator->translate('txt_not_found'));
        $form = new ContactForm();
        $request = $this->getRequest ();
        if ($request->isPost ()) {
            $email = $request->getPost ('email', '');
            $first_name = $request->getPost ('first_name', '');
            $middle_name = $request->getPost ('middle_name', '');
            $last_name = $request->getPost ('last_name', '');
            $fullname = $request->getPost ('fullname', '');
            if( !empty($email) && (!empty($fullname) || ( !empty($first_name) && !empty($last_name))) ){
                $contact = new AnythingContact();
                $data = $request->getPost ();
                $contact->exchangeArray ( $data );
                $contact->website_id = $this->website->website_id;
                $contact->fullname = $first_name.' '.$middle_name.' '.$last_name;
                if ( $this->getModelTable ( 'AnythingContactTable' )->saveAnythingContact ( $contact ) ) {
                    $html = "<table>
                                <tr>
                                    <td> 
                                        {$translator->translate('chao')} Admin , 
                                        {$contact->fullname} 
                                        {$translator->translate('vua_gui_mot_lien_he')}:
                                    </td>
                                </tr>
                                <tr>
                                    <td>{$contact->title}</td>
                                </tr>
                                <tr>
                                    <td>{$contact->description}</td>
                                </tr>
                                <tr>
                                    <td> 
                                        {$translator->translate('so_dien_thoai')} : {$contact->telephone}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Email: {$contact->email}</td>
                                </tr>
                            </table>";
        
                    $html = new MimePart($html);
                    $html->type = "text/html";
        
                    $body = new MimeMessage();
                    $body->setParts(array($html));
        
        
                    $message = new Message();
                    $message->addTo($websitesHelper->getEmailSend())
                    ->addFrom($contact->email)
                    ->setSubject($translator->translate('thong_tin_lien_he'.$contact->type))
                    ->setBody($body)
                    ->setEncoding("UTF-8");
                    
                    $transport = new SmtpTransport();
                    $options   = new SmtpOptions(array(
                        'name' => $websitesHelper->getHostMail(),
                        'host' => $websitesHelper->getHostMail(),
                        'port' => $websitesHelper->getPortMail(),
                        'connection_class'  => 'login',
                        'connection_config' => array(
                            'username' => $websitesHelper->getUserNameHostMail(),
                            'password' => $websitesHelper->getPasswordHostMail(),
                        ),
                    ));

                    $transport->setOptions($options);

                    try {
                        $transport->send($message);
                    } catch(\Zend\Mail\Exception $e) {
                    }catch(\Exception $ex) {
                    }
                    
                    $item = array( 'flag' => TRUE, 'msg' => $translator->translate('txt_contact_success'));
                }else{
                    $item = array( 'flag' => FALSE, 'msg' => $translator->translate('txt_co_mot_loi'));
                }
            }else{
                $item = array( 'flag' => FALSE, 'msg' => $translator->translate('txt_chua_dien_day_du_thong_tin'));
            }
        }
                            
        echo json_encode($item);
        die();
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