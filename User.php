<?php
class User
{
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;

    public function __construct() {}

    public function register(
        $login,
        $password,
        $email,
        $firstname,
        $lastname
    ) {}

    public function connect(string $login, string $password) {}
}
