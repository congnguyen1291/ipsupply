<?php
namespace Cms\Controller;
use Cms\Lib\Paging;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Filter\File\LowerCase;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;

use JasonGrimes\Paginator;

class ContactController extends BackEndController{

    public function __construct(){
        parent::__construct();
        $this->data_view['current'] = 'contact';
    }

    public function indexAction(){
    	$language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $id = $this->params()->fromRoute('id', 0);
        $q = $this->params()->fromQuery('q', '');
        $type = $this->params()->fromQuery('type', 0);

        $params = array();
        $params['page'] = $page;
        $params['limit'] = $limit;

        $total = $this->getModelTable('ContactTable')->getTotalContact($params);
        $contacts = $this->getModelTable('ContactTable')->getContacts($params);

        $link = '/cms/contact?page=(:num)'.( !empty($q) ? '&q='.$q.'&type='.$type : '');
        $paginator = new Paginator($total, $limit, $page, $link);
        
        $this->data_view['contacts'] = $contacts;
        $this->data_view['limit'] = $limit;
        $this->data_view['page'] = $page;
        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['q'] = $q;
        $this->data_view['type'] = $type;
        return $this->data_view;
    }

    public function emailNewsletterAction(){
    	$language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $id = $this->params()->fromRoute('id', 0);
        $q = $this->params()->fromQuery('q', '');
        $type = $this->params()->fromQuery('type', 0);

        $params = array();
        $params['page'] = $page;
        $params['limit'] = $limit;

        $total = $this->getModelTable('EmailNewLetterTable')->countAll($params);
        $emails = $this->getModelTable('EmailNewLetterTable')->fetchAll($params);

        $link = '/cms/contact/email-newsletter?page=(:num)'.( !empty($q) ? '&q='.$q.'&type='.$type : '');
        $paginator = new Paginator($total, $limit, $page, $link);
        
        $this->data_view['emails'] = $emails;
        $this->data_view['limit'] = $limit;
        $this->data_view['page'] = $page;
        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['q'] = $q;
        $this->data_view['type'] = $type;
        return $this->data_view;
    }

