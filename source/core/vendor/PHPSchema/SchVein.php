<?php
namespace Schema;
use Schema\SchVessel;

class SchVein extends SchVessel{
	protected $drainsTo	=	'Vessel';
	protected $regionDrained	=	'AnatomicalSystem,AnatomicalStructure';
	protected $tributary	=	'AnatomicalStructure';
	function __construct(){$this->namespace = "Vein";}
}