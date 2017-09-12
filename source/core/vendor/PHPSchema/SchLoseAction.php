<?php
namespace Schema;
use Schema\SchAchieveActions;

class SchLoseAction extends SchAchieveActions{
	protected $winner	=	'Person';
	function __construct(){$this->namespace = "LoseAction";}
}