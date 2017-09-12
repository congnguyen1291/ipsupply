<?php

namespace Application\Model;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class FBUser{

    public $id;
    public $website_id;
    public $name;
    public $link;
    public $picture;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id'])) ? $data['id'] : null;
        $this->website_id = (isset($data['website_id'])) ? $data['website_id'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : '';
        $this->link = (isset($data['link'])) ? $data['link'] : '';
        $this->picture = (isset($data['picture'])) ? $data['picture'] : '';
    }
    
}
