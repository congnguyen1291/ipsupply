<?php
namespace Schema;
use Schema\SchMedicalRiskEstimator;

class SchMedicalRiskScore extends SchMedicalRiskEstimator{
	protected $algorithm	=	'Text';
	function __construct(){$this->namespace = "MedicalRiskScore";}
}