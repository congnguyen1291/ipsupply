<?php
namespace Schema;
use Schema\SchPlanAction;

class SchReserveAction extends SchPlanAction{
	protected $scheduledTime	=	'DateTime';
	function __construct(){$this->namespace = "ReserveAction";}
}