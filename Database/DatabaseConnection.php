<?php
/**
 * Class to hold the exception message
 *
 * @category  Mamut
 * @package   Gloversure_Mamut
 * @author    Charles Palmer <chp@gloversure.co.uk>
 * @copyright Gloversure Ltd
 *
 */
class DBException extends Exception {
    
    protected $query;

    public function __construct($errstr, $errno, $query = null) {
        $this->query = $query;

        parent::__construct($errstr, $errno);
    }

    public function getQuery() {
        return $this->query;
    }

    public function __toString() {
        if(is_string($this->query)){
            return $this->getMessage() . ": ". $this->query;
        } else {
            return $this->getMessage();
        }
    }
}

/** Database conneection class for Mamut
 *
 * @category  Mamut
 * @package   Gloversure_Mamut
 * @author    Charles Palmer <chp@gloversure.co.uk>
 * @copyright 2016 Gloversure Ltd
 *
 */
class Gloversure_Mamut_Database_DatabaseConnection {

    protected $connection;
    
    public function __construct($user, $pass, $dsn)
    {
        $this->connect($user, $pass, $dsn);
    }

    /**
     * Connects to the database
     *
     * @param string $user
     * @param string $pass
     * @param string $dsn
     *
     * @access private
     *
     */
    private function connect($user, $pass, $dsn)
    {
        try {
            $this->connection = new PDO("odbc:".$dsn, $user, $pass);
        } catch (PDOException $e) {
            throw new DBException($e, 2);
        }
    }
    
    /**
     * Checks the sql and executes the query
     *
     * @param string $sql
     * 
     * @access public
     *
     * @return MySQLResult
     *
     */
    public function query($sql)
    {
        // Check the sql
        if(!empty($sql)) {
            return $this->executeQuery($sql);
        }
        
        throw new DBException('missing query', 2);
    }

    /**
     * Executes a query against the database
     *
     * @param string $sql
     *
     * @access private
     *
     * @return MySQLResult
     *
     */
    private function executeQuery($sql)
    {
        $stmt = $this->connection->prepare($sql);
        
        $stmt->execute(); 
        
        if(!$resource){
            return $stmt;
        }
        
        throw new DBException($this->connection->errorCode(), $this->connection->errorInfo(), $sql);
    }
}

