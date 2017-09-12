<?php
namespace Schema;
use Schema\SchCreativeWork;

class SchBlog extends SchCreativeWork{
	protected $blogPost	=	'BlogPosting';
	function __construct(){$this->namespace = "Blog";}
}