<?php
namespace Schema;
use Schema\SchMediaObject;

class SchAudioObject extends SchMediaObject{
	protected $transcript	=	'Text';
	function __construct(){$this->namespace = "AudioObject";}
}