<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Cities extends App
{
    public function getCitiesFromStringId($str)
    {
        $cities = $this->getModelTable('CitiesTable')->getCitiesFromStringId($str);
        return $cities;
    }

    public function getRow($cities_id)
    {
        $cities = $this->getModelTable('CitiesTable')->getRow($cities_id);
        return $cities;
    }

    public function getCity($cities_id)
    {
        $cities = $this->getModelTable('CitiesTable')->getRow($cities_id);
        return $cities;
    }

    public function loadCitiesByCountry($country_id)
    {
        $cities = $this->getModelTable('UserTable')->loadCities($country_id);
        return $cities;
    }

}
