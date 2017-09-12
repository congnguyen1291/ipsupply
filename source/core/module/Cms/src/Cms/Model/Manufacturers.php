<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/2/14
 * Time: 4:32 PM
 */

namespace Cms\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Manufacturers implements InputFilterAwareInterface{
    public $manufacturers_id;
    public $website_id;
    public $manufacturers_name;
    public $thumb_image;
    public $description;
    public $warranty_description;
    public $is_published;
    public $is_delete;
    public $date_create;
    public $date_update;
    public $ordering;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->manufacturers_id     = (!empty($data['manufacturers_id'])) ? $data['manufacturers_id'] : null;
        $this->website_id     = (!empty($data['website_id'])) ? $data['website_id'] : null;
        $this->manufacturers_name = (!empty($data['manufacturers_name'])) ? $data['manufacturers_name'] : null;
        $this->thumb_image = (!empty($data['thumb_image'])) ? $data['thumb_image'] : null;
        $this->description = (!empty($data['description'])) ? $data['description'] : null;
        $this->warranty_description = (!empty($data['warranty_description'])) ? $data['warranty_description'] : null;
        $this->is_published  = (!empty($data['is_published'])) ? $data['is_published'] : 0;
        $this->is_delete  = (!empty($data['is_delete'])) ? $data['is_delete'] : 0;
        $this->date_create = (!empty($data['date_create'])) ? $data['date_create'] : date('Y-m-d H:i:s');
        $this->date_update = (!empty($data['date_update'])) ? $data['date_update'] : date('Y-m-d H:i:s');
        $this->ordering = (!empty($data['ordering'])) ? $data['ordering'] : 0;
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
                'name'     => 'manufacturers_id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'manufacturers_name',
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
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
} 