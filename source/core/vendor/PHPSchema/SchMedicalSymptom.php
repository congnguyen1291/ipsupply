<?php
namespace Schema;
use Schema\SchMedicalSignOrSymptom;

class SchMedicalSymptom extends SchMedicalSignOrSymptom{
	function __construct(){$this->namespace = "MedicalSymptom";}
}