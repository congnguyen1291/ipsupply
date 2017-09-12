<?php
namespace Schema;
use Schema\SchMedicalEnumeration;

class SchDrugPrescriptionStatus extends SchMedicalEnumeration{
	function __construct(){$this->namespace = "DrugPrescriptionStatus";}
}