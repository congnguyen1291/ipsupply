<?php
namespace Schema;
use Schema\SchAchieveAction;

class SchWinAction extends SchAchieveAction{
	protected $loser	=	'Person';
	function __construct(){$this->namespace = "WinAction";}
}