<?php

namespace App\Repositories\Statements;

use PDO;
use PDOStatement;

class GetGlobalQuotesLogStatement extends BaseStatement {

    public int $limit = 10;

    /**
     * @param   array   $arg   Array: [0] => table name; [1] => owner; [2] offset
     */
    public function __invoke(...$args): PDOStatement {
        $stmt = $this->conn->prepare("SELECT `owner`, `datetime_request`, `quote`, `symbol` FROM {$args[0]} WHERE `owner` = :owner ORDER BY `datetime_request` DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue('owner', $args[1], PDO::PARAM_STR);
        $stmt->bindValue('limit', $this->limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $args[2], PDO::PARAM_INT);

        return $stmt;
    }

}
