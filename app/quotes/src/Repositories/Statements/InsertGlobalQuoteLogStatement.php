<?php

namespace App\Repositories\Statements;

use PDO;
use PDOStatement;

class InsertGlobalQuoteLogStatement extends BaseStatement {

    /**
     * @param   array   $arg   Array: [0] => table name; [1] => GlobalQuoteLog;
     */
    public function __invoke(...$args): PDOStatement {
        $stmt = $this->conn->prepare("INSERT INTO {$args[0]} (`owner`, `datetime_request`, `quote`, `symbol`) VALUES (:owner,:datetime_request,:quote,:symbol)");
        
        $stmt->bindValue('owner', $args[1]->getOwner(), PDO::PARAM_STR);
        $stmt->bindValue('datetime_request', $args[1]->getDateTimeRequest()->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindValue('quote', json_encode($args[1]->getQuote()), PDO::PARAM_STR);
        $stmt->bindValue('symbol', $args[1]->getSymbol(), PDO::PARAM_STR);

        return $stmt;
    }

}
