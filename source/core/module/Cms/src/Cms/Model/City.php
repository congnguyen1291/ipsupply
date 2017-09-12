<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/16/14
 * Time: 3:17 PM
 */

namespace Cms\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class City
{
    public $cities_id;
    public $country_id;
    public $area_id;
    public $cities_title;
    public $is_published;
    public $is_delete;
    public $ordering;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->cities_id   = (!empty($data['cities_id'])) ? $data['cities_id'] : NULL;
        $this->country_id   = (!empty($data['country_id'])) ? $data['country_id'] : NULL;
        $this->area_id = (!empty($data['area_id'])) ? $data['area_id'] : 0;
        $this->cities_title   = (!empty($data['cities_title'])) ? $data['cities_title'] : '';
        $this->is_published   = (!empty($data['is_published'])) ? $data['is_published'] : 0;
        $this->is_delete   = (!empty($data['is_delete'])) ? $data['is_delete'] : 0;
        $this->ordering   = (!empty($data['ordering'])) ? $data['ordering'] : 0;
    }
    
}