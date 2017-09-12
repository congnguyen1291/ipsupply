<?php
namespace Schema;
use Schema\SchOrganization;

class SchNGO extends SchOrganization{
	function __construct(){$this->namespace = "NGO";}
}