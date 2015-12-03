<?php
/**
 * Handles the registration process: Registers and verifies the acceptance of a new
 * user, or redirects her/him back to the registration page if an error occurs.
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

require_once "includes/main.php";

// If the variable “submitted” was sent in the form,
if (isset($_POST["submitted"])) {
    // and, it’s equal to 1, meaning that it was actually submitted,
    if (1 == $_POST["submitted"]) {
        // and, every variable that is part of the form was actually received by
        // this file, meaning that the form was not hi- or side-jacked,
        if (whiteList()) {
            // and, both the username and password contain at least one character
            // (this is a redundancy check, since each form variable is marked
            // as “required” in the HTML form),
            if (0 < strlen($_POST['username']) && 0 < strlen($_POST['password'])) {
                // then process the username and password.

                // 1. Remove whitespace surrounding the username.
                // 2. Convert <, >, ', and " to their respective HTML entities
                // 3. Handle HTML5 code
                // 4. Use the UTF-8 character set
                $username = htmlentities(
                    trim($_POST['username']), ENT_QUOTES | 'ENT_HTML5', "UTF-8"
                );

                $password = trim($_POST['password']);

                if (!doesUserExist($username)) {

                    registerNewUser($username, $password);

                    include_once "includes/register_success.inc";

                    header("Refresh: 5; ./index.php?action=login");  //change file path to login page!
                } else {
                    header("Location: error.php?message_type=registration_error");
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
  <link rel="stylesheet" type="text/css" href="css/register.css">
  <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
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
            <p><input type="email" 
                 name="email address"
                 placeholder="email address"></p>
            <p><input type="text" 
                 name="location"
                 placeholder="location"></p>

          <p><input type="hidden" name="submitted" value="1"></p>
          <p><input class="button" type="submit" value="<?php echo $button_value; ?>"></p>
      </form>
    </div>

    <div class="register">
      <h6>Already a Member?</h6>
      <a href="login.php">Sign In</a>
    </div>

  <script src="js/register.js"></script>
</body>
</html>