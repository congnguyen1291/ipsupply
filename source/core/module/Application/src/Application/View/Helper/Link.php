<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Link  extends App
{
    public function getCategoryUrl(){
        $url = FOLDERWEB . $this->getUrlPrefixLang().'/category';
        return $url;
    }
}
