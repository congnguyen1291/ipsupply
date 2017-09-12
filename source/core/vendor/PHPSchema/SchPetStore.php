<?php
namespace Schema;
use Schema\SchStore;

class SchPetStore extends SchStore{
	function __construct(){$this->namespace = "PetStore";}
}