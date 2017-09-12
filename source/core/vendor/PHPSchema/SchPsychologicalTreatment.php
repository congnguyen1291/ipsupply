<?php
namespace Schema;
use Schema\SchMedicalTherapy;

class SchPsychologicalTreatment extends SchMedicalTherapy{
	function __construct(){$this->namespace = "PsychologicalTreatment";}
}