<?php
namespace Schema;
use Schema\SchCreativeWork;

class SchPeriodical extends SchCreativeWork{
	protected $issn	=	'Text';
	function __construct(){$this->namespace = "Periodical";}
}