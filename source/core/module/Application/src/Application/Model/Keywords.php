<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/6/14
 * Time: 9:22 AM
 */

namespace Application\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Keywords {
    public $banks_id;
    public $banks_title;
    public $banks_description;
    public $is_published;
    public $is_delete;
    public $date_create;
    public $thumb_image;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->keyword_id            = (!empty($data['keyword_id'])) ? $data['keyword_id'] : NULL;
        $this->url_friendly          = (!empty($data['url_friendly'])) ? $data['url_friendly'] : NULL;
        $this->keyword         = (!empty($data['keyword'])) ? $data['keyword'] : NULL;
        $this->number_search         = (!empty($data['number_search'])) ? $data['number_search'] : 0;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

} 