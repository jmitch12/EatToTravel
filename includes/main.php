<?php
/**
 * Contains functions used for registering new users, authenticating the login
 * protocol for existing users, and working with files.
 *
 * PHP version 5.3.28
 *
 * @category Web_App
 * @package  Web_App
 * @author   Roy Vanegas <roy@thecodeeducators.com>
 * @license  https://gnu.org/licenses/gpl.html GNU General Public License
 * @link     https://bitbucket.org/code-warrior/web-app/
 */

/**
 * WHITELIST
 *
 * Returns true if this white list verifies its predefined variables against a
 * $_POST variables’ form values, or, false otherwise.
 *
 * @access public
 * @return Boolean representing the valid or invalid nature of this white list
 */
function whiteList()
{
    /**
     * $validated maintains the state of this function. It’s set to true if the
     * conditions validating the following white list are true.
     */
    $validated = false;

    /**
     * $white_list represents the names of items that must be matched by HTML form
     * name values. Note: If you’re using an image as a submit button (<input
     * type="image">, then you must remember to add “x” and “y” to the
     * following array.
     */
    $white_list = array("username", "password", "submitted"); /**edit the array to use it for personal website*/

    /**
     * $amount_of_form_input_names_found_in_white_list keeps a count of items in the
     * white list array (defined above) that are also found in the $_POST array.
     */
    $amount_of_form_input_names_found_in_white_list = 0;

    if (isset($_POST)) {
        /**
         * Cycle through every item in the $_POST array so I can inspect every $key
         * for a match in the $white_list.
         */
        foreach ($_POST as $key => $value) {
            /**
             * Perform a case-sensitive test of whether $key is found in $white_list.
             * (A $key is the string-based index in the $_POST associative array. For
             * example, the $key in $_POST["username"] would be "username."
             * Similarly, the $key in $_POST["submitted"] would be "submitted.") If
             * it is, increase $amount_of_form_input_names_found_in_white_list.
             */
            if (in_array($key, $white_list)) {
                $amount_of_form_input_names_found_in_white_list++;
            }
        }

        // Compare the internal white list count to the external form variable count
        if (count($white_list) == count($_POST)) {
            // Compare the external form count to the external form variable count
            if ($amount_of_form_input_names_found_in_white_list == count($_POST)) {
                // Ensure that the external form variable count isn’t empty
                if ($amount_of_form_input_names_found_in_white_list != 0 ) {
                    $validated = true;
                }
            }
        }
    }

    return $validated;
}

/**
 * DOES USER EXIST
 *
 * Checks the database for the existence of a $username in the table “user.”
 * Returns true on success, false otherwise.
 *
 * @param String $username is the string being checked in the user table.
 *
 * @access public
 * @return Boolean true returned if $username is found; false otherwise.
 */
function doesUserExist($username)
{
    try {
        include_once "config.php";

        $user_exists = false;

        $db = new PDO(
            "mysql:host=".DBHOST."; dbname=".DBNAME, DBUSER, DBPASS
        );

        $statement = $db -> prepare(
            "SELECT username FROM user WHERE username = :username"
        );

        $statement -> execute(array('username' => $username));

        while ($row = $statement -> fetch()) {
            if ($row['username'] == $username) {
                $user_exists = true;

                break;
            }
        }

        $statement = null;

        return $user_exists;

    } catch( PDOException $e ) {
        echo "<div>Error thrown in function <code>doesUserExist</code></div>";
        echo $e -> getMessage();

        exit;
    }
}

/**
 * REGISTER NEW USER
 *
 * Inserts $username and $password into the user table, generating a random salt for
 * the password in the interim.
 *
 * @param String $username of a new user that should not exceed the limit established
 *                         for this field in mysql/user.sql.
 * @param String $password of a new password that should not exceed the limit
 *                         established for this field in mysql/user.sql.
 *
 * @access public
 * @return void
 */
