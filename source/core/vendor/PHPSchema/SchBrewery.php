<?php
namespace Schema;
use Schema\SchFoodEstablishment;

class SchBrewery extends SchFoodEstablishment{
	function __construct(){$this->namespace = "Brewery";}
}