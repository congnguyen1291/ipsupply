<?php
namespace Schema;
use Schema\SchCreativeWork;

class SchMusicRecording extends SchCreativeWork{
	protected $byArtist	=	'MusicGroup';
	protected $duration	=	'Duration';
	protected $inAlbum	=	'MusicAlbum';
	protected $inPlaylist	=	'MusicPlaylist';
	function __construct(){$this->namespace = "MusicRecording";}
}