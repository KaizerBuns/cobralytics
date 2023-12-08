<?
chdir(dirname(__FILE__));
$application="admin";
$disable_cache=true;
error_reporting(E_ALL);
require_once('../config/config.php');
require_once(ROOT_PATH.'/config/includes.php');
require_once(ROOT_PATH.'/system/utils/functions.php');

$user = User::find(array('id' => 1));
EmailHelper::email_confirm($user);
?>