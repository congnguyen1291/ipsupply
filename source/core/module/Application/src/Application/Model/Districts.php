<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/16/14
 * Time: 3:17 PM
 */

namespace Application\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Districts
{
    public $districts_id;
    public $cities_id;
    public $districts_title;
    public $is_published;
    public $is_delete;
    public $ordering;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->districts_id   = (!empty($data['districts_id'])) ? $data['districts_id'] : NULL;
        $this->cities_id   = (!empty($data['cities_id'])) ? $data['cities_id'] : NULL;
        $this->districts_title = (!empty($data['districts_title'])) ? $data['districts_title'] : '';
        $this->is_published   = (!empty($data['is_published'])) ? $data['is_published'] : 0;
        $this->is_delete   = (!empty($data['is_delete'])) ? $data['is_delete'] : 0;
        $this->ordering   = (!empty($data['ordering'])) ? $data['ordering'] : 0;
    }
    
}