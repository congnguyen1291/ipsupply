<?php
namespace Schema;
use Schema\SchMedicalEntity;

class SchMedicalCause extends SchMedicalEntity{
	protected $causeOf	=	'MedicalEntity';
	function __construct(){$this->namespace = "MedicalCause";}
}