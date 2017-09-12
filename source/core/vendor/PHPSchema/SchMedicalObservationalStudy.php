<?php
namespace Schema;
use Schema\SchMedicalStudy;

class SchMedicalObservationalStudy extends SchMedicalStudy{
	protected $studyDesign	=	'MedicalObservationalStudyDesign';
	function __construct(){$this->namespace = "MedicalObservationalStudy";}
}