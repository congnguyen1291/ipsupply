<?php
namespace Schema;
use Schema\SchAudience;

class SchBusinessAudience extends SchAudience{
	protected $numberofEmployees	=	'QuantitativeValue';
	protected $yearlyRevenue	=	'QuantitativeValue';
	protected $yearsInOperation	=	'QuantitativeValue';
	function __construct(){$this->namespace = "BusinessAudience";}
}