<?php
namespace Schema;
use Schema\SchAdministrativeArea;

class SchCity extends SchAdministrativeArea{
	function __construct(){$this->namespace = "City";}
}