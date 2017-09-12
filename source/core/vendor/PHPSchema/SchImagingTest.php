<?php
namespace Schema;
use Schema\MedicalImagingTechnique;

class SchImagingTest extends SchMedicalTest{
	protected $imagingTechnique	=	'MedicalImagingTechnique';
	function __construct(){$this->namespace = "ImagingTest";}
}