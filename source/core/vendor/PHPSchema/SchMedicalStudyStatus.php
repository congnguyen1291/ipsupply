<?php
namespace Schema;
use Schema\SchMedicalEnumeration;

class SchMedicalStudyStatus extends SchMedicalEnumeration{
	function __construct(){$this->namespace = "MedicalStudyStatus";}
}