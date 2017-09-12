<?php
namespace Application\View\Helper;
use Application\View\Helper\App;
use Schema\Schema;

class SEO extends App
{
    public function getSchema()
    {
        $chema = new Schema();
        return $chema;
    }

}
