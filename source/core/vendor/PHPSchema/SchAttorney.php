<?php
namespace Schema;
use Schema\SchProfessionalService;

class SchAttorney extends SchProfessionalService{
	function __construct(){$this->namespace = "Attorney";}
}