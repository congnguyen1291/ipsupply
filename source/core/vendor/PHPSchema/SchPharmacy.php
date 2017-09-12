<?php
namespace Schema;
use Schema\SchMedicalOrganization;

class SchPharmacy extends SchMedicalOrganization{
	function __construct(){$this->namespace = "Pharmacy";}
}