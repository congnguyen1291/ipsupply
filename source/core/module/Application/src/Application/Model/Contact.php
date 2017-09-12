<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/16/14
 * Time: 3:17 PM
 */
/*
CREATE TABLE `contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `title` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `fullname` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('contact','comment') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'contact',
  `date_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_viewed` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 * */

namespace Application\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Contact implements InputFilterAwareInterface{
    public $contact_id = null;
    public $website_id = null;
    public $users_id = null;
    public $title = null;
    public $full_name = null;
    public $content = null;
    public $email = null;
    public $phone = null;
    public $type = null;
    public $date_create = null;
	
	public $product_id = null;
    public $product_name = null;
    public $is_viewed = null;
    protected $inputFilter;
    
    public function exchangeArray($data)
    {
        $this->contact_id     = (isset($data['contact_id'])) ? $data['contact_id'] : null;
        $this->website_id     = (isset($data['website_id'])) ? $data['website_id'] : null;
    	$this->users_id     = (isset($data['users_id'])) ? $data['users_id'] : null;
    	$this->title     = (isset($data['title'])) ? $data['title'] : '';
        $this->full_name = (isset($data['full_name'])) ? $data['full_name'] : '';
        $this->content = (isset($data['content'])) ? $data['content'] : '';
		$this->product_id = (isset($data['product_id'])) ? $data['product_id'] : '';
        $this->product_name = (isset($data['product_name'])) ? $data['product_name'] : '';
        $this->email = (isset($data['email'])) ? $data['email'] : '';
        $this->phone = (isset($data['phone'])) ? $data['phone'] : '';        
        $this->type = (isset($data['type']) && !empty($data['type'])) ? $data['type'] : 'contact';
        $this->date_create = (isset($data['date_create'])) ? $data['date_create'] : date('Y-m-d H:i:s');
        $this->is_viewed = (isset($data['is_viewed'])) ? $data['is_viewed'] : 0;         
    }
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
    
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();


            $inputFilter->add(array(
                'name'     => 'title',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'content',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 10,
                        ),
                    ),
                ),
            ));
            
            

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
} 