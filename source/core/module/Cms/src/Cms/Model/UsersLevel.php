<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/10/14
 * Time: 11:25 AM
 */

namespace Cms\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class UsersLevel implements InputFilterAwareInterface
{
    public $users_level_id;
    public $website_id;
    public $users_level_name;
    public $users_level_icon;
    public $users_level_longdescription;
    public $users_level_decrease;
    public $min_buy;
    public $is_published;
    public $date_create;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->users_level_id     = (!empty($data['users_level_id'])) ? $data['users_level_id'] : null;
        $this->website_id     = (!empty($data['website_id'])) ? $data['website_id'] : $_SESSION['CMSMEMBER']['website_id'];
        $this->users_level_name     = (!empty($data['users_level_name'])) ? $data['users_level_name'] : null;
        $this->users_level_icon = (!empty($data['users_level_icon'])) ? $data['users_level_icon'] : null;
        $this->users_level_longdescription = (!empty($data['users_level_longdescription'])) ? $data['users_level_longdescription'] : null;
        $this->users_level_decrease = (!empty($data['users_level_decrease'])) ? $data['users_level_decrease'] : null;
        $this->min_buy = (!empty($data['min_buy'])) ? str_replace(',', '', $data['min_buy']) : null;
        $this->is_published = (!empty($data['is_published'])) ? str_replace(',', '', $data['is_published']) : 0;
        $this->date_create = (!empty($data['date_create'])) ? $data['date_create'] : date('Y-m-d H:i:s');
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
                'name'     => 'users_level_id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'users_level_decrease',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'min_buy',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'users_level_name',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}