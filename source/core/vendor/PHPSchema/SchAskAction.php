<?php
namespace Schema;
use Schema\SchCommunicateAction;

class SchAskAction extends SchCommunicateAction{
	protected $question	=	'Text';
	function __construct(){$this->namespace = "AskAction";}
}