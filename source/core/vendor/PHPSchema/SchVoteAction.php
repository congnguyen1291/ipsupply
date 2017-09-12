<?php
namespace Schema;
use Schema\SchChooseAction;

class SchVoteAction extends SchChooseAction{
	protected $candidate	=	'Person';
	function __construct(){$this->namespace = "VoteAction";}
}