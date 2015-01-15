<?php
/**
 * MongoDB PHP CRUD Class Wrapper
 *
 * Provides an Active Record method with which to interact with a MongoDB 
 * system. Allows programattic query creation and execution for CRUD operations.
 *
 * @package mongo_api
 */

/* Global config */
define( 'DB_NAME', 'my_db_name' );
define( 'DB_HOST', 'mongodb://localhost' );

/**
 * Mongo API
 *
 * Provides an object from which to interact with the MongoDB
 * @package mongo_api
 */
class MongoAPI {

    private static $instance;
    private $mongo,
            $required,
            $db,
            $collection,
            $offset,
            $limit, 
            $where,
            $and_where,
            $or_where,
            $comps;

    function __construct() {
        $this->mongo = new MongoClient( DB_HOST );
        $this->db = DB_NAME;
        $this->comps = array( '!=' => '$ne', '>' => '$gt', '>=' => '$gte', '<' => '$lt', '<=' => '$lte' );
        $this->required = array( 'db' => 'Database', 'collection' => 'Collection' );
    }


    //======================================================================
    // UNIVERSAL METHODS
    //======================================================================

    /**
     * DB
     *
     * Sets the DB property
     * @param str $dbname
     * @return obj
     */
    public function db( $dbname ) {
        $this->db = $dbname;
        return $this;
    }

    /**
     * Collection
     *
     * Sets the Collection property
     * @param str $colname
     * @return obj
     */
    public function collection( $colname ) {
        $this->collection = $colname;
        return $this;
    }

    /**
     * Where
     *
     * Adds the where property, to be used in future queries
     * @param str $key
     * @param str $value
     * @param str $comp
     * @return obj
     */
    public function where( $key, $value, $comp = '=' ) {
        if( '=' === $comp ) {
            $this->where[] = array( $key => $value );
        }else{
            $comp_var = $this->comps[$comp];
            $this->where[] = array( $key => array( $comp_var => $value ) );
        }

        return $this;
    }

    /**
     * And Where
     *
     * Adds a value to the and_where property, to be used in future queries
     * @param str $key
     * @param str $value
     * @param str $comp
     * @return obj
     */
    public function and_where( $key, $value, $comp = '=' ) {
        if( '=' === $comp ) {
            $this->and_where[] = array( $key => $value );
        }else{
            $comp_var = $this->comps[$comp];
            $this->and_where[] = array( $key => array( $comp_var => $value ) );
        }

        return $this;
    }

    /**
     * Or Where
     *
     * Adds a value to the or_where property, to be used in future queries
     * @param str $key
     * @param str $value
     * @param str $comp
     * @return obj
     */
    public function or_where( $key, $value, $comp = '=' ) {
        if( '=' === $comp ) {
            $this->or_where[] = array( $key => $value );
        }else{
            $comp_var = $this->comps[$comp];
            $this->or_where[] = array( $key => array( $comp_var => $value ) );
        }

        return $this;
    }

    /**
     * Offset
     *
     * Sets the offset property, used to offset future queries
     * @param str $amount
     * @return obj
     */
    public function offset( $amount ) {
        $this->offset = $amount;

        return $this;
    }

    /**
     * Limit
     *
     * Sets the limit property, used to limit rows in future queries
     * @param str $amount
     * @return obj
     */
    public function limit( $amount ) {
        $this->limit = $amount;

        return $this;
    }

    /**
     * Drop
     *
     * Drops the specified collection from the DB entirely
     * @return obj
     */
    public function drop() {
        if( $this->isValid() != true ) {
            return $this->isValid();
        }

        $db = $this->mongo->selectDB( $this->db );
        $collection = $db->selectCollection( $this->collection );

        $collection->drop();

        return $this;
    }


    //======================================================================
    // FIND
    //======================================================================

    /**
     * Get Row
     *
     * Retrieves a single result from the DB, using the properties previously
     * set (where, db, and collection)
     * @return obj
     */
    public function getRow() {
        if( $this->isValid() !== true ) {
            return $this->isValid();
        }

        $db = $this->mongo->selectDB( $this->db );
        $collection = $db->selectCollection( $this->collection );

        $where = $this->setupWhere();

        $item = $collection->findOne( $where );
        $this->reset();
        return $item;
    }

