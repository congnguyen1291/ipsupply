<?php
namespace Schema;
use Schema\SchAssessAction;

class SchReviewAction extends SchAssessAction{
	protected $resultReview	=	'Review';
	function __construct(){$this->namespace = "ReviewAction";}
}