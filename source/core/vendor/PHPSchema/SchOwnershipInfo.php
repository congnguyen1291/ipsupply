<?php
namespace Schema;
use Schema\SchStructuredValue;

class SchOwnershipInfo extends SchStructuredValue{
	protected $acquiredFrom	=	'Person,Organization';
	protected $ownedFrom	=	'DateTime';
	protected $ownedThrough	=	'DateTime';
	protected $typeOfGood	=	'Product';
	function __construct(){$this->namespace = "OwnershipInfo";}
}