    /**
     * Get
     *
     * Retrieves an array of results from the DB, using the properties previously
     * set (where, db, and collection)
     * @return array
     */
    public function get() {
        if( $this->isValid() != true ) {
            return $this->isValid();
        }

        $items = array();

        $db = $this->mongo->selectDB( $this->db );
        $collection = $db->selectCollection( $this->collection );

        $where = $this->setupWhere();

        $limit = is_null( $this->limit ) ? 0 : $this->limit;
        
        $cursor = $collection->find( $where )->skip( $this->offset )->limit( $limit );

        $this->reset();

        foreach( $cursor as $c ) {
            $items[] = $c;
        }

        return $items;
    }


    //======================================================================
    // INSERT
    //======================================================================

    /**
     * Insert
     *
     * Inserts a value into the DB, using the properties previously
     * set (where, db, and collection)
     * @param array $data
     * @return obj
     */
    public function insert( $data ) {
        if( $this->isValid() !== true ) {
            return $this->isValid();
        }

        $db = $this->mongo->selectDB( $this->db );
        $collection = $db->selectCollection( $this->collection );

        $this->reset();

        return $collection->insert( $data );
    }

    /**
     * Batch Insert
     *
     * Inserts a set of values into the DB, using the properties previously
     * set (where, db, and collection)
     * @param array $datasets
     * @return array
     */
    public function batchInsert( $datasets ) {
        if( $this->isValid() !== true ) {
            return $this->isValid();
        }

        $db = $this->mongo->selectDB( $this->db );
        $collection = $db->selectCollection( $this->collection );

        $return = array();
        foreach( $datasets as $data ) {
            $return[] = $collection->insert( $data );
        }
        
        $this->reset();

        return $return;
    }


    //======================================================================
    // UPDATE
    //======================================================================

    /**
     * Update
     *
     * Updates a set of values in the DB, using the properties previously
     * set (where, db, and collection)
     * @param array $data
     * @param bool $multiple
     * @param bool $upsert
     * @return obj
     */
    public function update( $data, $multiple = false, $upsert = false ) {
        if( $this->isValid() !== true ) {
            return $this->isValid();
        }

        $db = $this->mongo->selectDB( $this->db );
        $collection = $db->selectCollection( $this->collection );

        $where = $this->setupWhere();

        $this->reset();

        return $collection->update( $where, array( '$set' => $data ), array( 'multiple' => $multiple, 'upsert' => $upsert ) );
    }

    //======================================================================
    // DELETE
    //======================================================================

    /**
     * Delete
     *
     * Deletes a value from the DB, using the properties previously
     * set (where, db, and collection)
     * @param bool $multiple
     * @return obj
     */
    public function delete( $multiple = true ) {
        if( $this->isValid() !== true ) {
            return $this->isValid();
        }

        $db = $this->mongo->selectDB( $this->db );
        $collection = $db->selectCollection( $this->collection );

        $where = $this->setupWhere();

        $this->reset();

        return $collection->remove( $where, array( 'multiple' => $multiple ) );
    }

    //======================================================================
    // UTILITIES
    //======================================================================   

    /**
     * Setup Where
     *
     * Parses the previously set where, and_where, and or_where properties
     * and combines them into a single value that the MongoDB query can use
     * @return array
     */
    private function setupWhere() {
        $where = array();

        if( count( $this->where ) ) {

            if( count( $this->and_where ) ) {
                $where['$and'][] = $this->where[0];
            }elseif( count( $this->or_where ) ) {
                $where['$or'][] = $this->where[0];
            }else{
                $where = $this->where[0];
            }
            
        }

        if( count( $this->and_where ) ) {
            foreach( $this->and_where as $condition ) {
                $where['$and'][] = $condition;
            }
        }

        if( count( $this->or_where ) ) {
            foreach( $this->or_where as $condition ) {
                $where['$or'][] = $condition;
            }
        }

        return $where;
    }
    
    /**
     * Is Valid
     *
     * Checks to verify if a DB and Collection property has been set.
     * If not, return error message. 
     * @return bool OR str
     */
    private function isValid() {
        $result = '';

        foreach( $this->required as $key => $label ) {
            if( !$this->$key || '' === $this->$key ) {
                $result .= "You must set a value for $label<br />";
            }
        }

        if( $result !== '' ) {
            return $result;
        }

        return true;
    }

    /**
     * Reset
     *
     * Resets the various Query properties to avoid accidental
     * queries against an unintended database
     */
    private function reset() {
        $this->db = DB_NAME;
        $this->collection = '';
        $this->offset = 0;
        $this->limit = 0;
        $this->where = array();
        $this->and_where = array();
        $this->or_where = array();
    }

    /**
    * Get Instance
    *
    * Gets the Singleton instance for this class
    * @return obj
    */
    public static function getInstance(){

        if(self::$instance === null){
            self::$instance = new MongoAPI();
        }

        return self::$instance;
    }   

}