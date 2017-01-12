<?php 
    require("config.php");
    session_start();
    if (!isset($_SESSION["email"]))
    {
        header("Location: login.php?not_logged_in");
		// Ensure the script stops loading if the user is not logged in to protect the rest of the page.
		exit();
    }

    $db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    
    if(isset($_SESSION["email"])) 
    {
        $myemail = $_SESSION["email"];

        $result = $db->query("SELECT * FROM user WHERE email = '$myemail'");

        $user = $result->fetch_array(MYSQLI_ASSOC);

        $myid = $user["id"];
        $myfirstname = $user["firstname"];
        $mylastname = $user["lastname"];
        $myprivilege = $user["privilege_level"];
    }
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

    // Admin dashboard 
    //
    //
    if($myprivilege == 2) 
    {
?>
        <section class="hero is-success is-bold">
		</div>
<?php 
    } 
    // User dashboard 
    //
    //
    elseif ($myprivilege == 1) 
    {
?>
        <section class="hero is-info is-bold">
<?php 
    } 
    else 
    {
        unset($_SESSION["email"]);
        header("Location: login.php?not_logged_in");
    }
?>
            <div class="hero-body">
                <div class="container">
                    <h1 class="title is-1">
                        Your Dashboard
                    </h1>
                    <h2 class="subtitle is-3">
                    Welcome to your dashboard, <?=$myfirstname; ?>.
                    </h2>
                </div>
            </div>
        </section>
