<?php
require_once 'Movie.php';

class DB {

    private $dbh;
    private $stmt;

    public function __construct($user="root", $pass="root", $dbname="moviesdb") {
        $this->dbh = new PDO(
            "mysql:host=localhost;dbname=$dbname",
            $user,
            $pass,
            array( PDO::ATTR_PERSISTENT => true )
        );
    }

    public function query($query) {
        $this->stmt = $this->dbh->prepare($query);
        return $this;
    }

    public function bind($pos, $value, $type = null) {
        if( is_null($type) ) {
            switch( true ) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }

        $this->stmt->bindValue($pos, $value, $type);
        return $this;
    }

    public function execute() {
        return $this->stmt->execute();
    }

    public function resultset() {
        $this->execute();
        $result = $this->stmt->fetchAll(PDO::FETCH_CLASS, "Movie");
        return $result;
    }

    public function single() {
        $this->execute();
        $this->stmt->setFetchMode(PDO::FETCH_CLASS, 'Movie');
        $result = $this->stmt->fetch();
        return $result;
    }
}
