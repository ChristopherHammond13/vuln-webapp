<?php
    require("config.php");
    require("includes/PasswordHash.php");
    session_start();
    if (isset($_SESSION['email']))
    {
        header("Location: dashboard.php");
        exit();
    }

    $db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email']) && isset($_POST['password']))
    {
        $myemail = $db->real_escape_string($_POST["email"]);
        $mypassword = $_POST["password"];

        // Super super securely check the email and password even when there's spaces!!!!
        $sql = "SELECT * FROM user WHERE email = '$myemail'";
        $result = $db->query($sql);

        // Check that there's actually a result
        if ($result)
        {
            if ($result->num_rows > 0)
            {
                // There was a result, so let's check that the password is actually correct
                $ph = new PasswordHash(8, true);
                $row = $result->fetch_array(MYSQLI_ASSOC);
                if ($ph->CheckPassword($mypassword, $row["password"]))
                {
                    $_SESSION["email"] = $row["email"];
                    $_SESSION["firstname"] = $row["firstname"];
                    $_SESSION["lastname"] = $row["lastname"];
                    $_SESSION["privilege_level"] = $row["privilege_level"];
                    $_SESSION['userid'] = $row['id'];
                    header("Location: dashboard.php");
                    exit();
                }
                else
                {
                    header("Location: login.php?invalid");
                    exit();
                }
                
            }
            else
            {
                header("Location: login.php?invalid");
                exit();
            }
        }
        else
        {
            header("Location: login.php?invalid");
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
        <div class="box">
            <?php
                if ( isset($_GET['invalid'] ))
                {
                     echo "The email or password you entered is invalid.";
                }
                else if (isset($_GET['created']))
                {
                    echo "Account created successfully, so please now sign in below.";
                }
            ?>
            <form method="POST">
                <p class="control has-icon">
                    <input class="input is-primary" name="email" type="text" placeholder="Email address">
                    <span class="icon is-small">
                        <i class="fa fa-envelope"></i>
                    </span>
                </p>
                <p class="control has-icon">
                    <input class="input is-danger" name="password" type="password" placeholder="Password">
                    <span class="icon is-small">
                        <i class="fa fa-lock"></i>
                    </span>
                </p>
                <p class="control">
                    <button class="button is-success" type="submit">
                        Login
                    </button>
                </p>
                <br>
                <br>
                <p class = "control">
                    <button class="button is-success">
                        Forgot Email
                    </button>
                </p>
                <p class = "control">
                    <button class="button is-success">
                        Forgot Password
                    </button>
                </p>
            </form>
        </div>
    </body>
