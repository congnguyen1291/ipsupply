<?php
namespace Schema;
use Schema\SchFoodEstablishment;

class SchRestaurant extends SchFoodEstablishment{
	function __construct(){$this->namespace = "Restaurant";}
}