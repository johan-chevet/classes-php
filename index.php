<?php
require_once "User-pdo.php";
require_once "User.php";

// Class name for button to see whats active
function isActive(string $name): string
{
    return $_SESSION["chosen_class"] === $name ? "active" : "";
}

session_start();

// Set class to use, choose mysqli by default
if (!isset($_SESSION["chosen_class"])) {
    $_SESSION["chosen_class"] = "mysqli";
}

// Change the class to use if user click on the other button
if (isset($_POST["class-choice"]) && $_SESSION["chosen_class"] !== $_POST["class-choice"]) {
    $_SESSION["chosen_class"] = $_POST["class-choice"] === "pdo" ? "pdo" : "mysqli";
}

// Set instance of user
$user = $_SESSION["chosen_class"] === "pdo" ? new Userpdo() : new User();

// Connect the user if credentials found in sessions
if (isset($_SESSION["user_credentials"])) {
    [$login, $password] = $_SESSION["user_credentials"];
    $user->connect($login, $password);
}

// Handle submit register
if (isset($_POST["submit-register"])) {
    $paramList = ["login", "password", "email", "firstname", "lastname"];
    $errors = null;
    foreach ($paramList as $param) {
        if (!isset($_POST[$param]) || $_POST[$param] === "") {
            $errors[] = "'$param' is not valid";
        }
    }
    if (!isset($errors)) {
        $result = $user->register(
            $_POST["login"],
            $_POST["password"],
            $_POST["email"],
            $_POST["firstname"],
            $_POST["lastname"]
        );
    }
}

// Handle submit login
if (isset($_POST["submit-login"])) {
    $paramList = ["login", "password"];
    $errors = null;
    foreach ($paramList as $param) {
        if (!isset($_POST[$param]) || $_POST[$param] === "") {
            $errors[] = "'$param' is not valid";
        }
    }
    if (!isset($errors)) {
        $user->connect($_POST["login"], $_POST["password"]);
        if ($user->isConnected()) {
            $_SESSION["user_credentials"] = [$_POST["login"], $_POST["password"]];
            $result = $user->getAllInfos();
        } else {
            $result[] = "Invalid credentials";
        }
    }
}

// Handle submit update
if (isset($_POST["submit-update"])) {
    $paramList = ["login", "password", "email", "firstname", "lastname"];
    $errors = null;
    foreach ($paramList as $param) {
        if (!isset($_POST[$param]) || $_POST[$param] === "") {
            $errors[] = "'$param' is not valid";
        }
    }
    if (!isset($errors)) {
        $user->update(
            $_POST["login"],
            $_POST["password"],
            $_POST["email"],
            $_POST["firstname"],
            $_POST["lastname"]
        );
        $result = $user->getAllInfos();
        if (!count($result)) {
            $result[] = "User is not logged in";
        }
    }
}

// Handle disconnect
if (isset($_POST["submit-disconnect"])) {
    $user->disconnect();
    unset($_SESSION["user_credentials"]);
}
// Handle delete
if (isset($_POST["submit-delete"])) {
    $user->delete();
    unset($_SESSION["user_credentials"]);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Classes</title>
</head>

<body>
    <form method="post">
        <div name="classes" id="classes">
            <button class="<?= "btn-class " . isActive("mysqli") ?>" name="class-choice" value="mysql">MYSQLI</button>
            <button class="<?= "btn-class " . isActive("pdo") ?>" name="class-choice" value="pdo">PDO</button>
        </div>
    </form>
    <?php
    if (!$user->isConnected()) {
        include "form-templates/register-form.html";
        if (isset($_POST["submit-register"])) {
            include "result.php";
        }
        include "form-templates/login-form.html";
        if (isset($_POST["submit-login"])) {
            include "result.php";
        }
    } else {
        include "user-connected.php";
        include "form-templates/update-form.html";
        if (isset($_POST["submit-update"])) {
            include "result.php";
        }
        include "form-templates/disconnect-form.html";
        include "form-templates/delete-form.html";
    }
    ?>
</body>

</html>