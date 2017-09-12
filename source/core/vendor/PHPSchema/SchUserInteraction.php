<?php
namespace Schema;
use Schema\SchEvent;

class SchUserInteraction extends SchEvent{
	function __construct(){$this->namespace = "UserInteraction";}
}