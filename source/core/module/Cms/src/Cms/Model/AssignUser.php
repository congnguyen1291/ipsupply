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

class AssignUser implements InputFilterAwareInterface
{
    public $assign_user_id;
    public $assign_id;
    public $users_id;
    public $assign_user_status;
    public $assign_user_date_send;
    public $assign_user_date_relay;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->assign_user_id   = (!empty($data['assign_user_id'])) ? $data['assign_user_id'] : NULL;
        $this->assign_id   = (!empty($data['assign_id'])) ? $data['assign_id'] : NULL;
        $this->users_id = (!empty($data['users_id'])) ? $data['users_id'] : NULL;
        $this->assign_user_status   = (!empty($data['assign_user_status'])) ? $data['assign_user_status'] : 'pending';
        $this->assign_user_date_send   = (!empty($data['assign_user_date_send'])) ? $data['assign_user_date_send'] : '';
        $this->assign_user_date_relay   = (!empty($data['assign_user_date_relay'])) ? $data['assign_user_date_relay'] : '';
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
                'name' => 'assign_user_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}