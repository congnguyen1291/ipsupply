<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/3/14
 * Time: 10:49 AM
 */

namespace Application\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Assign implements InputFilterAwareInterface
{
    public $assign_id;
    public $invoice_id;
    public $users_id;
    public $assign_shipping;
    public $assign_code;
    public $assign_name;
    public $total_money;
    public $assign_status;
    public $assign_read;
    public $assign_unit;
    public $ratio_usd;
    public $assign_date;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->assign_id   = (!empty($data['assign_id'])) ? $data['assign_id'] : NULL;
        $this->users_id   = (!empty($data['users_id'])) ? $data['users_id'] : 0;
        $this->invoice_id   = (!empty($data['invoice_id'])) ? $data['invoice_id'] : 0;
        $this->assign_shipping   = (!empty($data['assign_shipping'])) ? $data['assign_shipping'] : 0;
        $this->assign_code   = (!empty($data['assign_code'])) ? $data['assign_code'] : '';
        $this->assign_name = (!empty($data['assign_name'])) ? $data['assign_name'] : '';
        $this->total_money   = (!empty($data['total_money'])) ? $data['total_money'] : 0;
        $this->assign_status   = (!empty($data['assign_status'])) ? $data['assign_status'] : 'pending';
        $this->assign_read   = (!empty($data['assign_read'])) ? $data['assign_read'] : 0;
        $this->assign_unit   = (!empty($data['assign_unit'])) ? $data['assign_unit'] : '';
        $this->ratio_usd   = (!empty($data['ratio_usd'])) ? $data['ratio_usd'] : 1;
        $this->assign_date   = (!empty($data['assign_date'])) ? $data['assign_date'] : date('Y-m-d H:m:s');
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
                'name' => 'assign_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'assign_code',
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
                'name' => 'assign_name',
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