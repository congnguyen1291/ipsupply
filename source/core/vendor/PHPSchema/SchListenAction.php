<?php
namespace Schema;
use Schema\SchConsumeAction;

class SchListenAction extends SchConsumeAction
{
    function __construct()
    {
        $this->namespace = "ListenAction";
    }
}