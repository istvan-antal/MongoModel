<?php

/**
 * @copyright  Copyright (c) 2013 István Miklós Antal
 * @author     István Miklós Antal istvan.m.antal@gmail.com
 * @license    MIT License, see: license.txt
 */

interface MongoModelInterface {
    
    /**
     * Returns the collection name of the model.
     * 
     * @return string
     */
    public static function collection();
    
    /**
     * Returns a mongo connection.
     * 
     * @return MongoDB
     */
    public static function getConnection();
}

abstract class MongoModel implements MongoModelInterface {
    
    /**
     * Indicates if the exists in the database.
     * 
     * @var boolean
     */
    protected $isNew = true;
    
    /**
     * Indicates if the record already existed in the database on an attempted insert.
     * 
     * @var type 
     */
    protected $was_already_in_database = false;
    
    /**
     * Record data.
     * 
     * @var array
     */
    protected $data = array();
    
    /**
     * Returns the mongo collection for the model.
     * 
     * @return MongoCollection
     */
    private static function getModelCollection() {
        return static::getConnection()->selectCollection(static::collection());
    }
    
    /**
     * Constructor.
     * 
     * @param string $id Record unique id.
     * 
     * @throws Exception Throws an exception if an id was specified but doesn't 
     * exist in the database.
     */
    public function __construct($id = null) {
        if ($id) {
            $collection = self::getModelCollection();
            
            $this->data = $collection->findOne(array('_id' => $id));
            
            if ($this->data) {
                $this->isNew = false;
            } else {
                throw new Exception('Item does not exist');
            }
        }
    }

    /**
     * Returns the records unique id.
     * 
     * @return string
     */
    public function getId() {
        return $this->data['_id'];
    }

    /**
     * Attempts to insert the record into the database.
     * 
     * @return void
     * 
     * @throws MongoCursorException Throws exception on database failure.
     */
    public function store() {
        $collection = self::getModelCollection();
        
        if ($this->isNew) {
            
            try {
                $collection->insert($this->data);
            } catch (MongoCursorException $e) {
                if ($e->getCode() === 11000) {
                    $this->was_already_in_database = true;
                } else {
                    throw $e;
                }
            }
            
            $this->isNew = false;
        }
    }
    
    /**
     * Sets the records data.
     * 
     * @param array $data Data array.
     * 
     * @return void
     */
    public function setData(array $data) {
        $this->data = $data;
    }
    
    /**
     * Returns the records representation as an array, this may be different from the raw record data.
     * 
     * @return array
     */
    public function export() {
        return $this->data;
    }
    
    /**
     * Queries for records.
     * 
     * @param array   $query Query array.
     * @param integer $limit Max amount of records to be returned.
     * 
     * @return array
     */
    public static function find(array $query = array(), $limit = null) {
        $result = array();
        
        $cursor = self::getModelCollection()->find($query);
        
        if ($limit !== null) {
            $cursor->limit($limit);
        }
        
        $class = get_called_class();
        
        foreach ($cursor as $data) {
            $item = new $class;
            $item->setData($data);
            $result[]= $item;
        }
        
        return $result;
    }
    
    /**
     * Counts the items in the collection.
     * 
     * @return integer
     */
    public static function count() {
        return self::getModelCollection()->count();
    }
}