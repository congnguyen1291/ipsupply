<?php
namespace Schema;
use Schema\SchTransferAction;

class SchDownloadAction extends SchTransferAction{
	function __construct(){$this->namespace = "DownloadAction";}
}