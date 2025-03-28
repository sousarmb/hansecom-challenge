<?php

namespace App\Interfaces;

use \PDOStatement;

interface BaseStatementInterface {
    public function __invoke(...$args): PDOStatement;
}