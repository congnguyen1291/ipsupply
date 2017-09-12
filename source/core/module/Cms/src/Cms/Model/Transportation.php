<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/11/14
 * Time: 11:54 AM
 */
namespace Cms\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Transportation implements InputFilterAwareInterface
{
    public $transportation_id;
    public $website_id;
    public $shipping_class;
    public $transportation_type;
    public $transportation_title;
    public $transportation_description;
    public $is_published;
    public $is_delete;
    protected $inputFilter;
    public function exchangeArray($data)
    {
        $this->transportation_id = (!empty($data['transportation_id'])) ? $data['transportation_id'] : NULL;
        $this->website_id = (!empty($data['website_id'])) ? $data['website_id'] : $_SESSION['CMSMEMBER']['website_id'];
        $this->shipping_class = (!empty($data['shipping_class'])) ? $data['shipping_class'] : 0;
        $this->transportation_type = (!empty($data['transportation_type'])) ? $data['transportation_type'] : 0;
        $this->transportation_title = (!empty($data['transportation_title'])) ? $data['transportation_title'] : NULL;
        $this->transportation_description = (!empty($data['transportation_description'])) ? $data['transportation_description'] : NULL;
        $this->is_published = (!empty($data['is_published'])) ? $data['is_published'] : 0;
        $this->is_delete = (!empty($data['is_delete'])) ? $data['is_delete'] : 0;
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
                'name' => 'transportation_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'transportation_title',
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
                'name' => 'transportation_description',
                'required' => TRUE,
            ));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}