<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/19/14
 * Time: 4:18 PM
 */
namespace Cms\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Setting implements InputFilterAwareInterface
{
    public $id;
    public $website_id;
    public $name;
    public $value;
    public $description;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : NULL;
        $this->website_id = (!empty($data['website_id'])) ? $data['website_id'] : $_SESSION['CMSMEMBER']['website_id'];
        $this->name = (!empty($data['name'])) ? $data['name'] : NULL;
        $this->value = (!empty($data['value'])) ? $data['value'] : NULL;
        $this->description = (!empty($data['description'])) ? $data['description'] : NULL;
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
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}