<?php
namespace Schema;
use Schema\SchWebPage;

class SchMedicalWebPage extends SchWebPage{
	protected $aspect	=	'Text';
	function __construct(){$this->namespace = "MedicalWebPage";}
}