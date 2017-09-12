<?php
namespace Cms\Controller;
use Cms\Lib\Paging;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Filter\File\LowerCase;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class ContactController extends BackEndController{

    public function __construct(){
        parent::__construct();
        $this->data_view['current'] = 'contact';
    }

    public function indexAction(){
    	$page = isset($_GET['page']) ? $_GET['page'] : 0;
    	$this->intPage = $page;
        $page_size = $this->intPageSize;
        $link = '';
        $page = isset($_GET['page']) ? $_GET['page'] : 0;

        $total = $this->getModelTable('ContactTable')->getTotalContact();
        $objPage = new Paging($total, $page, $page_size, $link);
        $paging = $objPage->getListFooter($link);
        $contacts = $this->getModelTable('ContactTable')->getContacts();
        
        $this->data_view['contacts'] = $contacts;
        $this->data_view['paging'] = $paging;
        return $this->data_view;
    }

    public function replayAction(){
    	$websitesHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Websites');
    	$id = $this->params()->fromRoute('id', NULL);
        if(!$id){
            return $this->redirect()->toRoute('cms/contact');
        }
        try{
            $contact = $this->getModelTable('ContactTable')->getContact($id);
            $replays = $this->getModelTable('ContactTable')->getReplays($id);
            $this->getModelTable('ContactTable')->update(array('readed' => 1), array('id' => $id) );
        }catch (\Exception $ex){
            return $this->redirect()->toRoute('cms/contact');
        }
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            if(!trim($data['content'])){
                $error[] = "Nội dung trả lời không được bỏ trống";
            }else{
                $this->getModelTable('ContactTable')->replay($data);
                try {
	                $viewModel = new ViewModel();
			        $viewModel->setTerminal(true);
			        $viewModel->setTemplate("cms/contact/email");
			        $viewModel->setVariables(
			        	array('content' => $data['content'])
			        );
			        $viewRender = $this->getServiceLocator()->get('ViewRenderer');
			        $html = $viewRender->render($viewModel);

	                $html = new MimePart($html);
			        $html->type = "text/html";
			        $body = new MimeMessage();
			        $body->setParts(array($html));

					$listemailcc=explode(",",$this->website['website_email_customer']);
					if(count($listemailcc)<=0){
						$listemailcc=$this->website['website_email_customer'];
					}
			        $message = new Message();
			        $message->addTo($contact->email)
			            ->addFrom($websitesHelper->getEmailSend(), $this->website['website_name'])
						->addCc($listemailcc)
						->addReplyTo($listemailcc, "Admin website")
			            ->setSubject($this->website['website_name'].' replay liên hệ')
			            ->setBody($body)
			            ->setEncoding("UTF-8");

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
		            return $this->redirect()->toRoute('cms/contact');
		        } catch(\Zend\Mail\Exception $e) {
		            //return false;
		        }catch(\Exception $ex) {
		            //return false;
		        }
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

            }
        }
        $this->data_view['contact'] = $contact;
        $this->data_view['replays'] = $replays;
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
		}
		return $this->redirect()->toRoute('cms/contact');
	}

}