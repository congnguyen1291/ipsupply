<?php
namespace Schema;
use Schema\SchMedicalOrganization;

class SchDentist extends SchMedicalOrganization{
	function __construct(){$this->namespace = "Dentist";}
}