<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Districts extends App
{
	public function loadDistrictByCities($cities_id)
    {
        $districts = $this->getModelTable('UserTable')->loadDistrict($cities_id);
        return $districts;
    }

    public function getDistrict($districts_id)
    {
        $districts = $this->getModelTable('DistrictsTable')->getRow($districts_id);
        return $districts;
    }
}
