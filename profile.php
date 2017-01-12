<?php
    require("config.php");
    require("includes/PasswordHash.php");
    session_start();
    if (!isset($_SESSION['email']))
    {
        header("Location: index.php");
        exit();
    }
    $db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
?>
<html>
    <head>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.3.0/css/bulma.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
    </head>

    <?php
        include("includes/nav.php");
        if($_SERVER["REQUEST_METHOD"] == "POST")
        {
            if($db->connect_errno)
            {
                die("Failed to connect to MySQL:");
            }
            $firstName = $db->real_escape_string(htmlspecialchars($_POST["firstName"]));
            $lastName = $db->real_escape_string(htmlspecialchars($_POST["lastName"]));
            $email = $db->real_escape_string(htmlspecialchars($_POST["email"]));
            $userid = $_SESSION['userid'];
            $password = isset($_POST['password']) ? $_POST['password'] : 0; //initialise the password variable
            
            $db->query("UPDATE user SET firstname = '$firstName', lastname = '$lastName', email='$email' WHERE id='$userid'");
            if ($password != "")
            {
                // Securely hash the password with salted bcrypt
                $ph = new PasswordHash(8, true);
                $hash = $ph->HashPassword($password);
                $hash = $db->real_escape_string($hash);
                $db->query("UPDATE user SET password = '$hash'");
            }
            $_SESSION["email"] = $email;
            $_SESSION["firstname"] = $firstName;
            $_SESSION["lastname"] = $lastName;
            header("Location: index.php?data_updated");

        }

        if(isset($_SESSION["email"]))
        {
        ?>

        <body>
            <h1 class="title is-one">Profile Settings</h1>
            <br>
            <h4 class="title is-four">Change Settings </h4>
            <div class="box">
                <form method="POST" action="profile.php">
                    <div class="control is-horizontal">
                        <div class="control is-grouped">
                            <p class="control is-expanded">
                                <input class="input" type="text" value="<?=$deets['firstname'];?>" name="firstName" placeholder="First Name">
                            </p>
                            <p class="control is-expanded">
                                <input class="input" type="text" value="<?=$deets['lastname'];?>"name="lastName" placeholder="Last Name">
                            </p>
                        </div>
                    </div>

                    <div class="control">
                            <p class="control">
                                <input class="input" type="email" value="<?=$deets['email'];?>" placeholder="Email" name="email">
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
                    <p class = "control">
                        <button class="button is-success" type="submit">
                            Submit
                        </button>
                    </p>  
        
        <?php
        }

        else
        {
            header("Location: index.php");
        }

        ?>

