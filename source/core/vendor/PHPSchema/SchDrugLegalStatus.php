<?php
namespace Schema;
use Schema\SchMedicalIntangible;

class SchDrugLegalStatus extends SchMedicalIntangible{
	protected $applicableLocation	=	'AdministrativeArea';
	function __construct(){$this->namespace = "DrugLegalStatus";}
}