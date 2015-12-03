<?php
/**
 * Contains the define mappings for the database credentials listed in the file
 * “setup.sql,” which is in the “mysql” folder in the root of this project
 * directory.
 *
 * TODO: Implement $base_url across project.
 *
 * PHP version 5.3.28
 *
 * @category Web_App
 * @package  Web_App
 * @author   Roy Vanegas <roy@thecodeeducators.com>
 * @license  https://gnu.org/licenses/gpl.html GNU General Public License
 * @link     https://bitbucket.org/code-warrior/web-app/
 */

define("DBNAME", "web_app");
define("DBHOST", "localhost");
define("DBUSER", "the_user");
define("DBPASS", "the_password");

$base_url = dirname(__FILE__);
