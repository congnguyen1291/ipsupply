<?php
namespace Schema;
use Schema\SchEducationalOrganization;

class SchCollegeOrUniversity extends SchEducationalOrganization{
	function __construct(){$this->namespace = "CollegeOrUniversity";}
}