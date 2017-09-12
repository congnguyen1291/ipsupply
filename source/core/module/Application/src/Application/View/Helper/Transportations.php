<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Transportations extends App
{
    public function getTransportationsById($transportation_id)
    {
        $transportations = $this->getModelTable('UserTable')->getTransportationsById($transportation_id);
        return $transportations;
    }

}
