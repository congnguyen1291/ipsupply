<?php
namespace Schema;
use Schema\SchPeopleAudience;

class SchParentAudience extends SchPeopleAudience{
	protected $childMaxAge	=	'Number';
	protected $childMinAge	=	'Number';
	function __construct(){$this->namespace = "ParentAudience";}
}