<?php 
	if($myprivilege == 2) 
	{
?>
	<section class="section">
		<div class="columns">
			<div class="column">
				<div class="card">
				  <div class="card-content">
					<div class="media">
					  <div class="media-content">
						<p class="title is-4"><?=$myfirstname; ?> <?=$mylastname; ?></p>
						<p class="subtitle is-6">
						</p>
					  </div>
					</div>

					<div class="content">
						<span class="icon is-small">
							<i class="fa fa-envelope"></i>
						</span>
						<span><a href="mailto:<?=$myemail; ?>"><?=$myemail; ?></a></span>
						<br>
						<span class="icon is-small">
							<i class="fa fa-unlock-alt"></i>
						</span>
						<span><strong>Admin</strong></span>
						<br>

					</div>
				  </div>
				</div> 
				<br>
				<div class="block">
					<a class="button is-success">
						<span class="icon is-small">
							<i class="fa fa-rocket"></i>
						</span>
						<span>Create Shipment</span>
					</a>
                    <a class="button is-primary" href="dashboard.php?id=<?=$myid; ?>">
                    <span class="icon is-small">
                        <i class="fa fa-cube"></i>
                    </span>
                    <span>View My Shipments</span>
                    </a>
					<a class="button is-warning" href="profile.php">
						<span class="icon is-small">
							<i class="fa fa-key"></i>
						</span>
						<span>Change Details</span>
					</a>
				</div>
			</div>
			<div class="column">
			<h1 class="title">Users</h1>
			<table class="table">
				<thead>
					<tr>
						<th><abbr title="User ID">ID</abbr></th>
						<th><abbr title="First Name">Name</abbr></th>
						<th><abbr title="Last Name">Surname</abbr></th>
						<th><abbr title="Privilege Level">Type</abbr></th>
						<th></th>	
					</tr>
				</thead>
				<tbody>
<?php
        if(isset($_GET["id"]) && isset($_GET["disable"]) && !isset($_GET["enable"]))
        {
            $disable_user_id = $_GET["id"]; 
            $db->query("UPDATE user SET privilege_level=0 WHERE id='$disable_user_id'");
        }
        elseif(isset($_GET["id"]) && !isset($_GET["disable"]) && isset($_GET["enable"]))
        {
            $enable_user_id = $_GET["id"]; 
            $db->query("UPDATE user SET privilege_level=1 WHERE id='$enable_user_id'");
        }

        $user_result = $db->query("SELECT * FROM user");
		while ($user = $user_result->fetch_array(MYSQLI_ASSOC)) 
		{
?>
				<tr>
					<th><?=$user["id"]; ?></th>
					<td><?=$user["firstname"]; ?></td>	
                    <td><?=$user["lastname"]; ?></td>	
<?php 
            if($user["privilege_level"] == 1)
            {
?>
					<th>User</th>
<?php 
            }
            elseif($user["privilege_level"] == 2)
            {
?>
                    <th>Admin</th>
<?php 
            }
            else 
            {
?>
                    <th class="is-danger">Disabled</thead>
<?php 
            }
?>
                    <th>
                        <div class="block">
                        <a class="button is-primary" href="dashboard.php?id=<?=$user["id"]; ?>">
						<span class="icon is-small">
							<i class="fa fa-cube"></i>
						</span>
                        <span>View Shipments</span>
                        </a> 
<?php 
            if($user["privilege_level"] == 1)
            {
?>
                        <a class="button is-danger" href="dashboard.php?id=<?=$user["id"]; ?>&disable">
						<span class="icon is-small">
							<i class="fa fa-ban"></i>
						</span>
                        <span>Disable</span>
                        </a> 
<?php
            }
            elseif($user["privilege_level"] == 2)
            {
?>
                        <a class="button is-danger is-disabled">
						<span class="icon is-small">
							<i class="fa fa-ban"></i>
						</span>
                        <span>Disable</span>
                        </a> 
<?php 
            }
            else 
            {
?>
                        <a class="button is-success" href="dashboard.php?id=<?=$user["id"]; ?>&enable">
						<span class="icon is-small">
							<i class="fa fa-plus-circle"></i>
						</span>
                        <span>Enable</span>
                        </a> 
<?php
            }
?>
                        </block>
                    </th>
				</tr>
<?php
		}
?>
			</tbody>
			</table>
			</div>
            <div class="column">
<?php
        if(isset($_GET["id"]))
        {
            $shipment_user_id = $_GET["id"]; 
            $user_result = $db->query("SELECT * FROM user WHERE id='$shipment_user_id'");
            $viewed_user = $user_result->fetch_array(MYSQLI_ASSOC);
            $shipment_user_name = $viewed_user["firstname"];
            $shipment_user_surname = $viewed_user["lastname"];
?>
        <h1 class="title"><?=$shipment_user_name; ?> <?=$shipment_user_surname; ?>'s Shipments</h1>
<?php
        }
        else 
        {
            $shipment_user_id = $myid;
?>
        <h1 class="title">Your Shipments</h1>
<?php
        }
?>
			<table class="table">
				<thead>
					<tr>
						<th><abbr title="Shipment ID">ID</abbr></th>
						<th><abbr title="Shipment Name">Name</abbr></th>
						<th></th>	
					</tr>
				</thead>
				<tbody>
<?php
        $shipment_result = $db->query("SELECT * FROM shipment WHERE customer_id = '$shipment_user_id'");
		while ($shipment = $shipment_result->fetch_array(MYSQLI_ASSOC)) 
		{
?>
				<tr>
					<th><?=$shipment["id"]; ?></th>
					<td><?=$shipment["name"]; ?></td>	
                    <th><a class="button is-info" href="tracking.php?id=<?=$shipment["id"]; ?>">
						<span class="icon is-small">
							<i class="fa fa-space-shuttle"></i>
						</span>
                        <span>Track</span>
                        </a></th>
				</tr>
<?php
		}
?>
			</div>
		</div>
	</section>
<?php 
	}
	else
	{
?>
	<section class="section">
		<div class="columns">
		  <div class="column is-one-thirds">
				<div class="card">
				  <div class="card-content">
					<div class="media">
					  <div class="media-content">
						<p class="title is-4"><?=$myfirstname; ?> <?=$mylastname; ?></p>
						<p class="subtitle is-6">
						</p>
					  </div>
					</div>

					<div class="content">
						<span class="icon is-small">
							<i class="fa fa-envelope"></i>
						</span>
						<span><a href="mailto:<?=$myemail; ?>"><?=$myemail; ?></a></span>
						<br>
						<span class="icon is-small">
							<i class="fa fa-unlock-alt"></i>
						</span>
						<span><strong>User</strong></span>
						<br>

					</div>
				  </div>
				</div> 
				<br>
				<div class="block">
					<a class="button is-success">
						<span class="icon is-small">
							<i class="fa fa-rocket"></i>
						</span>
						<span>Request Shipment</span>
					</a>
					<a class="button is-warning" href="profile.php">
						<span class="icon is-small">
							<i class="fa fa-key"></i>
						</span>
						<span>Change Details</span>
					</a>
				</div>
		  </div>
		  <div class="column is-two-thirds">
			<h1 class="title">Your Shipments</h1>
			<table class="table">
				<thead>
					<tr>
						<th><abbr title="Shipment ID">ID</abbr></th>
						<th><abbr title="Shipment Name">Name</abbr></th>
						<th></th>	
					</tr>
				</thead>
				<tbody>
<?php
        $shipment_result = $db->query("SELECT * FROM shipment WHERE customer_id = '$myid'");
		while ($shipment = $shipment_result->fetch_array(MYSQLI_ASSOC)) 
		{
?>
				<tr>
					<th><?=$shipment["id"]; ?></th>
					<td><?=$shipment["name"]; ?></td>	
                    <th><a class="button is-info" href="tracking.php?id=<?=$shipment["id"]; ?>">
						<span class="icon is-small">
							<i class="fa fa-space-shuttle"></i>
						</span>
                        <span>Track</span>
                        </a></th>
				</tr>
<?php
		}
?>
			</tbody>
			</table>
		  </div>
		</div>
</section>
<?php 
	}
?>
    </body>
</html>
