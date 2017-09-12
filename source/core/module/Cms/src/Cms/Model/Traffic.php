<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/6/14
 * Time: 9:22 AM
 */

namespace Cms\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Traffic implements InputFilterAwareInterface{
    public $traffic_id;
    public $session_id;
    public $users_id;
    public $email;
    public $date_create;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->traffic_id        = (!empty($data['traffic_id'])) ? $data['traffic_id'] : NULL;
        $this->session_id        = (!empty($data['session_id'])) ? $data['session_id'] : '';
        $this->users_id          = (!empty($data['users_id'])) ? $data['users_id'] : 0;
        $this->email             = (!empty($data['email'])) ? $data['email'] : '';
        $this->date_create         = (!empty($data['date_create'])) ? $data['date_create'] : date('Y-m-d H:i:s');
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
                'name' => 'traffic_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
} 