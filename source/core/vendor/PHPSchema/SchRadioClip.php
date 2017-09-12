<?php
namespace Schema;
use Schema\SchClip;

class SchRadioClip extends SchClip{
	protected $partOfSeason	=	'Season';
	protected $partOfSeries	=	'Series';
	function __construct(){$this->namespace = "RadioClip";}
}