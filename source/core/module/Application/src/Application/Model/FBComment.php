<?php

namespace Application\Model;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class FBComment{

    public $id;
    public $website_id;
    public $id_parent;
    public $user_id;
    public $message;
    public $is_important;
    public $is_readed;
    public $date;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id'])) ? $data['id'] : null;
        $this->website_id = (isset($data['website_id'])) ? $data['website_id'] : null;
        $this->id_parent = (isset($data['id_parent'])) ? $data['id_parent'] : 0;
        $this->user_id = (isset($data['user_id'])) ? $data['user_id'] : 0;
        $this->message = (isset($data['message'])) ? $data['message'] : '';
        $this->is_important = (isset($data['is_important'])) ? $data['is_important'] : 0;
        $this->is_readed = (isset($data['is_readed'])) ? $data['is_readed'] : 0;
        $this->date = (isset($data['date'])) ? $data['date'] : Date('Y-m-d H:m:i');
    }
    
}
