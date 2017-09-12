<?php
namespace Schema;
use Schema\SchScholarlyArticle;

class SchMedicalScholarlyArticle extends SchScholarlyArticle{
	protected $publicationType	=	'Text';
	function __construct(){$this->namespace = "MedicalScholarlyArticle";}
}