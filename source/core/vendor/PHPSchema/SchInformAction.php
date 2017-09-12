<?php
namespace Schema;
use Schema\SchCommunicateAction;

class SchInformAction extends SchCommunicateAction{
	protected $event	=	'Event';
	function __construct(){$this->namespace = "InformAction";}
}