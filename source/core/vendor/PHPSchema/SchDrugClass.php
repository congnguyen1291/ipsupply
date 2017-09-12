<?php
namespace Schema;
use Schema\SchMedicalTherapy;

class SchDrugClass extends SchMedicalTherapy{
	protected $drug	=	'Drug';
	function __construct(){$this->namespace = "DrugClass";}
}