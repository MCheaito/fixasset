<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
error_reporting(E_ALL); ini_set('display_errors', '1');
date_default_timezone_set("America/New_York");
require('/var/www/source-emr-fastmed/vendor/autoload.php');
$dot = Dotenv\Dotenv::createImmutable('/var/www/source-emr-fastmed/'); //Location of .env
$dot->load(); //Load the configuration (Not override, for override use overload() method

/**
 * A simple function that uses mtime to delete files older than a given age (in seconds)
 * Very handy to rotate backup or log files, for example...
 * 
 * $dir String whhere the files are
 * $max_age Int in seconds
 * return String[] the list of deleted files
 */

function delete_older_than($dir, $max_age) {
  $list = array();
  
  $limit = time() - $max_age;
  
  $dir = realpath($dir);
  
  if (!is_dir($dir)) {
    return;
  }
  
  $dh = opendir($dir);
  if ($dh === false) {
    return;
  }
  
  while (($file = readdir($dh)) !== false) {
    $file = $dir . '/' . $file;
    if (!is_file($file)) {
      continue;
    }
    
    if (filemtime($file) < $limit) {
      $list[] = $file;
      unlink($file);
    }
    
  }
  closedir($dh);
  return $list;

}


//Enter your database information here and the name of the backup file
$mysqlDatabaseName =env('DB_DATABASE');
$mysqlUserName =env('DB_USERNAME');
$mysqlPassword = env('DB_PASSWORD');
$mysqlHostName =env('DB_HOST');
$dtz=new DateTimeZone("America/New_York");
$dt = new DateTime("now", $dtz);
$currentTime = $dt->format("Y-m-d") . "T" . $dt->format("H:i:s");
$dir='/var/www/source-emr-fastmed/storage/app/7mJ~33/backups_fastmed/';
// Delete backups older than 7 days
$deleted = delete_older_than($dir, 3600*24*6);
$mysqlExportPath = $dir.'bk_fastmed_'.$currentTime.'.sql';

//Please do not change the following points
//Export of the database and output of the status
$command='sudo mysqldump --no-tablespaces --opt -h'.$mysqlHostName .' -u'.$mysqlUserName .' -p'.$mysqlPassword .' '.$mysqlDatabaseName .' > '.$mysqlExportPath;
exec($command,$output,$worked);
switch($worked){
case 0:
echo 'The database <b>' .$mysqlDatabaseName .'</b> was successfully stored in the following path '.getcwd().'/' .$mysqlExportPath .'</b>';
break;
case 1:
echo 'An error occurred when exporting <b>' .$mysqlDatabaseName .'</b> '.getcwd().'/' .$mysqlExportPath .'</b>';
break;
case 2:
echo 'An export error has occurred, please check the following information: <br/><br/><table><tr><td>MySQL Database Name:</td><td><b>' .$mysqlDatabaseName .'</b></td></tr><tr><td>MySQL User Name:</td><td><b>' .$mysqlUserName .'</b></td></tr><tr><td>MySQL Password:</td><td><b>NOTSHOWN</b></td></tr><tr><td>MySQL Host Name:</td><td><b>' .$mysqlHostName .'</b></td></tr></table>';
break;
}
?>
