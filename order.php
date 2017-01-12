<?php
    session_start();
    if (!isset($_SESSION['email'])) {
        header("Location: login.php");
        exit();
    }

    require('config.php');
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // echo(print_r($_POST));
        $description = $_POST['description'];
        $packages = $_POST['packages'];
        $shippingtype = $_POST['shippingtype'];
        $cost = $_POST['cost'];
        $destination = $_POST['destination'];
        $email = $_SESSION['email'];

        $db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        if ($db->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $mysqli->db. ") " . $mysqli->db;
        }
        else
        {
            // $query = $db->prepare("INSERT INTO `user` (email,password,privilege_level) VALUES (?,?,1)");
            // $query->bind_param("ss", $email, $passwd);

            // $query->execute();

            // $query->close();
            $result = $db->query("SELECT id FROM user WHERE email = '$email';");
            if ($result) {
                if ($result->num_rows > 0)
                {
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $id = $row["id"];
                    // Why prepare statements when you can cripple horrendously?
                    $db->query("INSERT INTO `shipment` (name, cost, destination, customer_id, date_dispatch) VALUES('$description', '$cost', '$destination', '$id', NOW())");
                    $shipment_id = $db->insert_id;
                    foreach ($packages as $pkg) {
                        if ($pkg['cost'] === 0) {
                            continue;
                        }
                        $query = <<<SQL
                            INSERT INTO `package`
                            (description, cost, width, height, depth, weight, shipment_id)
                            VALUES (
                                "{$pkg['name']}",
                                "{$pkg['cost']}",
                                "{$pkg['height']}",
                                "{$pkg['width']}",
                                "{$pkg['depth']}",
                                "{$pkg['weight']}",
                                '$shipment_id'
                            );
SQL;
                        $db->query($query);
                    }
                    header("Location: index.php?created");
                }
                else
                {
                    header("Location: login.php?invalid");
                }
            }
            else
            {
                header("Location: login.php?invalid");
            }

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
        <?php include("includes/nav.php") ?>


        <section class="hero is-primary is-bold">
            <div class="container">
            </div>
            <div class="hero-body">
                <div class="container">
                    <h1 class="title is-1">
                        Request A Delivery
                    </h1>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="content">
                    <h1 class="title">We'll need a few details first.</h1>
                    <div class="columns">
                        <div class="column is-8 is-offset-2">
                            <form method="post">
                                <label class="label">Description</label>
                                <p class="control">
                                    <input class="input" type="text" name="description" />
                                </p>

                                <label class="label">Packages</label>
                                <div class="box">
                                    <table class="table" id="pkgtable">
                                        <thead>
                                            <th>Name</th>
                                            <th>Width (cm)</th>
                                            <th>Height (cm)</th>
                                            <th>Depth (cm)</th>
                                            <th>Weight (g)</th>
                                            <th>Cost (£)</th>
                                        </thead>
                                        <tbody id="pkglist">
                                        </tbody>
                                    </table>
                                    <div id="nopackageswarning" class="notification">This shipment currently has no packages!</div>

                                    <div class="columns">
                                        <div class="column is-8">
                                            <label class="label">Name</label>
                                            <p class="control">
                                                <input class="input" type="text" id="pkgname" />
                                            </p>
                                            <label class="label">Width, Height, Depth (cm)</label>
                                            <div class="control is-grouped">
                                                <p class="control is-expanded">
                                                    <input class="input" type="text" id="pkgwidth" placeholder="Width"/>
                                                </p>
                                                <p class="control is-expanded">
                                                    <input class="input" type="text" id="pkgheight" placeholder="Height"/>
                                                </p>
                                                <p class="control is-expanded">
                                                    <input class="input" type="text" id="pkgdepth" placeholder="Depth"/>
                                                </p>
                                            </div>

                                            <label class="label">Weight (g)</label>
                                            <p class="control">
                                                <input class="input" type="text" id="pkgweight" />
                                            </p>
                                        </div>
                                        <div class="column is-4">
                                            <label class="label">Cost</label>
                                            <p class="control">
                                                <input class="input" disabled type="text" id="pkgcost" value="00.00"/>
                                            </p>
                                            <p>Cost = (width + height + depth) x £0.005 x weight.</p>
                                            <button class="button title is-4 is-primary" type="button" id="pkgadd">Add</button>
                                            <button class="button title is-4 is-danger" type="button" id="pkgclear">Clear</button>
                                        </div>
                                    </div>
                                </div> <!-- end of packages -->

                                <label class="label">Shipping Type</label>
                                <p class="control">
                                    <span class="select">
                                        <select name="shippingtype">
                                            <option value="5.00" selected="selected">Standard (+£5.00)</option>
                                            <option value="8.00">Express (+£8.00)</option>
                                            <option value="16.00">Same Day (+£16.00)</option>
                                        </select>
                                    </span>
                                </p>

                                <label class="label">Destination Address</label>
                                <p class="control">
                                    <input type="text" name="destination" class="input" />
                                </p>

                                <label class="label">Total cost (£)</label>
                                <h1 class="subtitle is-2" id="costlabel">£5.00</h1>
                                <input class="input subtitle is-2" name="cost" value="5.00" type="hidden"/>

                                <div class="control is-grouped">
                                    <p class="control">
                                        <button class="button is-large is-primary">Submit</button>
                                    </p>

                                    <p class="control">
                                        <a class="button is-large is-link" href="/">Cancel</a>
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js"
        integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
        crossorigin="anonymous"></script>
        <script>

        $.fn.reduce = [].reduce;

        var nopackageswarning = '<div id="nopackageswarning" class="notification">This shipment currently has no packages!</div>';

        var clearPkg = function () {
            $('input')
                .filter(function () {
                    return this.id.match(/^pkg/);
                })
                .each(function (i) {
                    $(this).val("");
                })
        };

        var updatePkgCost = function() {
            var width = $('#pkgwidth').val()? parseInt($('#pkgwidth').val(),10) : 0;
            var height = $('#pkgheight').val()? parseInt($('#pkgheight').val(),10) : 0;
            var depth = $('#pkgdepth').val()? parseInt($('#pkgdepth').val(),10) : 0;
            var weight = $('#pkgweight').val()? parseInt($('#pkgweight').val(),10) : 5;

            $('#pkgcost').val((0.005 * (width + height + depth + weight)).toFixed(2).toString());
        }

        var pkgcount = 0;

        $('#pkgwidth').keyup(updatePkgCost);
        $('#pkgheight').keyup(updatePkgCost);
        $('#pkgdepth').keyup(updatePkgCost);
        $('#pkgweight').keyup(updatePkgCost);

        var updatePkgs = function () {
            if (pkgcount >= 0) {
                $('#nopackageswarning').remove();
            }
            else if (!$('#nopackageswarning').length) {
                $('#pkgtable').append(nopackageswarning);
            }
        };

        var updateCost = function () {
            var cost = $('input')
                .filter(function () {
                    return ($(this).attr("name") && $(this).attr("name").match(/^packages\[\d+\]\[cost\]$/));
                })
                .map(function () {
                    return $(this).val()? parseFloat($(this).val(), 10) : 0;
                })
                .reduce(function (a,b) {
                    return a + (Math.round(b*100) / 100);
                },0);

            cost += parseFloat($('select[name="shippingtype"]').val());

            $('input[name="cost"]').val(cost.toFixed(2));
            $('#costlabel').html("£" + cost.toFixed(2));
        };

        $('select[name="shippingtype"]').change(updateCost);

        $('#pkgadd').click(function () {
            var name = $('#pkgname').val();
            var width = $('#pkgwidth').val();
            var height = $('#pkgheight').val();
            var depth = $('#pkgdepth').val();
            var weight = $('#pkgweight').val();
            var cost = $('#pkgcost').val();

            $('#pkglist').append('<tr data-index="'+pkgcount+'">');
                $('#pkglist tr[data-index="'+pkgcount+'"]').append("<td>"+name+"</td>");
                $('#pkglist tr[data-index="'+pkgcount+'"]').append("<td>"+width+"</td>");
                $('#pkglist tr[data-index="'+pkgcount+'"]').append("<td>"+height+"</td>");
                $('#pkglist tr[data-index="'+pkgcount+'"]').append("<td>"+depth+"</td>");
                $('#pkglist tr[data-index="'+pkgcount+'"]').append("<td>"+weight+"</td>");
                $('#pkglist tr[data-index="'+pkgcount+'"]').append("<td>"+cost+"</td>");
                $('#pkglist tr[data-index="'+pkgcount+'"]').append('<input type="hidden" name="packages['+pkgcount+'][name]" value="'+name+'" />');
                $('#pkglist tr[data-index="'+pkgcount+'"]').append('<input type="hidden" name="packages['+pkgcount+'][width]" value="'+width+'" />');
                $('#pkglist tr[data-index="'+pkgcount+'"]').append('<input type="hidden" name="packages['+pkgcount+'][height]" value="'+height+'" />');
                $('#pkglist tr[data-index="'+pkgcount+'"]').append('<input type="hidden" name="packages['+pkgcount+'][depth]" value="'+depth+'" />');
                $('#pkglist tr[data-index="'+pkgcount+'"]').append('<input type="hidden" name="packages['+pkgcount+'][weight]" value="'+weight+'" />');
                $('#pkglist tr[data-index="'+pkgcount+'"]').append('<input type="hidden" name="packages['+pkgcount+'][cost]" value="'+cost+'" />');
            $('#pkglist').append("</tr>");

            pkgcount++;

            clearPkg();
            updatePkgs();
            updateCost();
        });

        $('#pkgclear').click(clearPkg);
        </script>
    </body>
</html>
