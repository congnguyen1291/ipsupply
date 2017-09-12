<?php
namespace Schema;
use Schema\SchBase;

class SchThing extends SchBase{
	protected $additionalType	=	'URL';
	protected $alternateName	=	'Text';
	protected $description	=	'Text';
	protected $image	=	'URL,ImageObject';
	protected $name	=	'Text';
	protected $potentialAction	=	'Action';
	protected $sameAs	=	'URL';
	protected $url	=	'Text';
	function __construct(){$this->namespace = "Thing";}
}