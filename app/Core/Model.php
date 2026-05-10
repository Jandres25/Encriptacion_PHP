<?php

namespace App\Core;

abstract class Model
{
    public function __construct(protected \mysqli $db) {}
}
