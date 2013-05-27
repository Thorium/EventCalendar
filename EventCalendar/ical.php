<?php
 header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
 header('Expires: '. gmdate("D, d M Y H:i:s") , ' GMT');
 header("Cache-Control: no-cache, must-revalidate");
 header("Pragma: no-cache");

//Debug:
ini_set('display_errors', 'On');
 
//Alustetaan sivu:
require 'dataaccess.php';
$DA = new Data_Access;
$DA->openDatabase();

if(isset($_REQUEST["itemId"]) && is_numeric($_REQUEST["itemId"])) {

$sql="select Id, Title, Visible, EventType, DateYear, DateMonth, DateDay, DateHour, DateMinute, City, StreetAddress, LocationDetails, EventDetails from EventCalendar where Visible=0 and EventType=0 and Id=".($_REQUEST["itemId"]/100);
$eventdata=$DA->getValues($sql);

if(isset($eventdata)){
    while($row=mysqli_fetch_array($eventdata, MYSQL_ASSOC)){

$evtStartTime = mktime($row["DateHour"],$row["DateMinute"],0,$row["DateMonth"],$row["DateDay"],$row["DateYear"]);
$evtEndTime = mktime($row["DateHour"]+1,$row["DateMinute"],0,$row["DateMonth"],$row["DateDay"],$row["DateYear"]);
$ical = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//eventcalendar//NONSGML v1.0//EN
BEGIN:VEVENT
UID:" . md5($row["Id"]) . "@test.com
SUMMARY:" . $row["Title"] . "
TZID:EET-1276
DTSTAMP:" . gmdate('Ymd').'T'. gmdate('His') . "Z
DTSTART:" . date("Ymd", $evtStartTime) . 'T' . date("His", $evtStartTime)."
DTEND:" . date("Ymd", $evtEndTime) . 'T' . date("His", $evtEndTime)."
LOCATION:" . $row["StreetAddress"] . ", " . $row["City"] . "
DESCRIPTION:" . $row["LocationDetails"] . ", " . $row["EventDetails"] . "
END:VEVENT
END:VCALENDAR";
    }
}

header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: inline; filename=test.ics');
echo $ical;
}
exit;
?>