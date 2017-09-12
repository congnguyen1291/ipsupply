<?php
namespace Schema;
use Schema\SchMoveAction;

class SchTravelAction extends SchMoveAction{
	protected $distance	=	'Distance';
	function __construct(){$this->namespace = "TravelAction";}
}