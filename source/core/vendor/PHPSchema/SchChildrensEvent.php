<?php
namespace Schema;
use Schema\SchEvent;

class SchChildrensEvent extends SchEvent{
	function __construct(){$this->namespace = "ChildrensEvent";}
}