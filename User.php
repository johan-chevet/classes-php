<?php
class User
{
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;

    private $mysqli;

    public function __construct()
    {
        $this->mysqli = new mysqli("localhost", "root", "", "classes");
        if ($this->mysqli->connect_errno) {
            echo ("Db connection error: " . $this->mysqli->connect_error);
            $this->mysqli = null;
        }
    }

    private function query(string $sql)
    {
        try {
            return $this->mysqli?->query($sql);
        } catch (mysqli_sql_exception $e) {
            echo $e->getMessage();
        }
    }

    public function register(
        string $login,
        string $password,
        string $email,
        string $firstname,
        string $lastname
    ) {
        // Todo: Validation? return array of info
        $inserted = $this->query("INSERT INTO utilisateurs (login, password, email, firstname, lastname)
             VALUES ('$login', '$password', '$email', '$firstname', '$lastname')");
        if ($inserted) {
            return [];
        }
    }

    public function connect(string $login, string $password)
    {
        $data = $this->query("SELECT * FROM utilisateurs WHERE login='$login' AND password='$password' LIMIT 1");
        if ($data->num_rows > 0) {
            $user = $data->fetch_assoc();
            $this->id = $user["id"];
            $this->login = $user["login"];
            $this->email = $user["email"];
            $this->firstname = $user["firstname"];
            $this->lastname = $user["lastname"];
        }
    }
}

$user = new User();
$user->register("test", "test", "email", "prenom", "nom");
// $users->connect("test", "test");
// var_dump($users);
