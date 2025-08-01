<?php
class Userpdo
{
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;

    private $dbh;

    public function __construct()
    {
        $this->dbh = new PDO("mysql:host=localhost;dbname=classes", "root", "");;
        $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function register(
        string $login,
        string $password,
        string $email,
        string $firstname,
        string $lastname
    ) {
        try {
            $exist = $this->dbh->prepare("SELECT id FROM utilisateurs WHERE login=?");
            $exist->execute([$login]);
            if ($exist->rowCount() > 0) {
                echo "Login '$login' already taken";
                return;
            }
            $stmt = $this->dbh->prepare("INSERT INTO utilisateurs (login, password, email, firstname, lastname)
             VALUES (:login, :password, :email, :firstname, :lastname)");
            $stmt->bindParam("login", $login);
            $stmt->bindParam("password", $password);
            $stmt->bindParam("email", $email);
            $stmt->bindParam("firstname", $firstname);
            $stmt->bindParam("lastname", $lastname);
            $stmt->execute();
            return [$this->dbh->lastInsertId(), $login, $email, $firstname, $lastname];
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function connect(string $login, string $password)
    {
        $stmt = $this->dbh->prepare("SELECT * FROM utilisateurs WHERE login=? AND password=? LIMIT 1");
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute([$login, $password]);
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
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
            $stmt = $this->dbh->prepare("DELETE FROM utilisateurs WHERE id=:id");
            $stmt->execute([$this->id]);
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
            $stmt = $this->dbh->prepare("UPDATE utilisateurs SET 
            login=?, password=?, email=?,
            firstname=?, lastname=? WHERE id=?");
            $updated = $stmt->execute([$login, $password, $email, $firstname, $lastname, $this->id]);
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
