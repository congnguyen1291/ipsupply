<?php
  require_once( 'SolrPHPClient/Apache/Solr/Service.php' );
  // 
  // 
  // Try to connect to the named server, port, and url
  // 
  $options = array
	(
		'hostname' => '42.117.1.99',
		'login'    => "admin",
		'password' => "",
		'port'     => 8983,
	);
  $solr = new Apache_Solr_Service( '42.117.1.99', '8983', '/solr', 'core1');
  if ( ! $solr->ping() ) {
    echo 'Solr service not responding.';
    exit;
  }
  //
  //
  // Create two documents
  //
  $docs1=array();
  $docs1[]= array('id' => 17,'keywords' => 'congnguyen fdsafdasfds ','keywords1' => 'Franz jagt im komplett verwahrlosten Taxi quer durch Bayern','category' => array( 'Orange', 'Birne'));
  $docs1[]= array('id' => 18, 'keywords' => 'congnguyen 1fdfdsa fdsa fdas ', 'keywords1' => 'Pfdas fdasfdsa fdsa', 'category' => array( 'Apfel', 'Birne'));
  $docs1[] = array('id' => 19,'keywords' => 'congnguyen 1 3213213 312312','keywords1' => 'f dadas fsdf32e4324 2432432Polyfon ','category' => array( 'Apfel', 'Birne'));
  $docs=$docs1;
  var_dump($docs);
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
  //
  // 
  // Run some queries. 
  //
  $offset = 0;
  $limit = 100;
  $queries=$_GET["q"];
  //foreach ( $queries as $query ) {
    $response = $solr->search( $queries, $offset, $limit );
    if($response->getHttpStatus() == 200 ) { 
		
       //print_r( $response->getRawResponse() );
      if ( $response->response->numFound > 0){
		 
        foreach($response->response->docs as $doc){ 
          echo $doc->id;
		  echo $doc->keywords;
		  echo $doc->keywords1.'<br />';
        }
        echo '<br />';
      }
    }else {
		echo $response->getHttpStatusMessage();
    }
// }
?>