<?php 
    require("config.php");
    session_start();

    if (!isset($_SESSION['username']))
    {
        header("Location: index.php");
        exit();
    }

    $db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
?>
<!doctype html>
<html>
    <head>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.3.0/css/bulma.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
    </head>
    <body>
<?php
    include("includes/nav.php");

    if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"]) && !isset($_GET["not_found"]))
    {
        $tracking_id = $db->real_escape_string(htmlspecialchars($_GET["id"]));
        
        $shipment_result = $db->query("SELECT * FROM shipment WHERE id = '$tracking_id'");
        $shipment = $shipment_result->fetch_array(MYSQLI_ASSOC);

        $package_result = $db->query("SELECT * FROM package WHERE shipment_id = '$tracking_id'");

        // if the id has matched then we have found the item
        // else that doesn't exist
        if($shipment_result->num_rows > 0) 
        {
            // Here we want to set some vars to display 
        }
        else 
        {
            header("Location: tracking.php?id=$tracking_id&not_found");
        }
    }
    elseif($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"]))
    {
        $tracking_id = htmlspecialchars($_GET["id"]);
    }
    elseif($_SERVER["REQUEST_METHOD"] == "GET" && !isset($_GET["id"]) && !isset($_GET["invalid"])) 
    {
        header("Location: tracking.php?invalid");
        exit();
    }
?>

<?php 
        if(isset($_GET["id"]) && !isset($_GET["not_found"])) 
        {
?> 
       
        <div class="columns">
            <!-- we want to display general data about the shipment here -->
            <div class="column is-two-thirds">
                <div class="container">
                    <div class="heading">
                        <h1 class="title">Shipment <?=$tracking_id; ?></h1>
                    </div> 
                    <div class="content">
                        <p><strong>Name</strong>: <?=$shipment["name"]; ?></p>
                        <p><strong>Cost</strong>: <?=$shipment["cost"]; ?></p>
                        <p><strong>Destination</strong>: <?=$shipment["destination"]; ?></p>
                        <p><strong>Source</strong>: <?=$shipment["source"]; ?></p>
                        <p><strong>Customer ID</strong>: <?=$shipment["customer_id"]; ?></p>
                        <p><strong>Dispatched</strong>: <?=$shipment["date_dispatch"]; ?></p>
                        <p><strong>Arrived</strong>: <?=$shipment["date_arrival"]; ?></p>
                    </div>
                </div>
            </div>

            <!-- we want to display the packages in the shipment here -->
            <div class="column">
<?php 
            while ($package = $package_result->fetch_array(MYSQLI_ASSOC)) {
?>
                <article class="message">
                    <div class="message-header">
                        Package ID: <?=$package["id"];?> 
                    </div>
                    <div class="message-body">
                        <p><strong>Description</strong>: <?=$package["description"]; ?></p>
                        <p><strong>Cost</strong>: <?=$package["cost"]; ?></p>
                        <p><strong>Width</strong>: <?=$package["width"]; ?></p>
                        <p><strong>Height</strong>: <?=$package["height"]; ?></p>
                        <p><strong>Depth</strong>: <?=$package["depth"]; ?></p>
                    </div>
                </article>
<?php 
            }
?>
            </div>
        </div>
<?php 
        }
        elseif(isset($_GET["id"]) && isset($_GET["not_found"]))
        {
        http_response_code(404);
?>
        <div class="notification is-warning has-text-centered">
            Shipment with ID <?=$tracking_id; ?> not found!       
        </div>
        
        <div class="container has-text-centered">
            <h1 class="title is-1">Error Code: <?=http_response_code(); ?></h1>
            <h2 class="subtitle is-3">Looks like we couldn't find what you were looking for, sorry about that!</h2>
        </div>
<?php 
        }
        elseif(isset($_GET["invalid"]))
        {
        http_response_code(400);
?>
        <div class="notification is-danger has-text-centered">
            Page not found!       
        </div>

        <div class="container has-text-centered">
            <h1 class="title is-1">Error Code: <?=http_response_code(); ?></h1>
            <h2 class="subtitle is-3">Oops, it seems something went wrong!</h2>
            <h3>Take a look at the address bar to try and fix the problem.</h3>
        </div>
<?php 
        } 
?>

    </body>
</html>
