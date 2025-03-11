<?php
$url = "https://www4.parte.ramq.gouv.qc.ca/RAT/HL/HLB_RecvrDemAT/HLB2_ServRecvrDem_svc/ServRecvrDem2.svc?wsdl";
$username = "AIR0223AA";
$password = "Elissar2023a";

// Path to your XML file
//$xmlFile = public_path('file.xml');  // Use public_path to get the correct public directory path
$xmlFile = config('app.url').'/public/file.xml';

// Read the XML content
$xmlContent = file_get_contents($xmlFile);
//echo $xmlFile;
$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, $url); 
curl_setopt($ch, CURLOPT_USERPWD, $username.':'.$password);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($ch, CURLOPT_TIMEOUT, 4); 
curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlContent); 
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: close'));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

$result = curl_exec($ch); 

//Check for errors ( again optional )
if ( curl_errno($ch) ) {
    $result = 'ERROR -> ' . curl_errno($ch) . ': ' . curl_error($ch);
} else {
    $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    switch($returnCode){
        case 200:
            break;
        default:
            $result = 'HTTP ERROR -> ' . $returnCode;
            break;
    }
}

//Output the results
 var_dump($result); 
// var_dump(curl_getinfo($ch));

//Close the handle
curl_close($ch);
 



/*$options = [
    CURLOPT_URL => $url,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $xmlContent,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
       'Content-Type: application/xml', // Set the content type to XML
        'Content-Length: ' . strlen($xmlContent), // Set the content length
    ],
    CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
    CURLOPT_USERPWD => $username.':'.$password,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_SSL_VERIFYPEER => false,
];

$curl = curl_init();
curl_setopt_array($curl, $options);

$response = curl_exec($curl);

if (curl_errno($curl)) {
    echo 'Error: ' . curl_error($curl);
} else {
    var_dump($response);
}

curl_close($curl);*/
?>
