<?php
    session_start();
    // If the user is already logged in then redirect them to the home page because there's no need to sign up again
    if (isset($_SESSION['email']))
    {
        header("Location: index.php");
        exit();
    }

    require("config.php");
    require("includes/PasswordHash.php");
    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        $firstName = $db->real_escape_string(htmlspecialchars($_POST["firstName"]));
        $lastName = $db->real_escape_string(htmlspecialchars($_POST["lastName"]));
        $email = $db->real_escape_string(htmlspecialchars($_POST["email"]));
        $passwd = $_POST["password"];
        $privlevel = 1; // Only an admin can create an administrator or disable/enable an account
        
        if ($db->connect_errno)
        {
            die("Failed to connect to MySQL");
        }
        else
        {
            $ph = new PasswordHash(8, true);
            $pwhash = $ph->HashPassword($passwd);
            $pwhash = $db->real_escape_string($pwhash);
            $db->query("INSERT INTO `user` (email, password, firstname, lastname, privilege_level) VALUES('$email', '$pwhash', '$firstName', '$lastName', 1)");
            header("Location: login.php?created");
            exit();
        }
    }
?>
<!doctype html>
<html>
    <head>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.3.0/css/bulma.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
    </head>

    <body>
        <?php require("includes/nav.php"); ?>
        <h1 class="title is-one"> Planet Express </h1>
        <h3 class="title is-three"> Sign Up Here </h1>
        <form method="POST">
            <div class="control is-horizontal">
                <div class="control is-grouped">
                    <p class="control is-expanded">
                        <input class="input" type="text" name="firstName" placeholder="First Name">
                    </p>
                    <p class="control is-expanded">
                        <input class="input" type="text" name="lastName" placeholder="Last Name">
                    </p>
                </div>
            </div>

            <div class="control">
                    <p class="control">
                        <input class="input" type="email" placeholder="Email" name="email">
                    </p>
            </div>

            <div class="control is-horizontal">
                <div class="control is-grouped">
                    <p class="control is-expanded">
                        <input class="input" type="password" placeholder="Password" name="password">
                    </p>
                    <p class="control is-expanded">
                        <input class="input" type="password" placeholder="Enter Password">
                    </p>
                </div>
            </div>

            <p class="control">
                <input class="input" type="hidden" placeholder="privilege_level" name="privilege_level" value="1">
            </p>

            <p class = "control">
                <button class="button is-success" type="submit">
                    Submit
                </button>
            </p>
