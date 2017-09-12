<?php
namespace Schema;
use Schema\SchConsumeAction;

class SchViewAction extends SchConsumeAction{
	function __construct(){$this->namespace = "ViewAction";}
}