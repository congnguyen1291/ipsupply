<?php
namespace Schema;
use Schema\SchMedicalOrganization;

class SchVeterinaryCare extends SchMedicalOrganization{
	function __construct(){$this->namespace = "VeterinaryCare";}
}