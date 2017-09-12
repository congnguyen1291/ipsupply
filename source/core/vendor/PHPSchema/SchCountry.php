<?php
namespace Schema;
use Schema\SchAdministrativeArea;

class SchCountry extends SchAdministrativeArea{
	function __construct(){$this->namespace = "Country";}
}