<?php
namespace Schema;
use Schema\SchOrganization;

class SchCorporation extends SchOrganization{
	protected $tickerSymbol	=	'Text';
	function __construct(){$this->namespace = "Corporation";}
}