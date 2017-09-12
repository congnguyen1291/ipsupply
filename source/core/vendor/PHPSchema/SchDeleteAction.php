<?php
namespace Schema;
use Schema\SchUpdateAction;

class SchDeleteAction extends SchUpdateAction{
	function __construct(){$this->namespace = "DeleteAction";}
}