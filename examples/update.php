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
        array( 'name' => 'Black Beard', 'age' => 45, 'bounty' => '$4,000,000' ),
        array( 'name' => 'Long John Silver', 'age' => 51, 'bounty' => '$300,000' ),
        array( 'name' => 'Dread Pirate Roberts', 'age' => 100, 'bounty' => '$20,000,000' )
    );

/* Batch Insert the data into the DB */
$mongo->batchInsert( $datasets );

/* Get the items back */
$pirates = $mongo->collection( 'pirates' )->get();

/* Print it to page */
echo '<p><b>Before Update Singular</b></p>';
echo '<pre>'; 
print_r( $pirates );
echo '</pre>';

/* Update pirates where age >= 50 (single update) */
$data = array( 'updated' => true );
$mongo->collection( 'pirates' )->where( 'age', 50, '>=' )->update( $data );

/* Get the items back */
$pirates = $mongo->collection( 'pirates' )->get();

/* Print it to page */
echo '<p><b>After Update Single where age >= 50 (only updates the first matching record)</b></p>';
echo '<pre>'; 
print_r( $pirates );
echo '</pre>';

/* Delete pirates where age == 51 */
$data = array( 'updated' => true );
/* Set second parameter to true to update all matching records */
$mongo->collection( 'pirates' )->where( 'age', 50, '>=' )->update( $data, true );

/* Get the items back */
$pirates = $mongo->collection( 'pirates' )->get();

/* Print it to page */
echo '<p><b>After Multiple update where age >= 50 (updates all matching records)</b></p>';
echo '<pre>'; 
print_r( $pirates );
echo '</pre>';