<?php
namespace Schema;
use Schema\SchOrganizeAction;

class SchPlanAction extends SchOrganizeAction{
	protected $scheduledTime	=	'DateTime';
	function __construct(){$this->namespace = "PlanAction";}
}