function registerNewUser($username, $password)
{
    try {
        include_once "config.php";

        $salt = substr(md5(rand(0, 65536)), 0, 8);

        $connection = new PDO(
            "mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS
        );

        $statement = $connection -> prepare(
            "INSERT INTO user (username,salt,password) " .
            "VALUES (:username,:salt,:password)"
        );

        $statement -> execute(
            array(
                'username' => $username,
                'salt'     => $salt,
                'password' => md5($password . $salt)
            )
        );

        $statement = null;
    } catch(PDOException $e) {
        echo "<div>Error thrown in <code>registerNewUser</code></div>";
        echo $e -> getMessage();

        exit;
    }
}

/**
 * AUTHENTICATE USER
 *
 * Checks the user table for the existence of a $username with a matching $password.
 * To do this, the $username’s salt must be retrieved so that the md5 function can
 * carry out its comparison. If the comparison is successful, then true is returned;
 * otherwise, false is returned.
 *
 * @param String $username is a user’s existing username.
 * @param String $password is the password associated with the $username.
 *
 * @access public
 * @return bool
 */
function authenticateUser($username, $password)
{
    try {
        include_once "config.php";

        $db = new PDO(
            "mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS
        );

        $statement = $db -> prepare(
            "SELECT password, salt " .
            "FROM user " .
            "WHERE username=:username"
        );

        $statement -> execute(array('username' => $username));

        $row = $statement -> fetch();

        $statement = null;

        if (md5($password . $row['salt']) == $row['password']) {
            $state = true;
        } else {
            $state = false;
        }

        return $state;

    } catch(PDOException $e) {
        echo "<div>Error thrown in <code>authenticateUser</code></div>";
        echo $e -> getMessage();

        exit;
    }
}

/**
 * INSERT NEW FILE
 *
 * Inserts into the file table a file $name with a MIME $type residing at a certain
 * $path uploaded by $username.
 *
 * @param String $username is the owner of the file.
 * @param String $path     is the path to the folder containing the file.
 * @param String $name     is the filename.
 * @param String $type     is the MIME type of the file.
 *
 * @access pubic
 * @return void
 */
function insertNewFile($username, $path, $name, $type)
{
    try {
        include_once "config.php";

        $connection = new PDO(
            "mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS
        );

        $statement = $connection -> prepare(
            "INSERT INTO file (username,path,name,format) " .
            "VALUES (:username,:path,:name,:format)"
        );

        $statement -> execute(
            array(
                'username' => $username,
                'path'     => $path,
                'name'     => $name,
                'format'   => $type
            )
        );

        $statement = null;
    } catch(PDOException $e) {
        echo "<div>Error thrown in <code>insertNewFile</code></div>";
        echo $e -> getMessage();

        exit;
    }
}

/**
 * GET ALL FILE LINKS FOR
 *
 * Returns an array of file names uploaded by $username.
 *
 * @param String $username of user for whom file names are being retrieved.
 *
 * @access public
 * @return array of name fields from file table representing file names.
 */
function getAllFileLinksFor($username)
{
    try {
        include_once "config.php";

        $db = new PDO(
            "mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS
        );

        $statement = $db -> prepare(
            "SELECT name FROM file WHERE username = :username"
        );

        $statement -> execute(array('username' => $username));

        $index = 0;

        while ($row = $statement -> fetch()) {
            $links[$index++] = $row['name'];
        }

        $statement = null;

        if (!isset($links)) {
            $links = 0;
        }

        return $links;

    } catch(PDOException $e) {
        echo "<div>Error thrown in <code>getAllFileLinksFor</code></div>";
        echo $e -> getMessage();

        exit;
    }
}

/**
 * DELETE FILE
 *
 * This function simply calls PHP’s unlink function
 * (http://php.net/manual/en/function.unlink.php) and is not implemented in this
 * project. However, it’s included here to get students going with the concept of
 * deleting a file from the server.
 *
 * @param String $filename is the name of the file that should be removed.
 *
 * @access public
 * @return void
 */
function deleteFile($filename)
{
    unlink($filename);
}
