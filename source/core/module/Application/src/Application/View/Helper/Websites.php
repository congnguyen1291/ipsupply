<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;

class Websites  extends App
{
    private  $website = NULL;

    public function getWebsite()
    {
        if( !empty($_SESSION['website']) ){
            return $_SESSION['website'];
        }
        return array();
    }

    public function getLogo() {
        if( !empty($this->getWebsite()) ){
            return $this->getWebsite()['logo'];
        }
        return '';
    }

    public function getName() {
        if( !empty($this->getWebsite()) ){
            return $this->getWebsite()['website_name'];
        }
        return '';
    }

    public function getLogoFooter() {
        if( !empty($this->getWebsite()) ){
            return $this->getWebsite()['logo'];
        }
        return '';
    }

    public function getUrlFacebook() {
        if( !empty($this->getWebsite()) ){
            return $this->getWebsite()['url_facebook'];
        }
        return '';
    }

    public function getUrlTwister() {
        if( !empty($this->getWebsite()) ){
            return $this->getWebsite()['url_twister'];
        }
        return '';
    }

    public function getUrlGooglePlus() {
        if( !empty($this->getWebsite()) ){
            return $this->getWebsite()['url_google_plus'];
        }
        return '';
    }

    public function getUrlRss() {
        if( !empty($this->getWebsite()) ){
            return $this->getWebsite()['url_rss'];
        }
        return '';
    }

    public function getUrlYoutube() {
        if( !empty($this->getWebsite()) ){
            return $this->getWebsite()['url_youtube'];
        }
        return '';
    }

    public function getUrlInstagram() {
        if( !empty($this->getWebsite()) ){
            return $this->getWebsite()['url_youtube'];
        }
        return '';
    }

    public function getDomain() {
        if( !empty($_SESSION['domain']) ){
            return $_SESSION['domain'];
        }
        return '';
    }

    public function getEmailSend() {
        return EMAIL_ADMIN_SEND;
    }

    public function getHostMail() {
        return HOST_MAIL;
    }

    public function getPortMail() {
        return 587;
    }

    public function getUserNameHostMail() {
        return USERNAME_HOST_MAIL;
    }

    public function getPasswordHostMail() {
        return PASSWORD_HOST_MAIL;
    }
	
    public function getBaseUrl() {
        if( !empty($_SESSION['domain']) ){
            return $_SESSION['domain'];
        }
        return 'coz.vn';
    }

    public function getStaticUrl() {
        if( !empty($this->getWebsite()) ){
            return 'static.coz.vn/'.$this->getWebsite()['websites_folder'];
        }
        return '';
    }

    public function getUrlImageStatic( $image ) {
        if( !empty($this->getWebsite()) && !empty($image) ){
            return '//'.$this->getStaticUrl().'/styles/images/'.$image;
        }
        return '';
    }

    public function getUrlCssStatic( $css ) {
        if( !empty($this->getWebsite()) && !empty($css) ){
            return '//'.$this->getStaticUrl().'/styles/css/'.$css;
        }
        return '';
    }

    public function getMinRangeSlider() {
        if( !empty($this->getWebsite())
            && !empty($this->getWebsite()['website_min_value_slider']) ){
            return $this->getWebsite()['website_min_value_slider'];
        }
        return 0;
    }

    public function getMaxRangeSlider() {
        if( !empty($this->getWebsite())
            && !empty($this->getWebsite()['website_max_value_slider']) ){
            return $this->getWebsite()['website_max_value_slider'];
        }
        return 10000000;
    }

    public function getOnlySaleLocal() {
        if( !empty($this->getWebsite())
            && !empty($this->getWebsite()['is_local']) ){
            return $this->getWebsite()['is_local'];
        }
        return 0;
    }

    public function getWebsiteContries( $is_array = TRUE ) {
        if( !empty($this->getWebsite())
            && !empty($this->getWebsite()['website_contries']) ){
            if( $is_array ){
                return explode(',', $this->getWebsite()['website_contries']);
            }
            return $this->getWebsite()['website_contries'];
        }
        return '';
    }

    public function hasLoginFacebook() {
        if( !empty($this->getWebsite())
            && !empty($this->getWebsite()['has_login_facebook']) ){
            return TRUE;
        }
        return FALSE;
    }