    public function replayAction(){
    	$websitesHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Websites');
    	$translator = $this->getServiceLocator()->get('translator');
    	$id = $this->params()->fromRoute('id', NULL);
        if(!$id){
            return $this->redirect()->toRoute('cms/contact');
        }
        try{
            $contact = $this->getModelTable('ContactTable')->getContact($id);
            $replays = $this->getModelTable('ContactTable')->getReplays($id);
            $this->getModelTable('ContactTable')->updateContact(array('readed' => 1), array('id' => $id) );
        }catch (\Exception $ex){
            return $this->redirect()->toRoute('cms/contact');
        }
        $error = '';
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $files = $this->params()->fromFiles('file');
            if(!trim($data['content'])){
                $error = $translator->translate('txt_noi_dung_tra_loi_trong');
            }else{
                try {
	                $viewModel = new ViewModel();
			        $viewModel->setTerminal(true);
			        $viewModel->setTemplate("cms/contact/email");
			        $viewModel->setVariables(
			        	array('content' => $data['content'])
			        );
			        $viewRender = $this->getServiceLocator()->get('ViewRenderer');
			        $html = $viewRender->render($viewModel);

			        $attachments = array();
			        $upload_url = '';
			        if( !empty($files) ){
			            $websiteFolder = PATH_BASE_ROOT . '/temps';
			            if(!is_dir($websiteFolder)){
			                @mkdir ( $websiteFolder, 0777 );
			            }
			            $size = $files['size'];//max 20M
			            if($size > 20971520)
			            {
			                @unlink($files['tmp_name']);
			            }else{
			            	$temp = preg_split ( '/[\/\\\\]+/', $files["name"] );
			                $filename = $temp [count ( $temp ) - 1];
			                if ( !empty($filename) ) {
				            	$name = $this->file_name ( $filename );
	                    		$extention = $this->file_extension ( $filename );
	                    		if( in_array($extention, array('rar', 'zip')) ){
					            	$upload_url = $websiteFolder. "/" . $name.'.'.$extention;
					            	if (copy ( $files["tmp_name"], $upload_url )) {
					            		$attachments[] = array(
								            				'content' => file_get_contents($upload_url),
								            				'filename' => $name.'.'.$extention,
								            			);
					            	}
					            }
						    }
			            }
				    }

	                $htmlPart = new MimePart($html);
			        $htmlPart->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
        			$htmlPart->type     = "text/html; charset=UTF-8";

        			$textPart           = new MimePart($data['content']);
			        $textPart->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
			        $textPart->type     = "text/plain; charset=UTF-8";

			        $listUrlFile = array();
			        $body = new MimeMessage();
			        if( empty($attachments) ){
				        $body->setParts(array($textPart));
            			$messageType = 'text/html; charset=UTF-8';
				    }else{
				    	$content = new MimeMessage();
				    	$content->addPart($textPart);
			            $content->addPart($htmlPart);
			            $contentPart = new MimePart($content->generateMessage());
			            $contentPart->type = "multipart/alternative;\n boundary=\"" . $content->getMime()->boundary() . '"';
			            $body->addPart($contentPart);
			            $messageType = 'multipart/related';
			            foreach ($attachments as $thisAttachment) {
			                $attachment = new MimePart($thisAttachment['content']);
			                $attachment->filename    = $thisAttachment['filename'];
			                $attachment->type        = Mime::TYPE_OCTETSTREAM;
			                $attachment->encoding    = Mime::ENCODING_BASE64;
			                $attachment->disposition = Mime::DISPOSITION_ATTACHMENT;
			                $body->addPart($attachment);
			                $listUrlFile[] = '/temps/'.$thisAttachment['filename'];
			            }
				    }
				    
					$listemailcc=explode(",",$this->website['website_email_customer']);
					if(count($listemailcc)<=0){
						$listemailcc=$this->website['website_email_customer'];
					}
			        $message = new Message();
			        $message->addTo($contact->email)
			            ->addFrom($websitesHelper->getEmailSend(), $this->website['website_name'])
						->addCc($listemailcc)
						->addReplyTo($listemailcc, $translator->translate('txt_admin_website'))
			            ->setSubject($this->website['website_name'].$translator->translate('txt_replay_lien_he'))
			            ->setBody($body)
			            ->setEncoding("UTF-8");
			            //->getHeaders()->get('content-type')->setType($messageType);

			        // Setup SMTP transport using LOGIN authentication
			        $transport = new SmtpTransport();
			        $options = new SmtpOptions(array(
			            'name' => $websitesHelper->getHostMail(),
			            'host' => $websitesHelper->getHostMail(),
			            'port' => $websitesHelper->getPortMail(),
			            'connection_class' => 'login',
			            'connection_config' => array(
			                'username' => $websitesHelper->getUserNameHostMail(),
			                'password' => $websitesHelper->getPasswordHostMail(),
			            ),
			        ));

			        $transport->setOptions($options);
		            $result=$transport->send($message);
		            $row = array(
		            		'users_id' => $data['users_id'],
            				'id' => $data['id'],
		            		'content' => $data['content'],
		            		'has_attachment' => (empty($attachments) ? 0 : 1),
		            		'file' => json_encode($listUrlFile)
		            	);
		            $this->getModelTable('ContactTable')->replay($row);
		            if( !empty($upload_url) ){
			            //@unlink($upload_url);
			        }
		            return $this->redirect()->toRoute('cms/contact');
		        } catch(\Zend\Mail\Exception $e) {
		        	$error = $translator->translate('txt_khong_gui_mail_duoc');
		        }catch(\Exception $ex) {
		        	$error = $translator->translate('txt_khong_luu_gui_mail_duoc');
		        }
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

            }
        }
        $this->data_view['contact'] = $contact;
        $this->data_view['replays'] = $replays;
        $this->data_view['error'] = $error;
        return $this->data_view;
    }

    public function deleteAction(){
		$request = $this->getRequest();
		if($request->isPost()){
			$ids = $request->getPost('cid');
			if(count($ids)){
				try{
					$this->getModelTable('ContactTable')->deleteContact($ids);
					/*strigger change namespace cached*/
                    $this->updateNamespaceCached();

				}catch(\Exception $ex){
					
				}
			}
		}else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $this->getModelTable('ContactTable')->deleteContact($id);
                $this->updateNamespaceCached();
            }
        }
		return $this->redirect()->toRoute('cms/contact');
	}

	public function deleteEmailNewsletterAction(){
		$request = $this->getRequest();
		if($request->isPost()){
			$ids = $request->getPost('cid');
			if(count($ids)){
				try{
					$this->getModelTable('EmailNewLetterTable')->deleteEmail($ids);
					/*strigger change namespace cached*/
                    $this->updateNamespaceCached();

				}catch(\Exception $ex){
					
				}
			}
		}else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $this->getModelTable('EmailNewLetterTable')->deleteEmail($id);
                $this->updateNamespaceCached();
            }
        }
		return $this->redirect()->toRoute('cms/contact', array('action' => 'email-newsletter'));
	}

}