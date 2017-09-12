<?php
namespace Schema;
use Schema\SchConsumeAction;

class SchReadAction extends SchConsumeAction{
	function __construct(){$this->namespace = "ReadAction";}
}