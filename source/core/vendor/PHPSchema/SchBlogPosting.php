<?php
namespace Schema;
use Schema\SchArticle;

class SchBlogPosting extends SchArticle{
	protected $blogPost	=	'BlogPost';
	protected $liveBlogUpdate	=	'LiveBlogUpdate';
	function __construct(){$this->namespace = "BlogPosting";}
}