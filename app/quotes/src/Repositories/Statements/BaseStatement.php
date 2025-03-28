<?php

namespace App\Repositories\Statements;

use App\Helpers\MySqlConnection;
use App\Interfaces\BaseStatementInterface;
use Pdo\Mysql;

abstract class BaseStatement implements BaseStatementInterface {

    protected Mysql $conn;

    public function __construct() {
        $this->conn = MySqlConnection::getConnection();
    }
 
}