<?php
namespace Schema;
use Schema\SchMedicalEntity;

class SchMedicalTest extends SchMedicalEntity{
	protected $affectedBy	=	'Drug';
	protected $normalRange	=	'Text';
	protected $signDetected	=	'MedicalSign';
	protected $usedToDiagnose	=	'MedicalCondition';
	protected $usesDevice	=	'MedicalDevice';
	function __construct(){$this->namespace = "MedicalTest";}
}