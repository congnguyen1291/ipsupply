<?php
namespace Schema;
use Schema\SchPerformingGroup;

class SchMusicGroup extends SchPerformingGroup{
	protected $album	=	'MusicAlbum';
	protected $track	=	'MusicRecording';
	function __construct(){$this->namespace = "MusicGroup";}
}