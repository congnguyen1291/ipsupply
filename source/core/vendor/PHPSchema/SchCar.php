<?php
namespace Schema;
use Schema\SchVehicle;

class SchCar extends SchVehicle{
	function __construct(){$this->namespace = "Car";}
}