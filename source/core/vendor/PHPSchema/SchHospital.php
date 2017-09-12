<?php
namespace Schema;
use Schema\SchEmergencyService;

class SchHospital extends SchEmergencyService{
	protected $availableService	=	'MedicalTherapy,MedicalProcedure,MedicalTest';
	protected $medicalSpecialty	=	'MedicalSpecialty';
	function __construct(){$this->namespace = "Hospital";}
}