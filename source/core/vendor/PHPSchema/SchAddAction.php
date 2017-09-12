<?php
namespace Schema;
use Schema\SchUpdateAction;

class SchAddAction extends SchUpdateAction{
	function __construct(){$this->namespace = "AddAction";}
}