<?php

namespace Cms\Model;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Templates implements InputFilterAwareInterface{

    public $template_id;
    public $categories_template_id;
    public $template_name;
    public $template_alias;
    public $template_description;
    public $template_thumb;
    public $template_thumb_mobile;
    public $template_dir;
    public $template_folder;
    public $files_css;
    public $files_js;
    public $templete_price;
    public $template_status;
    public $source;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->template_id     = (isset($data['template_id'])) ? $data['template_id'] : null;
        $this->categories_template_id = (isset($data['categories_template_id'])) ? $data['categories_template_id'] : null;
        $this->template_name  = (isset($data['template_name'])) ? $data['template_name'] : null;
        $this->template_alias  = (isset($data['template_alias'])) ? $data['template_alias'] : null;
        $this->template_description  = (isset($data['template_description'])) ? $data['template_description'] : null;
        $this->template_thumb  = (isset($data['template_thumb'])) ? $data['template_thumb'] : null;
        $this->template_thumb_mobile  = (isset($data['template_thumb_mobile'])) ? $data['template_thumb_mobile'] : null;
        $this->template_dir  = (isset($data['template_dir'])) ? $data['template_dir'] : '';
        $this->template_folder  = (isset($data['template_folder'])) ? $data['template_folder'] : '';
        $this->files_css  = (isset($data['files_css'])) ? $data['files_css'] : '';
        $this->files_js  = (isset($data['files_js'])) ? $data['files_js'] : '';
        $this->templete_price  = (isset($data['templete_price'])) ? $data['templete_price'] : '';
        $this->template_status  = (isset($data['template_status'])) ? $data['template_status'] : 0;
        $this->source          = (!empty($data['source']))  ? $data['source']  : NULL;
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
                'name' => 'template_name',
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
