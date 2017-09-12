<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/3/14
 * Time: 10:49 AM
 */

namespace Cms\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Commission implements InputFilterAwareInterface
{
    public $commission_id;
    public $merchant_id;
    public $rate;
    public $start_date;
    public $end_date;
    public $is_published;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->commission_id   = (!empty($data['commission_id'])) ? $data['commission_id'] : NULL;
        $this->merchant_id = (!empty($data['merchant_id'])) ? $data['merchant_id'] : 0;
        $this->rate   = (!empty($data['rate'])) ? $data['rate'] : 0;
        $this->start_date   = (!empty($data['start_date'])) ? $data['start_date'] : date('Y-m-d');
        $this->end_date   = (!empty($data['end_date'])) ? $data['end_date'] : date('Y-m-d');
        $this->is_published   = (!empty($data['is_published'])) ? $data['is_published'] : 0;
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
        if ( !$this->inputFilter ) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name' => 'merchant_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'rate',
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