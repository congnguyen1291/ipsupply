<?php
	require_once( 'Apache/Solr/Service.php' );
	
	// 
	// 
	// Try to connect to the named server, port, and url
	// 
	$solr = new Apache_Solr_Service( '42.117.1.99', '8983', '/solr' );
	if ( ! $solr->ping() ) {
		echo 'Solr service not responding.';
		exit;
	}
	//
	//
	// Create two documents to represent two auto parts.
	// In practive, documents would likely be assembled from a 
	//   database query. 
	//
	$parts = array(
		'spark_plug' => array(
			'partno' => 1,
			'name' => 'Spark plug',
			'model' => array( 'Boxster', '924' ),
			'year' => array( 1999, 2000 ),
			'price' => 25.00,
			'inStock' => true,
		),
		'windshield' => array(
			'partno' => 2,
			'name' => 'Windshield',
			'model' => '911',
			'year' => array( 1999, 2000 ),
			'price' => 15.00,
			'inStock' => false,
		)
	);
		
	$documents = array();
	foreach ( $parts as $item => $fields ) {
		$part = new Apache_Solr_Document();
		
		foreach ( $fields as $key => $value ) {
			if ( is_array( $value ) ) {
				foreach ( $value as $datum ) {
					$part->setMultiValue( $key, $datum );
				}
			}
			else {
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
	}
	catch ( Exception $e ) {
		echo $e->getMessage();
	}
	
	//
	// 
	// Run some queries. Provide the raw path, a starting offset
	//   for result documents, and the maximum number of result
	//   documents to return. You can also use a fourth parameter
	//   to control how results are sorted, among other options.
	//
	$offset = 0;
	$limit = 10;
	$queries = array(
		'partno: 1 OR partno: 2',
		'model: Boxster',
		'name: plug'
	);
	foreach ( $queries as $query ) {
		$response = $solr->search( $query, $offset, $limit );
		
		if ( $response->getHttpStatus() == 200 ) {	
			print_r( $response->getRawResponse() );
			if ( $response->response->numFound > 0 ) {
				echo "$query <br />";

				foreach ( $response->response->docs as $doc ) { 
					echo "$doc->partno $doc->name <br />";
				}
				
				echo '<br />';
			}
		}
		else {
			echo $response->getHttpStatusMessage();
		}
	}
?>