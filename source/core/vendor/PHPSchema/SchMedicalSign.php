<?php
namespace Schema;
use Schema\SchMedicalSignOrSymptom;

class SchMedicalSign extends SchMedicalSignOrSymptom{
	protected $identifyingExam	=	'PhysicalExam';
	protected $identifyingTest	=	'MedicalTest';
	function __construct(){$this->namespace = "MedicalSign";}
}