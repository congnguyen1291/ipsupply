<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Contries extends App
{
	public function getContries()
    {
        $contries = $this->getModelTable('CountryTable')->getContries();
        return $contries;
    }

    public function getContry( $country_id )
    {
        $country = $this->getModelTable('CountryTable')->getOne($country_id);
        return $country;
    }

    public function getContryForWebsite()
    {
        $website = $this->view->Websites()->getWebsite();
        $country = array();
        if( empty($website['is_local']) || empty($website['website_contries']) ){
            $country = $this->getModelTable('CountryTable')->getContries();
        }else{
            $ids = explode(',', $website['website_contries']);
            $country = $this->getModelTable('CountryTable')->getContriesLimit( array('id'=> $ids) );
        }
        return $country;
    }

    public function getContryBuyerForWebsite()
    {
        $website = $this->view->Websites()->getWebsite();
        $country = array();
        if( empty($website['is_local']) || empty($website['website_contries']) ){
            $country = $this->getModelTable('CountryTable')->getContries();
        }else{
            $ids = explode(',', $website['website_contries']);
            $country = $this->getModelTable('CountryTable')->getContriesLimit( array('id'=> $ids) );
        }
        return $country;
    }

    public function getClassByCode( $code )
    {
        $flag = '';
        if( !empty($code) ){
            $flag = 'flag-'.(strtolower($code));
        }
        return $flag;
    }
}
