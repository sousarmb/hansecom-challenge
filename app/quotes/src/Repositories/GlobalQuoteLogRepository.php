<?php

namespace App\Repositories;

use PDO;
use PDO\Mysql;
use App\Helpers\MySqlConnection;
use App\Models\GlobalQuoteLog;
use App\Repositories\Statements\InsertGlobalQuoteLogStatement;
use App\Repositories\Statements\GetGlobalQuotesLogStatement;


class GlobalQuoteLogRepository {

    private Mysql $conn;
    private string $table = 'quote_requests';

    public function __construct() {
        $this->conn = MySqlConnection::getConnection();
    }

    /**
     * Log quote request
     * 
     * @param   GlobalQuoteLog  $globalQuoteLog 
     * @return  int
     */
    public function insert(GlobalQuoteLog $globalQuoteLog): bool {
        $pStmt = (new InsertGlobalQuoteLogStatement())(
            $this->table,
            $globalQuoteLog
        );
        $pStmt->execute();

        return (bool)$pStmt->rowCount();
    }

    /**
     * Get quote requests for User
     * 
     * @param   string  $owner  User's email hash (used for anonymisation)
     * @param   int $offset Use to return slice of records (never returns the whole data set (overhead)) 
     * @return  array
     */
    public function get(
        string $owner,
        int $offset
    ): array {
        // check if the offset is the same as the record count
        // prevents case where client request one quote and clicks button to get more quotes and only has one
        // quote request in the database
        $pStmt = $this->conn->prepare("SELECT COUNT(1)=$offset AS `go` FROM {$this->table} WHERE `owner`=:owner");
        $pStmt->bindValue('owner', $owner, PDO::PARAM_STR);
        $pStmt->execute();
        if ((bool)$pStmt->fetchColumn()) {
            return [];
        }

        $pStmt = (new GetGlobalQuotesLogStatement())(
            $this->table,
            $owner,
            $offset
        );
        $pStmt->execute();

        return $pStmt->fetchAll(PDO::FETCH_OBJ);
    }
}
