<?php
namespace Schema;
use Schema\SchRating;

class SchAggregateRating extends SchRating{
	protected $itemReviewed	=	'Thing';
	protected $ratingCount	=	'Number';
	protected $reviewCount	=	'Number';
	function __construct(){$this->namespace = "AggregateRating";}
}