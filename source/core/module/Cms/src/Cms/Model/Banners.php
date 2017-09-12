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

class Banners implements InputFilterAwareInterface{
    public $banners_id;
    public $website_id;
    public $banners_title;
    public $banners_description;
    public $position_id;
    public $type_banners;
    public $size_id;
    public $code;
    public $file;
    public $status;
    public $date_show;
    public $date_hide;
    public $link;
    public $background;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->banners_id            = (!empty($data['banners_id'])) ? $data['banners_id'] : NULL;
        $this->website_id            = (!empty($data['website_id'])) ? $data['website_id'] : NULL;
        $this->banners_title          = (!empty($data['banners_title'])) ? $data['banners_title'] : NULL;
        $this->banners_description    = (!empty($data['banners_description'])) ? $data['banners_description'] : NULL;
        $this->position_id         = (!empty($data['position_id'])) ? $data['position_id'] : NULL;
        $this->type_banners         = (!empty($data['type_banners'])) ? $data['type_banners'] : NULL;
        $this->size_id         = (!empty($data['size_id'])) ? $data['size_id'] : NULL;
        $this->code         = (!empty($data['code'])) ? $data['code'] : NULL;
        $this->file   = (!empty($data['file'])) ? $data['file'] : NULL;
        $this->status              = (!empty($data['status']))  ? $data['status']  : 0;
        $this->date_show  = (!empty($data['date_show']))  ? $data['date_show'].':00'  : date('dd-mm-yy');
        $this->date_hide     = (!empty($data['date_hide']))  ? $data['date_hide'].':00'  : date('dd-mm-yy');
        $this->link             = (!empty($data['link'])) ? $data['link'] : '';
        $this->is_published             = (!empty($data['is_published'])) ? $data['is_published'] : 0;
        $this->background             = (!empty($data['background'])) ? $data['background'] : '#FFF';
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
                'name' => 'banners_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'position_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'banners_title',
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
                            'max' => 250,
                        ),
                    ),
                ),
            ));
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
} 