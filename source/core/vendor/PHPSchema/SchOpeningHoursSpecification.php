<?php
namespace Schema;
use Schema\SchStructuredValue;

class SchOpeningHoursSpecification extends SchStructuredValue{
	protected $closes	=	'Time';
	protected $dayOfWeek	=	'DayOfWeek';
	protected $opens	=	'Time';
	protected $validFrom	=	'DateTime';
	protected $validThrough	=	'DateTime';
	function __construct(){$this->namespace = "OpeningHoursSpecification";}
}