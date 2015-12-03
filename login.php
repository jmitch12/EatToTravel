<?php
/**
 * Handles the login process: Verifies, then logs in, a valid user, or throws an
 * error if login credentials are faulty.
 *
 * TODO: handle the error situations in the else clauses.
 *
 * PHP version 5.3.28
 *
 * @category Web_App
 * @package  Web_App
 * @author   Roy Vanegas <roy@thecodeeducators.com>
 * @license  https://gnu.org/licenses/gpl.html GNU General Public License
 * @link     https://bitbucket.org/code-warrior/web-app/
 */

session_start();

require_once "includes/main.php";

if (isset($_POST["submitted"])) {
    if (1 == $_POST["submitted"]) {
        if (whiteList()) {
            if (0 < strlen($_POST['username']) && 0 < strlen($_POST['password'])) {
                $username = htmlentities(
                    trim($_POST['username']), ENT_QUOTES | 'ENT_HTML5', "UTF-8"
                );
                $password = trim($_POST['password']);

                if (authenticateUser($username, $password)) {
                    $_SESSION['valid'] = true;
                    $_SESSION['username'] = $username;
                    header("Location: home.php");
                } else {
                    header("Location: error.php?message_type=login_error");
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Eat to Travel</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/login.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">  <!-- bootstrap css library for responsive design -->
</head>
<body background="image/fancyplate.jpg" width="100%"> 
    <div id="head">
        <p><a href="index.php"><img src="image/logo.png"></a></p>
    </div>

    <div class="login">
        <form action="<?php echo $action_value; ?>" method="post">
            <p><input type="text"
                   class="login"
                   name="username"
                   placeholder="username"
                   required
                   autofocus></p>
            <p><input type="password"
                   class="login"
                   name="password"
                   placeholder="password" required></p>
            <p><input type="hidden" name="submitted" value="1"></p>
            <p><input class="button" type="submit" value="<?php echo $button_value; ?>"></p>
        </form>
    </div>
    <div class="register">
        <h6>Don't have an account?</h6>
        <a href="register.php">Sign Up</a>
    </div>

    <script src="js/index.js"></script>
    <script src="js/jquery-2.1.4.min.js"></script>
</body>
</html>