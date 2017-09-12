<?php
include_once("cms/lib/Private/Tts/lib.php");
class SearchController extends Zend_Controller_Action 
{
	function init() 
    { 
             $this->initView(); 
             $this->view->baseUrl = $this->_request->getBaseUrl(); 
			 //Zend_Loader::loadClass('Indexs');
    } 
	public function indexAction()
	{
	
	}
	public function importdataAction()
	{
	  require_once($_SERVER['DOCUMENT_ROOT'].'/solrphp/SolrPHPClient/Apache/Solr/Service.php');
	  $solr = new Apache_Solr_Service(hostsearch, portsearch, foldersearch, core);
	  if ( ! $solr->ping() ) {
		echo 'Solr service not responding.';
		exit;
	  }
	  $docs1=array();
	  $db = Globals::getDB();
	  $db->query("SET NAMES 'utf8'");
	  $rowfetch=$db->fetchAll("select id, title, location_5_title, location_4_title, keywords, keywords1, level, status from dendau_domain1.Listing_Summary ");
	  if(count($rowfetch)>0){
		  foreach($rowfetch as $rowlisting){
			$docs1[]= array('id' => $rowlisting["id"],'keywords' => $rowlisting["title"], 'keywords1' => $rowlisting["keywords1"], 'location_5_title' => $rowlisting["location_5_title"], 'location_4_title' => $rowlisting["location_4_title"], 'level'=>$rowlisting["level"], 'status'=>$rowlisting["status"]);
		  }
	  }
	  $docs=$docs1;
	  //// ad document
	   $documents = array();
		  foreach ( $docs as $item => $fields ) {
			$part = new Apache_Solr_Document();
			foreach ($fields as $key => $value ) {
			  if ( is_array( $value ) ) {
				foreach ( $value as $data ) {
				  $part->setMultiValue( $key, $data );
				}
			  }else {
				$part->$key = $value;
			  }
			}
			$documents[] = $part;
		  }
		  //
		  //
		  // Load the documents into the index
		  // 
		  try {
			$solr->addDocuments( $documents );
			$solr->commit();
			$solr->optimize();
		  }catch ( Exception $e ) {
			echo $e->getMessage();
		  }
	  
	}
	public function searchAction()
	{
	  require_once($_SERVER['DOCUMENT_ROOT'].'/solrphp/SolrPHPClient/Apache/Solr/Service.php');
	  $solr = new Apache_Solr_Service(hostsearch, portsearch, foldersearch, core);
	  if ( ! $solr->ping() ) {
		echo 'Solr service not responding.';
		exit;
	  }
	   $db = Globals::getDB();
	  $db->query("SET NAMES 'utf8'");
	  $offset = 0;
	  $limit = 100;
	  if(isset($_GET["page"]) && $_GET["page"]!="" && $_GET["page"]!=0){
		$intPage= $_GET["page"];
	  }else{
		$intPage= 0;	
	  }
	  $where="";
	  $intPageSize = 12;
	  $nhahang=$_GET["nhahang"];
	  $khuvuc=$_GET["khuvuc"];
	  $tp=$GLOBALS['tp'];
	  $queries = array('keywords: "'.$nhahang.'"', 'keywords1: "'.$nhahang.'"', 'location_4_title: "'.$tp.'"', 'location_5_title: "'.$khuvuc.'"');
	  $listlisting="";
	  foreach ($queries as $query ){
		$response = $solr->search($query, $intPage, $intPageSize );
		if($response->getHttpStatus() == 200){ 
		  if ( $response->response->numFound > 0){
			foreach($response->response->docs as $doc){
			  $this->view->datalisting=Listing::DetaiListing($db," and Listing.id='".$doc->id."'");
			  $listlisting.=$this->view->render("search/searchlist.phtml");
			}
		  }
		}else {
			echo $response->getHttpStatusMessage();
		}
	  }
	  $this->view->searchlist=$listlisting;
	}
}
?>