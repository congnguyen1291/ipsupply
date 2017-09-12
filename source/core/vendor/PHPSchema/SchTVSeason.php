<?php
namespace Schema;
use Schema\SchCreativeWork;

class SchTVSeason extends SchCreativeWork{
	protected $endDate	=	'Date';
	protected $episode	=	'Episode';
	protected $numberOfEpisodes	=	'Number';
	protected $partOfSeries	=	'Series';
	protected $seasonNumber	=	'Integer,Text';
	protected $startDate	=	'Date';
	protected $trailer	=	'VideoObject';
	function __construct(){$this->namespace = "TVSeason";}
}