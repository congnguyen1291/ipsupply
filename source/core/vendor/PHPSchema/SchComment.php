<?php
namespace Schema;
use Schema\SchCreativeWork;

class SchComment extends SchCreativeWork{
	protected $downvoteCount	=	'Integer';
	protected $parentItem	=	'Question';
	protected $upvoteCount	=	'Integer';
	function __construct(){$this->namespace = "Comment";}
}