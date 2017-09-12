<?php
namespace Schema;
use Schema\SchVessel;

class SchArtery extends SchVessel{
	protected $arterialBranch	=	'AnatomicalStructure';
	protected $source	=	'AnatomicalStructure';
	protected $supplyTo	=	'AnatomicalStructure';
	function __construct(){$this->namespace = "Artery";}
}