<div class="header">
    <nav class="nav">
        <div class="nav-left">
            <div class="nav-item">
                <img src="assets/images/logo.png" alt="Planet Express Logo">
            </div>
        </div>

        <div class="nav-center">
            <div class="nav-item">
                <p class="control has-addons has-icon">
                  <input id="tracking-id-input" class="input is-expanded" placeholder="Enter tracking number">
                  <span class="icon is-small">
                    <i class="fa fa-search"></i>
                  </span>
                    <a id="track-shipment" class="button">
                        <span>Track Delivery</span>
                    </a>
                </p> 
            </div>
        </div>

        <div class="nav-right nav-menu">
            <a class="nav-item" href="index.php">
                Home
            </a>

            <span class="nav-item">
                <a class="button" href="signup.php">
                    <span class="icon">
                        <i class="fa fa-user-plus"></i>
                    </span>
                    <span>Register</span>
                </a>
                <a class="button is-primary" href="login.php">
                    <span class="icon">
                        <i class="fa fa-sign-in"></i>
                    </span>
                    <span>Login</span>
                </a>
            </span>
        </div>
    </nav>
</div>

<script>
document.getElementById("track-shipment").addEventListener("click", function() {
    var tracking_id = document.getElementById("tracking-id-input").value;

    window.location.href = ("tracking.php?id=" + tracking_id);
});
</script>

