# MongoDB PHP Wrapper

A PHP Class Wrapper for MongoDB that provides an Active Record method with which to interact with a MongoDB system. Allows programmatic query creation and execution for CRUD operations. 

**Note**: Every time you perform an operation ( even within the same PHP function ), you must re-define the Collection you're drawing from, as the query resets itself each time it runs. This is to avoid situations in which Collections are modified unintentionally due to not resetting the collection name after the query is complete.

## DB

### Description

Sets the DB select the collections from

### Parameters

**`$db_name`** - (*string*) (*required*) - The name of the DB

### Usage
    
    $this->db( 'my_db_name' );

## Collection

### Description

Sets Collection from which to draw records

### Parameters

**`$collection_name`** - (*string*) (*required*) - The name of the collection

### Usage
    
    $this->db( 'my_collection_name' );

## Where

### Description

Defines conditions that must be met in the query. You may use `where()`, `and_where()`, and `or_where()` to combine conditions.

**Note**: You may only use `and_where()` OR `or_where()` in a single query. You cannot combine the two at this time.

### Parameters

**`$key`** - (*string*) (*required*) - The key to be matched against
**`$value`** - (*string*) (*required*) - The value to match against the given key
**`$compare`** - (*string*) (*optional*) - The operator to use for the comparison ('=', '!=', '>', '>=', '<', '<='). Default is '='

### Usage
    
    // Single where condition
    $this->where( 'foo', 'bar' );
    $rows = $this->get();

    // Multiple where conditions (AND)
    $this->where( 'foo', 'bar', '=' );
    $this->and_where( 'blah', 'bleck', '!=' );
    $rows = $this->get();

    // Multiple where conditions (OR)
    $this->where( 'foo', 'bar', '=' );
    $this->or_where( 'blah', 'bleck', '!=' );
    $rows = $this->get();

## Get Single Row

### Description

Returns a single row matching the given WHERE statements

### Parameters

No parameters (set WHERE conditions using `$this->where()`

### Usage
    $this->where( 'foo', 'bar', '=' );
    $row = $this->getRow();

## Get

### Description

Returns an array of all rows matching the given WHERE statements

### Parameters

No parameters (set WHERE conditions using `$this->where()`

### Usage

    $this->where( 'foo', 'bar', '=' );
    $rows = $this->get();

## Insert

### Description

Inserts the given data into the chosen collection

### Parameters

**`$data`** - (*array*) (*required*) - The data to insert into the collection

### Usage
    
    $data = array( 'foo' => 'bar', 'this' => 'that' );
    $rows = $this->insert( $data );

## Batch Insert

### Description

Inserts an array of data into the Collection all at once. 

### Parameters

**`$data_set`** - (*array*) (*required*) - A multidimensional array of data to insert into the Collection

### Usage
    
    $data_set = array(
            array( 'foo' => 'bar', 'this' => 'that' ),
            array( 'foo' => 'baz', 'this' => 'nothing' )
        );
    $rows = $this->batchInsert( $data_set );

## Update

### Description

Updates records matching the WHERE conditions with the provided data array

### Parameters

**`$data`** - (*array*) (*required*) - An array of key/value pairs to update in the matching records
**`$multiple`** - (*boolean*) (*optional*) - If set to *true*, all matching records will be updated (defaults to *false*)
**`$upsert`** - (*boolean*) (*optional*) - If set to *true*, a record will be added to the Collection if no matching records can be found

### Usage
    
    // Update the first matching record where foo == bar
    $data = array( 'foo' => 'bar' );
    $this->where( 'this' => 'that' );
    $this->update( $data );

    // Update all records where foo == bar
    $data = array( 'foo' => 'bar' );
    $this->where( 'this' => 'that' );
    $this->update( $data, true );

    // Insert a new record if no records match foo == bar
    $data = array( 'foo' => 'bar' );
    $this->where( 'this' => 'that' );
    $this->update( $data, true, true );

## Delete

### Description

Deletes records matching the WHERE condition

### Parameters

No parameters (set WHERE conditions using `$this->where()`

### Usage
    
    $this->where( 'foo', 'bar', '=' );
    $rows = $this->delete();

## Examples

Look in the /examples folder for sample files containing working code for each operation. (Be sure to set up your configuration options in /src/Mongo.php before running the sample files).

## Single-Line Notation

This wrapper supports a single-line notation format. The following operations will result in the same action:
    
    $this->db( 'my_db_name' );
    $this->collection( 'my_collection_name' );
    $this->where( 'foo', 'bar', '!=' );
    $this->delete();

    $this->db( 'my_db_name' )->collection( 'my_collection_name')->where( 'foo', 'bar', '!=' )->delete();
    