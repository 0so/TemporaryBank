<?
require_once('/home/sterlid2/Private/userbase.php');

/* Force https connection */
forceHTTPS();

/* Check if the user is logged in already */
session_start();
if(checkIfLoggedIn()) {
    header("Location: home.php");
    die();
}

/* Check if there is a key */
if (!isset($_SESSION['key'])) {
    $_SESSION['key'] = bin2hex(random_bytes(32));
}

/* Signin form csrf token */
$signinToken = hash_hmac('sha256', '/authenticateSignin.php', $_SESSION['key']); 

/* Check if the user can been timed out (and redirected here [signin.php]) */
$timeout = false;
if (isset($_GET['timeout'])) {
    $timeout = (bool)$_GET['timeout']; // Get timeout value
};
?>

<!DOCTYPE html>
<html lang="en-US">
    <head>
    	<title>Sign In</title>
    	<!-- Stylesheet -->
    	<link rel="stylesheet" href="CSS/stylesheet.css">
    	<!-- Favicon -->
	<link rel="icon" href="Images/logo.ico">
    	<!-- Google Font -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <!-- Google Font -->
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <!-- Google Font -->
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;1,100&display=swap" rel="stylesheet">
        <!-- Svg Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
        <!-- Different screen size scaling compatability -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="flex-center-item">
            <div class="list fixed-sub round">
                <button id="notification" onClick="hideNotification()" class="notification max failure transform-button round <? if (!$timeout) echo "collapse" ?>">
                    <p><i id="notification-icon" class="fas fa-times icon"></i><span id="notification-text"><? if ($timeout) echo "Signed Out Due to Inactivity" ?></span></p>
                    <div class="split">
                           <div class="toggle-button">
            	            <i class="fas fa-times"></i>
            	        </div>
                    </div>
                </button>
                <br>
                <div class="accent-border top-round">
                    <h2 class="big"><b>Goldman Stacks</b><h2>
                </div>
                <br>
                <form id="login">
                    <label for="username" class="info">Username</label>
    	            <div class="form-item">
    		            <input id="username" class="input-field" name="username" type="text" required>
    	            </div>
    	            <label for="password" class="info">Password</label>
    	            <div class="form-item">
    		            <input id="password" class="input-field" name="password" type="password" required>
    	            </div>
    	            <input type="hidden" name="token" value="<? echo $signinToken ?>">
                    <a href="register.php" class="highlight-button transform-button split round">
                        <div class="list">
                            <p><i class="fas fa-info icon"></i> Don't have an account? Register here</p>
                        </div>
                        <div class="animate-left">
            	            <div class="toggle-button">
            	                <i class="fas fa-chevron-right"></i>
            	            </div>
                        </div>
                    </a>
                    <div class="form-item">
                        <button type="submit" class="standard-button transform-button flex-center round">
                            <div class="split">
                                <p class="animate-left">Sign In<p>
               		        <div class="toggle-button">
                		    <i class="fas fa-chevron-right"></i>
                		</div>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </body>
	<script type="text/javascript" src="Scripts/notification.js"></script>
	<script type="text/javascript">
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('login').addEventListener('submit', handleForm);
        });
        
        function handleForm(event) {
	        event.preventDefault();
	        
	        let form = event.target;
	        let formData = new FormData(form);
	        
	        let url = "requests/authenticateSignin.php";
	        let request = new Request(url, {
	            body: formData,
	            method: 'POST',
	        });
	        
	        fetch(request)
	            .then((response) => response.json())
	            .then((data) => {
			showNotification();

	                if (data.response) {
	                    setSuccessNotification(data.message);
	                    window.location.href = "home.php";
	                } else {
	                    setFailNotification(data.message);
	                }
	            })
	            .catch(console.warn);
	    }
    </script>
</html>
