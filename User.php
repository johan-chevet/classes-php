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
    }

    public function register(
        string $login,
        string $password,
        string $email,
        string $firstname,
        string $lastname
    ) {
        try {
            $exist = $this->mysqli->query("SELECT id FROM utilisateurs WHERE login='$login'");
            if ($exist->num_rows > 0) {
                return ["Login '$login' already taken"];
            }
            $this->mysqli->query("INSERT INTO utilisateurs (login, password, email, firstname, lastname)
             VALUES ('$login', '$password', '$email', '$firstname', '$lastname')");
            return [$this->mysqli->insert_id, $login, $email, $firstname, $lastname];
        } catch (mysqli_sql_exception $e) {
            echo $e->getMessage();
        }
    }

    public function connect(string $login, string $password)
    {
        $data = $this->mysqli->query("SELECT * FROM utilisateurs WHERE login='$login' AND password='$password' LIMIT 1");
        if ($data->num_rows > 0) {
            $user = $data->fetch_assoc();
            $this->id = $user["id"];
            $this->login = $user["login"];
            $this->email = $user["email"];
            $this->firstname = $user["firstname"];
            $this->lastname = $user["lastname"];
        }
    }

    public function disconnect()
    {
        $this->id = null;
    }

    public function delete()
    {
        if ($this->isConnected()) {
            $this->mysqli->query("DELETE FROM utilisateurs WHERE id=$this->id");
            $this->disconnect();
        }
    }

    public function update(
        string $login,
        string $password,
        string $email,
        string $firstname,
        string $lastname
    ) {
        if ($this->isConnected()) {
            $updated = $this->mysqli->query("UPDATE utilisateurs SET 
            login='$login', password='$password', email='$email',
            firstname='$firstname', lastname='$lastname' WHERE id='$this->id'");
            if ($updated) {
                $this->login = $login;
                $this->email = $email;
                $this->firstname = $firstname;
                $this->lastname = $lastname;
            }
        }
    }

    public function isConnected()
    {
        return $this->id ? true : false;
    }

    public function getAllInfos()
    {
        if ($this->isConnected()) {
            return [$this->id, $this->login, $this->email, $this->firstname, $this->lastname];
        }
        return [];
    }

    public function getLogin()
    {
        return $this->login;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function getFirstname()
    {
        return $this->firstname;
    }
    public function getLastname()
    {
        return $this->lastname;
    }
}
