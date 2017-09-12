<?php
namespace Schema;
use Schema\SchOrganization;

class SchEducationalOrganization extends SchOrganization{
	protected $alumni	=	'Person';
	function __construct(){$this->namespace = "EducationalOrganization";}
}