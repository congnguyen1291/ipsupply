<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/16/14
 * Time: 9:27 AM
 */

namespace Application\Controller;


//use Zend\View\Helper\ViewModel;
use Application\Model\User;
use Zend\View\Model\ViewModel;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class NotificationsController extends FrontEndController
{
    private $url = 'https://android.googleapis.com/gcm/send';

    public function registerAction()
    {
        $translator = $this->getServiceLocator()->get('translator');
        $request = $this->getRequest();
        $item = array('flag' => FALSE, 'msg' => 'not found');

        if ($request->isPost()) {
            $registration_id = $request->getPost("id");
            //$registration_id = 'ciulxVgVoSQ:APA91bFGkQYiY4BvjAk8i9upeGGmpOGNNYTz5jEMan3rE1UNahYX0IS07cZghQpymxQpXajcZPvOx-JHZA5ZFgJJy9cGKgoS1zhXFXhuC2z9GQcpWBSBvlWZ79c_4jBS_B2be1t7ykUm';
            if( !empty($registration_id) ){
                $fields = array(
                    'registration_ids' => array($registration_id),
                );

                $headers = array(
                    'Authorization: key=AAAA76CmOHs:APA91bHsRIt8KAjFVSSiA55_zA7NPvCceAJF8ffFqCzOdUalAMqOne8omfgHZjoTdYiKvuB62vHHLtUqDYwuwGTprrfyL-ExmvJ0IRY1KfaZzfrfDCtr6STq_cc5BIR4EBjYj0xWfUBf',
                    'Content-Type: application/json'
                );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $this->url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                $result = curl_exec($ch);
                if ($result === FALSE) {
                    $item = array('flag' => FALSE, 'msg' => 'Push msg send failed in curl: ' . curl_error($ch));
                }
                curl_close($ch);
                $item = array('flag' => TRUE, 'msg' => 'SUCCESS', 'result' => $result);
            }
        }
        echo json_encode($item);die();
    }

    public function testAction()
    {
        $translator = $this->getServiceLocator()->get('translator');
        $request = $this->getRequest();
        $item = array("data" => array(
                                    "title" => "Bạn đã có website chua?",
                                    "body" => "Với COZ platform bạn sẽ có được những gì bạn mong muốn",
                                    "icon" => "https://coz.vn/styles/images/logo.png",
                                    "tag" => "tag",
                                    "url" => "https://coz.vn"));
        echo json_encode($item);die();
    }

} 