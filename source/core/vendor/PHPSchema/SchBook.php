<?php
namespace Schema;
use Schema\SchCreativeWork;

class SchBook extends SchCreativeWork{
	protected $bookEdition	=	'Text';
	protected $bookFormat	=	'BookFormatType';
	protected $illustrator	=	'Person';
	protected $isbn	=	'Text';
	protected $numberOfPages	=	'Integer';
	function __construct(){$this->namespace = "Book";}
}