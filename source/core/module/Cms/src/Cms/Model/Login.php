<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/23/14
 * Time: 10:00 AM
 */

namespace Cms\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Login implements InputFilterAwareInterface
{
    public $website_id;
    public $username;
    public $password;
    public $rememberme;
    protected $inputFilter;

    public function exchangeArray($data){
        $this->website_id = (!empty($data['website_id'])) ? $data['website_id'] : NULL;
        $this->username = (!empty($data['username'])) ? $data['username'] : NULL;
        $this->password = (!empty($data['password'])) ? $data['password'] : NULL;
        $this->rememberme = (!empty($data['rememberme'])) ? $data['rememberme'] : 0;
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
        if(!$this->inputFilter){
            $inputFilter = new InputFilter();
            $inputFilter->add(array(
                'name' => 'username',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 100,
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'password',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 100,
                        ),
                    ),
                ),
            ));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}