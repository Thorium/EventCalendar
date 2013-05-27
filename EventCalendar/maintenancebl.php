<?php
 header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
 header('Expires: '. gmdate("D, d M Y H:i:s") , ' GMT');
 header("Cache-Control: no-cache, must-revalidate");
 header("Pragma: no-cache");

// Save this file to some other non-so-available location!
// Tallenna tämä tiedosto jonnekin mihin kaikilla ei ole pääsyä!

//Debug:
ini_set('display_errors', 'On');

//Alustetaan sivu:
require 'dataaccess.php';
$DA = new Data_Access;
$DA->openDatabase();

if(isset($_POST["addEventAction"])){ //Uusi kohde
    $sql1="insert into EventCalendar(Title, Visible, EventType, DateYear, DateMonth, DateDay, DateHour, DateMinute, City, StreetAddress, LocationDetails, EventDetails, Latitude, Longitude) values ('";
    $sql2= str_replace("'","''", $_POST["Title"])."',0,0,".intval($_POST["DateYear"]).",".intval($_POST["DateMonth"]).",".intval($_POST["DateDay"]).",".intval($_POST["DateHour"]).",".intval($_POST["DateMinute"]).",'".str_replace("'","''", $_POST["City"])."','".str_replace("'","''", $_POST["StreetAddress"])."','".str_replace("'","''", $_POST["LocationDetails"])."','".str_replace("'","''", $_POST["EventDetails"])."','".str_replace("'","''", $_POST["uLatitude"])."','".str_replace("'","''", $_POST["uLongitude"])."')";
    $DA->insertRow($sql1 . $sql2);
}
if(isset($_POST["addInfoAction"])){ //Uusi kohde
    $sql="insert into EventCalendar(Title, Visible, EventType, EventDetails) values ('".str_replace("'","''", $_POST["Title"])."',0,1,'".str_replace("'","''", $_POST["EventDetails"])."')";
    $DA->insertRow($sql);
}


if(isset($_POST["deleteId"])){ //Poistetaan kohde
    $sql="delete from EventCalendar where Id=".intval($_POST["deleteId"]);
    $DA->executeSQL($sql);
}

if(isset($_POST["deleteContactId"])){ //Poistetaan yhteystieto
    $sql="delete from ContactRequest where Id=".intval($_POST["deleteContactId"]);
    $DA->executeSQL($sql);
}

if(isset($_POST["updateAll"])){ //Tallennetaan kohde
    //echo "tallennus";    
    $j=0;
    while(isset($_POST["targetId".$j])){
        if(isset($_POST["Title".$j])){
            $sql="update EventCalendar set Title='".str_replace("'","''", $_POST["Title".$j])."', Visible=".intval($_POST["Visible".$j]).", DateYear=".intval($_POST["DateYear".$j]).", DateMonth=".intval($_POST["DateMonth".$j]).", DateDay=".intval($_POST["DateDay".$j]).", DateHour=".intval($_POST["DateHour".$j]).", DateMinute=".intval($_POST["DateMinute".$j]).", City='".str_replace("'","''", $_POST["City".$j])."', StreetAddress='".str_replace("'","''", $_POST["StreetAddress".$j])."', LocationDetails='".str_replace("'","''", $_POST["LocationDetails".$j])."', EventDetails='".str_replace("'","''", $_POST["EventDetails".$j])."', Latitude='".str_replace("'","''", $_POST["uLatitude".$j])."', Longitude='".str_replace("'","''", $_POST["uLongitude".$j])."' where Id=".intval($_POST["targetId".$j]);
            $DA->executeSQL($sql);
        }
        $j=$j+1;
    }
}
//var_dump($_POST);
?>