<?php
namespace Schema;
use Schema\SchStore;

class SchBookStore extends SchStore{
	function __construct(){$this->namespace = "BookStore";}
}