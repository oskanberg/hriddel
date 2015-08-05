<?php

/*
 * I deliberately don't have getters and setters
 * in this case they would only be single-line no-logic operations
 */

class User extends AbstractObject
{
    public $username;
    public $name;
    public $type;
}
