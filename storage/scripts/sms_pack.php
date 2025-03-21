<?php
  ini_set('display_errors', 1);
   ini_set('display_startup_errors', 1);
   error_reporting(E_ALL);

   date_default_timezone_set("America/New_York");

   require('/var/www/source-emr-labalhadi/vendor/autoload.php');
  
	 $dot = Dotenv\Dotenv::createImmutable('/var/www/source-emr-labalhadi/'); //Location of .env
	 $dot->load(); //Load the configuration (Not override, for override use overload() method
	 $dbHost = env('DB_HOST');
	 $dbUser = env('DB_USERNAME');
	 $dbPass = env('DB_PASSWORD');
	 $dbName = env('DB_DATABASE');
  
    $conn =  mysqli_connect($dbHost, $dbUser , $dbPass,$dbName);

   if (!$conn) {
	  die("Connection failed: " . mysqli_connect_error());
	}
	
  //get sms packages of each clinic
  
  $sms_query="select id,user_num,clinic_num,old_sms_pack,current_sms_pack,clinic_num,is_paid,pay_pack 
              from tbl_clinic_sms_pack where active='O'";
  
  $result_sms=mysqli_query($conn,$sms_query);
  
  while($row=$result_sms->fetch_assoc()){
	$fixed_pack = 140;
    $old_pack = $row['old_sms_pack'];
    $pay_pack = $row['pay_pack'];
	$id = $row['id'];
	$clinic_num = $row['clinic_num'];
	$user_num = $row['user_num'];
   	
	$query="update tbl_clinic_sms_pack set active = 'N' where id = '".$id."' ";
	mysqli_query($conn,$query);
    $query="insert into tbl_clinic_sms_pack(user_num,clinic_num,old_sms_pack,current_sms_pack,pay_pack,active)
	         values('".$user_num."','".$clinic_num."','".$old_pack."','".$fixed_pack."','".$pay_pack."','O')";
	mysqli_query($conn,$query);
    echo "update_success";
  }
	
mysqli_close($conn);	

?>