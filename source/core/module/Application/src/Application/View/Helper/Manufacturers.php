<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Manufacturers extends App
{
    public function getAllManufacturers($intPage, $intPageSize)
    {
        $manufacturers = $this->getModelTable('ManufacturersTable')->getAllManus($intPage, $intPageSize);
        return $manufacturers;
    }

    public function getManufacturers($list=array())
    {
        $manufacturers = $this->getModelTable('ManufacturersTable')->getRows($list);
        return $manufacturers;
    }


    public function getManuUrl($manu = array()){
        $url = FOLDERWEB . $this->getUrlPrefixLang().'/listing/'.$this->toAlias($manu['manufacturers_name']).'-'.$manu['manufacturers_id'];
        return $url;
    }

    public function getImage($manu)
    {
        if( !empty($manu) 
            && !empty($manu['thumb_image']) ){
            return $manu['thumb_image'];
        }
        return '';
    }

    public function getName($manu)
    {
        if( !empty($manu) 
            && !empty($manu['manufacturers_name']) ){
            return $manu['manufacturers_name'];
        }
        return '';
    }

    public function getId($manu)
    {
        if( !empty($manu) 
            && !empty($manu['manufacturers_id']) ){
            return $manu['manufacturers_id'];
        }
        return 0;
    }
	
}
