<?php
namespace Schema;
use Schema\SchEvent;

class SchFoodEvent extends SchEvent{
	function __construct(){$this->namespace = "FoodEvent";}
}