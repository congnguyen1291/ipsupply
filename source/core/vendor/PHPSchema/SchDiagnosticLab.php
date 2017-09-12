<?php
namespace Schema;
use Schema\SchMedicalOrganization;

class SchDiagnosticLab extends SchMedicalOrganization{
	protected $availableTest	=	'MedicalTest';
	function __construct(){$this->namespace = "DiagnosticLab";}
}