<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/3/14
 * Time: 10:49 AM
 */

namespace Cms\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Picture implements InputFilterAwareInterface
{
    public $picture_id;
    public $website_id;
    public $users_id;
    public $id_album;
    public $full_name;
    public $name;
    public $string_data;
    public $folder;
    public $caption;
    public $type;
    public $order;
    public $detector;
    public $number_comment;
    public $creation_date;
    public $last_edit_date;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->picture_id   = (!empty($data['picture_id'])) ? $data['picture_id'] : NULL;
        $this->website_id   = (!empty($data['website_id'])) ? $data['website_id'] : NULL;
        $this->users_id = (!empty($data['users_id'])) ? $data['users_id'] : NULL;
        $this->id_album   = (!empty($data['id_album'])) ? $data['id_album'] : 0;
        $this->full_name   = (!empty($data['full_name'])) ? $data['full_name'] : '';
        $this->name   = (!empty($data['name'])) ? $data['name'] : '';
        $this->string_data   = (!empty($data['string_data'])) ? $data['string_data'] : '';
        $this->folder   = (!empty($data['folder'])) ? $data['folder'] : '';
        $this->caption   = (!empty($data['caption'])) ? $data['caption'] : '';
        $this->type   = (!empty($data['type'])) ? $data['type'] : 'png';
        $this->order   = (!empty($data['order'])) ? $data['order'] : 0;
        $this->detector   = (!empty($data['detector'])) ? $data['detector'] : 0;
        $this->number_comment   = (!empty($data['number_comment'])) ? $data['number_comment'] : 0;
        $this->creation_date   = (!empty($data['creation_date'])) ? $data['creation_date'] : date('d-m-Y');
        $this->last_edit_date   = (!empty($data['last_edit_date'])) ? $data['last_edit_date'] : date('d-m-Y');
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
                'name' => 'api_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'website_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'users_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'full_name',
                'required' => TRUE
            ));
            $inputFilter->add(array(
                'name' => 'name',
                'required' => TRUE
            ));
            $inputFilter->add(array(
                'name' => 'folder',
                'required' => TRUE
            ));
            $inputFilter->add(array(
                'name' => 'type',
                'required' => TRUE
            ));
            
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}