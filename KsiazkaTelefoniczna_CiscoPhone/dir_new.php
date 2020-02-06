<?php

function CzyscOpis ( $sText ) {
    $aSzukaj = array('ć','Ć','ś','Ś','ą','Ą','ż','Ż','ó','Ó','ł','Ł','ś','Ś','ź','Ź','ń','Ń','ę','Ę');
    $aZamien = array('c','C','s','S','a','A','z','Z','o','O','l','L','s','S','z','Z','n','N','e','E');
    $sText = str_replace($aSzukaj, $aZamien, $sText);

    $sText = preg_replace("@\n|<br/*>@i","",$sText);
    $sText = preg_replace("@[^a-zA-Z0-9\s\_\-\:\.\+\(\)]+@","_",$sText);
    return $sText;
}


$serverName = "";
$userName = "";
$password = "";
$database = "";


$connectionInfo = array( "UID" => $userName, "PWD" => $password, "Database" => $database );
$link = sqlsrv_connect( $serverName, $connectionInfo );
	if( $link ) {
     
	} else{
     echo "Connection could not be established.<br />";
     die( print_r( sqlsrv_errors(), true ) );
		}

header("Content-type: text/xml; charset: utf-8");
header("Connection: close");
header("Expires: -1");

print("<CiscoIPPhoneDirectory>\n");
print("\t<Title>Ksiazka telefoniczna</Title>\n");
print("\t<Prompt>Wyniki wyszukiwania</Prompt>\n");

$letters = preg_replace('@\W@','',@$_GET['letters']);


$sql = "select concat(firstname,' ',surname)  name, phone from users where blocked=0 and phone>0 and (firstname like '$letters' or surname like '$letters%' ) ";

$params = array($letters, $letters);

$stmt = sqlsrv_query( $link, $sql, $params );


while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC ) ) {
  
  print("\t<DirectoryEntry>\n");
  print("\t\t<Name>");
  print(CzyscOpis($row['name']));
  print("</Name>\n");
  print("\t\t<Telephone>");
  print(CzyscOpis($row['phone']));
  print("</Telephone>\n");
  print("\t</DirectoryEntry>\n");
  
}

if( $stmt === false ) {
  die( print_r( sqlsrv_errors(), true));
}

print("</CiscoIPPhoneDirectory>\n");

?>