<?php
namespace Schema;
use Schema\SchInteractAction;

class SchFollowAction extends SchInteractAction{
	protected $followee	=	'Organization,Person';
	function __construct(){$this->namespace = "FollowAction";}
}