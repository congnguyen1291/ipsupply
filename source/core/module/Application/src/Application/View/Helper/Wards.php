<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Wards extends App
{
    public function loadWardByDistrict($districts_id)
    {
        $ward = $this->getModelTable('UserTable')->loadWard($districts_id);
        return $ward;
    }

    public function getWard($wards_id)
    {
        $ward = $this->getModelTable('WardsTable')->getRow($wards_id);
        return $ward;
    }

}