    public function hasLoginGoogle() {
        if( !empty($this->getWebsite())
            && !empty($this->getWebsite()['has_login_google']) ){
            return TRUE;
        }
        return FALSE;
    }

    public function hasLoginTwister() {
        if( !empty($this->getWebsite())
            && !empty($this->getWebsite()['has_login_twister']) ){
            return TRUE;
        }
        return FALSE;
    }

    public function getFacebookId() {
        if( !empty($this->getWebsite() )
            && !empty($this->getWebsite()['facebook_id']) ){
            return $this->getWebsite()['facebook_id'];
        }
        //return '911991678945728';
        return '';
    }

    public function getGoogleClientId() {
        if( !empty($this->getWebsite() )
            && !empty($this->getWebsite()['google_client_id']) ){
            return $this->getWebsite()['google_client_id'];
        }
        //return '1029192431739-2ubmp1cj55n7n7fdo3c2rg776h9a2c86.apps.googleusercontent.com';
        return '';
    }

    public function getGaCode() {
        if( !empty($this->getWebsite() )
            && !empty($this->getWebsite()['website_ga_code']) ){
            return $this->getWebsite()['website_ga_code'];
        }
        return '';
    }

    public function getTypeCropImage() {
        if( !empty($this->getWebsite() )
            && !empty($this->getWebsite()['type_crop_image']) ){
            return $this->getWebsite()['type_crop_image'];
        }
        return '';
    }

    public function getJavascript() {
        if( !empty($this->getWebsite() )
            && !empty($this->getWebsite()['javascript']) ){
            return $this->getWebsite()['javascript'];
        }
        return '';
    }

    public function getCss() {
        if( !empty($this->getWebsite() )
            && !empty($this->getWebsite()['css']) ){
            return $this->getWebsite()['css'];
        }
        return '';
    }

    function sendEmail($to, $from, $subject, $html, $text, $attachments = null)
    {
        $message = new Message();
        $message->addTo($to);
        $message->addFrom($from);
        $message->setSubject($subject);

        // HTML part
        $htmlPart           = new MimePart($html);
        $htmlPart->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
        $htmlPart->type     = "text/html; charset=UTF-8";

        // Plain text part
        $textPart           = new MimePart($text);
        $textPart->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
        $textPart->type     = "text/plain; charset=UTF-8";

        $body = new MimeMessage();
        if ($attachments) {
            // With attachments, we need a multipart/related email. First part
            // is itself a multipart/alternative message        
            $content = new MimeMessage();
            $content->addPart($textPart);
            $content->addPart($htmlPart);

            $contentPart = new MimePart($content->generateMessage());
            $contentPart->type = "multipart/alternative;\n boundary=\"" .
                $content->getMime()->boundary() . '"';

            $body->addPart($contentPart);
            $messageType = 'multipart/related';

            // Add each attachment
            foreach ($attachments as $thisAttachment) {
                $attachment = new MimePart($thisAttachment['content']);
                $attachment->filename    = $thisAttachment['filename'];
                $attachment->type        = Mime::TYPE_OCTETSTREAM;
                $attachment->encoding    = Mime::ENCODING_BASE64;
                $attachment->disposition = Mime::DISPOSITION_ATTACHMENT;
                $body->addPart($attachment);
            }

        } else {
            // No attachments, just add the two textual parts to the body
            $body->setParts(array($textPart, $htmlPart));
            $messageType = 'multipart/alternative';
        }

        // attach the body to the message and set the content-type
        $message->setBody($body);
        $message->getHeaders()->get('content-type')->setType($messageType);
        $message->setEncoding('UTF-8');

        $transport = new SmtpTransport();
        $options = new SmtpOptions(array(
            'name' => $this->getHostMail(),
            'host' => $this->getHostMail(),
            'port' => $this->getPortMail(),
            'connection_class' => 'login',
            'connection_config' => array(
                'username' => $this->getUserNameHostMail(),
                'password' => $this->getPasswordHostMail(),
            ),
        ));

        $transport->setOptions($options);
        $result = $transport->send($message);
    }
}
