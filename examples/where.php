<?php

/* Require the Class */
require '../src/Mongo.php';

/* Get Class instance */
$mongo = MongoAPI::getInstance();

/* Reset collection */
$mongo->collection( 'pirates' )->drop();

/* Select the 'pirates' collection */
$mongo->collection( 'pirates' );

/* Set up datasets as an array of array values */
$datasets = array(
        array( 'name' => 'Black Beard', 'age' => 45, 'bounty' => 4000000 ),
        array( 'name' => 'Long John Silver', 'age' => 51, 'bounty' => 300000 ),
        array( 'name' => 'Dread Pirate Roberts', 'age' => 100, 'bounty' => 20000000 )
    );

/* Batch Insert the data into the DB */
$mongo->batchInsert( $datasets );

/* Single Where statement */
$pirates = $mongo->collection( 'pirates' )->where( 'name', 'Black Beard' )->get();

echo '<p><b>Where name == Black Beard</b></p>';
echo '<pre>'; 
print_r( $pirates );
echo '</pre>';

/* Single Where statement > */
$pirates = $mongo->collection( 'pirates' )->where( 'age', 50, '>' )->get();

echo '<p><b>Where age >= 50</b></p>';
echo '<pre>'; 
print_r( $pirates );
echo '</pre>';

/* Multiple And Where statements */
$pirates = $mongo->collection( 'pirates' )->where( 'age', 50, '>' )->and_where( 'bounty', 20000000, '>=' )->get();

echo '<p><b>Where age >= 50 AND bounty >= 20000000</b></p>';
echo '<pre>'; 
print_r( $pirates );
echo '</pre>';

/* Multiple Or Where statements */
$pirates = $mongo->collection( 'pirates' )->where( 'age', 45 )->or_where( 'bounty', 1000000, '<' )->get();

echo '<p><b>Where age == 45 OR bounty < 1000000</b></p>';
echo '<pre>'; 
print_r( $pirates );
echo '</pre>';