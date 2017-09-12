<?php
namespace Schema;
use Schema\SchFoodEstablishment;

class SchCafeOrCoffeeShop extends SchFoodEstablishment{
	function __construct(){$this->namespace = "CafeOrCoffeeShop";}
}