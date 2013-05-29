<?php
class Item extends MongoModel {
    
    /**
     * DB connection.
     * 
     * @var MongoDB
     */
    private static $db;
    
    /**
     * Lazy getter for DB connection.
     * 
     * @return MongoDb
     */
    public static function getConnection() {
        if (!self::$db) {
            $client = (new MongoClient('localhost'));
            self::$db = $client->selectDB('mongo_model_test_db');
        }
        return self::$db;
    }

    public static function collection() {
        return "item";
    }
    
    /**
     * Setters and getters.
     */
    
    public function setTitle($title) {
        $this->data['title'] = $title;
    }
    
    public function setDescription($description) {
        $this->data['title'] = $title;
    }
    
    public function getTitle() {
        return $this->data['title'];
    }
    
    public function getDescription() {
        return $this->data['title'];
    }
    
}