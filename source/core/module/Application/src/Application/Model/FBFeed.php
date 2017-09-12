<?php

namespace Application\Model;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class FBFeed{

    public $id;
    public $website_id;
    public $page_id;
    public $user_id;
    public $link;
    public $message;
    public $caption;
    public $description;
    public $icon;
    public $name;
    public $picture;
    public $status_type;
    public $type;
    public $can_comment;
    public $can_like;
    public $date;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id'])) ? $data['id'] : null;
        $this->website_id = (isset($data['website_id'])) ? $data['website_id'] : null;
        $this->page_id = (isset($data['page_id'])) ? $data['page_id'] : 0;
        $this->user_id = (isset($data['user_id'])) ? $data['user_id'] : 0;
        $this->link = (isset($data['link'])) ? $data['link'] : '';
        $this->message = (isset($data['message'])) ? $data['message'] : '';
        $this->caption = (isset($data['caption'])) ? $data['caption'] : '';
        $this->description = (isset($data['description'])) ? $data['description'] : '';
        $this->icon = (isset($data['icon'])) ? $data['icon'] : '';
        $this->name = (isset($data['name'])) ? $data['name'] : '';
        $this->picture = (isset($data['picture'])) ? $data['picture'] : '';
        $this->status_type = (isset($data['status_type'])) ? $data['status_type'] : '';
        $this->type = (isset($data['type'])) ? $data['type'] : '';
        $this->can_comment = (isset($data['can_comment'])) ? $data['can_comment'] : '';
        $this->can_like = (isset($data['can_like'])) ? $data['can_like'] : '';
        $this->date = (isset($data['date'])) ? $data['date'] : Date('Y-m-d H:m:i');
    }
    
}
