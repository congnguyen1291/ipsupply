<?php
namespace Schema;
use Schema\SchMedicalTest;

class SchPathologyTest extends SchMedicalTest{
	protected $tissueSample	=	'Text';
	function __construct(){$this->namespace = "PathologyTest";}
}