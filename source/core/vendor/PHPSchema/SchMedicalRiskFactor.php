<?php
namespace Schema;
use Schema\SchMedicalEntity;

class SchMedicalRiskFactor extends SchMedicalEntity{
	protected $increasesRiskOf	=	'MedicalEntity';
	function __construct(){$this->namespace = "MedicalRiskFactor";}
}