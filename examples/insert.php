<?php

/* Require the Class */
require '../src/Mongo.php';

/* Get Class instance */
$mongo = MongoAPI::getInstance();

/* Uncomment these lines to reset collection */
// $mongo->collection( 'pirates' );
// $mongo->drop();

/* Select the 'pirates' collection */
$mongo->collection( 'pirates' );

/* Set up data in an array */
$data = array( 'name' => 'Black Beard', 'age' => 45, 'bounty' => '$4,000,000' );

/* Insert the data into the DB */
$mongo->insert( $data );

/* Get the items back */
/* Since the collection resets after every query, we need to define it again */
$pirates = $mongo->collection( 'pirates' )->get();

/* Print it to page */
echo '<pre>'; 
print_r( $pirates );
echo '</pre>';