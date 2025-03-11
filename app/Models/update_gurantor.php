
<?php
//$conn = mysqli_connect('localhost', 'networkemr', 'GHmarghf41254','l_optl');
$servername = "3.99.223.115";
$username = "user_labalhadi";
$password = "ZGdkvq0tZ6XGLsKN";
$dbname = "l_labalhadi";
//$columnName = "patient_id";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if (!$conn) {
  die("Connection failed: ");
}

$sqlReq = "SELECT * FROM `gurantors`";

$result =mysqli_query($conn,$sqlReq);

while ($row = mysqli_fetch_array($result)){
$id =  trim($row['GID']);
$full_name=str_replace("'", "\'", $row['name']);
$category = str_replace("'", "\'", $row['type']);
$full_address = str_replace("'", "\'", $row['address']);
$telephone = str_replace("'", "\'", $row['phone']);
$remarks = str_replace("'", "\'", $row['Contact']);
$email = str_replace("'", "\'", $row['email']);
$FinancialNumber=str_replace("'", "\'", $row['FinancialNumber']);

 $sqlReqi ="INSERT INTO tbl_external_labs(id,full_name,category, full_address, telephone,remarks, email,FinancialNumber,
 status)
 values(NULLIF('".$id."',''),NULLIF('".$full_name."',''),NULLIF('".$category."',''),
 NULLIF('".$full_address."',''),NULLIF('".$telephone."',''),NULLIF('".$remarks."',''),
 NULLIF('".$email."',''),NULLIF('".$FinancialNumber."',''),'A')";

echo $sqlReqi;	
mysqli_query($conn,$sqlReqi);

		
}
	echo "done";
?>