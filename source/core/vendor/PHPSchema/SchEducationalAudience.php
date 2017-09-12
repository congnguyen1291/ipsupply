<?php
namespace Schema;
use Schema\SchAudience;

class SchEducationalAudience extends SchAudience{
	protected $educationalRole	=	'Text';
	function __construct(){$this->namespace = "EducationalAudience";}
}