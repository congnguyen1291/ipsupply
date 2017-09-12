<?php
namespace Schema;
use Schema\SchIntangible;

class SchAudience extends SchIntangible{
	protected $audienceType	=	'Text';
	protected $geographicArea	=	'AdministrativeArea';
	function __construct(){$this->namespace = "Audience";}
}