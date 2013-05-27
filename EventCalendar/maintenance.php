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

$hideGone = "where ISNULL(CONCAT(DateYear, '-', DateMonth, '-', DateDay)) or DateYear=0 or STR_TO_DATE(CONCAT(DateYear, '-', DateMonth, '-', DateDay),'%Y-%m-%d') >= NOW() ";

if(isset($_GET["showold"])){
    $hideGone = "";
}

$sql="select Id, Title, Visible, EventType, DateYear, DateMonth, DateDay, DateHour, DateMinute, City, StreetAddress, LocationDetails, EventDetails, Latitude, Longitude from EventCalendar " . $hideGone . "order by STR_TO_DATE(CONCAT(DateYear, '-', DateMonth, '-', DateDay),'%Y-%m-%d') DESC, Id DESC";
$eventdata=$DA->getValues($sql);

$sql2="select Id, Request, Email, Contacted, Created from ContactRequest order by Created DESC";
$contactRequests=$DA->getValues($sql2);


?><!DOCTYPE html>

<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <title>Event calendar</title>    
    <script src="http://code.jquery.com/jquery-1.9.1.js" type="text/javascript"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js" type="text/javascript"></script>
    <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.6.2/modernizr.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
    <link href="evtcal.css" rel="stylesheet" type="text/css" />
    <script src="eventcalendar.js" type="text/javascript"></script>
    <script src="evtcal2.js" type="text/javascript"></script>
    <script type="text/javascript">
        function deleteItem(id){
            var r=confirm("Haluatko varmasti poistaa kohteen? Voit myös vain piilottaa sen, asettamalla näkyvyyden pois päältä.");
            if(r==true){
                $("#deleteId").val(id);
                $("#deleteForm").submit(function(e){
                    e.preventDefault();
                    $sdata = $(this).serialize();
                    //alert($sdata);
                    
                    $.ajax({
                        cache: false,
                        type: 'POST',
                        url: 'maintenancebl.php',
                        traditional: true,
                        data: $sdata,
                        success: function(data){
                            alert("Kohde poistettu."+data);
                            document.location.href="maintenance.php";
                        },
                        error:function(xhr){
                            alert("Toiminto epäonnistui. " + xhr.statusText);
                        }
                    });
                });
                $("#deleteForm").submit();
                return true;
            } else {
                $("#deleteId").val("");
                return false;
            }
        };
        function deleteContact(id){
            var r=confirm("Haluatko varmasti poistaa yhteystiedon?");
            if(r==true){
                $("#deleteContactId").val(id);
                $("#deleteContactForm").submit(function(e){
                    e.preventDefault();
                    $sdata = $(this).serialize();
                    //alert($sdata);
                    
                    $.ajax({
                        cache: false,
                        type: 'POST',
                        url: 'maintenancebl.php',
                        traditional: true,
                        data: $sdata,
                        success: function(data){
                            alert("Yhteystieto poistettu."+data);
                            document.location.href="maintenance.php";
                        },
                        error:function(xhr){
                            alert("Toiminto epäonnistui. " + xhr.statusText);
                        }
                    });
                });
                $("#deleteContactForm").submit();
                return true;
            } else {
                $("#deleteContactId").val("");
                return false;
            }
        };
        function fetchPosition(id){
            if(id=="new"){
                $("#Latitude").val("10.00");
                $("#Longitude").val("10.00");
            }else{
                $("#Latitude"+id).val("11.00");
                $("#Longitude"+id).val("11.00");
            }
            return false;
        };

        $(function () {
            var aMonth = ["01","02","03","04","05","06","07","08","09","10","11","12"];
            var aDay = ["01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31"];
            var aHour = ["01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24"];
            var aMinute = ["00","15","30","45"];
            $(".aMonth").autocomplete({source: aMonth});
            $(".aDay").autocomplete({source: aDay});
            $(".aHour").autocomplete({source: aHour});
            $(".aMinute").autocomplete({source: aMinute});
        
            $("#dialog").dialog({ autoOpen: false });
            $("#updateForm").submit(function(e){
                e.preventDefault();
                $sdata = $(this).serialize();
                //alert($sdata);

                $.ajax({
                    cache: false,
                    type: 'POST',
                    traditional: true,
                    url: 'maintenancebl.php',
                    data: $sdata,
                    success: function(data){
                        alert("Tiedot päivitetty."+data);
                        document.location.href="maintenance.php";
                    },
                    error:function(xhr){
                        alert("Toiminto epäonnistui. " + xhr.statusText);
                    }
                    });
            });
            
            $("#addInfoForm").submit(function(e){
                e.preventDefault();
                $sdata = $(this).serialize();
                //alert($sdata);
                
                $.ajax({
                    cache: false,
                    type: 'POST',
                    traditional: true,
                    url: 'maintenancebl.php',
                    data: $sdata,
                    success: function(data){
                        alert("Tiedote lisätty."+data);
                        document.location.href="maintenance.php";
                    },
                    error:function(xhr){
                        alert("Toiminto epäonnistui. " + xhr.statusText);
                    }
                    });
            });

            $("#addEventForm").submit(function(e){
                e.preventDefault();
                $sdata = $(this).serialize();
                //alert($sdata);
                $.ajax({
                    cache: false,
                    type: 'POST',
                    url: 'maintenancebl.php',
                    traditional: true,
                    data: $sdata,
                    success: function(data){
                        alert("Tapahtuma lisätty."+data);
                        document.location.href="maintenance.php";
                    },
                    error:function(xhr){
                        alert("Toiminto epäonnistui. " + xhr.statusText);
                    }
                    });
            });
        });
    </script>
</head>
    <body>

            <table class="datacontainer">
            <thead><tr class="dsHead"><th>Id</th>               <th>Event title</th>              <th>Year</th>               <th>Month</th>             <th>Day</th>             <th>Hour</th>             <th>Minute</th>             <th>City</th>                   <th>Street Address</th>                         <th>Location details</th>                                                  <th>Event details</th>                              <th>Latitude</th>                     <th>Longitude</th></tr></thead>
            <tbody id="#datasource" class="datacontainer">
                <!--
                    This structure comes from the data source. (Write with ASP, PHP or what ever...)
                    Modify the data values and linecount, but please use format provided here.
                    There are two types of items, events (13 columns) and infos (3 columns).
                    Id-field is what ever url-encoded string or number.
                    Please be brief for all fields: QR-codes have SMS-style character limitations.
                -->
<?php
if(isset($eventdata)){
    while($row=mysqli_fetch_array($eventdata, MYSQL_ASSOC)){
        if($row["EventType"]=="0" || $row["EventType"]==0){
            print "<tr class='dsEvent'><td class='dsId'>".htmlspecialchars($row["Id"]);
            print "</td>\r\n<td class='dsTitle'>".htmlspecialchars($row["Title"]);
            print "</td>\r\n<td class='dsYear'>".htmlspecialchars($row["DateYear"]);
            print "</td>\r\n<td class='dsMonth'>".htmlspecialchars($row["DateMonth"]);
            print "</td>\r\n<td class='dsDay'>".htmlspecialchars($row["DateDay"]);
            print "</td>\r\n<td class='dsHour'>".htmlspecialchars($row["DateHour"]);
            print "</td>\r\n<td class='dsMinute'>".htmlspecialchars($row["DateMinute"]);
            print "</td>\r\n<td class='dsCity'>".htmlspecialchars($row["City"]);
            print "</td>\r\n<td class='dsStreetAddress'>".htmlspecialchars($row["StreetAddress"]);
            print "</td>\r\n<td class='dsLocationDetails'>".htmlspecialchars($row["LocationDetails"]);
            print "</td>\r\n<td class='dsEventDetails'>".htmlspecialchars($row["EventDetails"]);
            print "</td>\r\n<td class='dsLatitude'>".htmlspecialchars($row["Latitude"]);
            print "</td>\r\n<td class='dsLongitude'>".htmlspecialchars($row["Longitude"])."</td></tr>";
        } else {
            print "<tr class='dsInfo'><td class='dsId'>".$row["Id"]."</td>\r\n<td colspan='9' class='dsTitle'>".$row["Title"]."</td>\r\n<td colspan='3' class='dsDetails'>".$row["EventDetails"]."</td></tr>";
        }
    }
}                    
?>
                <!-- End of events. -->
            </tbody>
        </table>

    <h3>Testitapahtumien ja tiedotteiden ylläpito</h3>
    Menneet testitapahtumat piilotetaan automaattisesti. <br/>
    (Näkyvillä-kenttä: 0 = Näkyy, 1 = Piilossa)
    <form id="updateForm" name="updateForm">
        <table class="mdatacontainer">
            <thead><tr class="dsHead"><th>Tunniste</th><th>Näkyvillä</th>               <th>Testin nimi</th>              <th>Vuosi</th>               <th>Kuukausi</th>             <th>Päivä</th>             <th>Tunti</th>             <th>Minuutti</th>             <th>Kaupunki</th>                   <th>Katuosoite</th>                         <th>Osoite ohje</th>                                                  <th>Testin tiedot</th>                              <th>Latitude</th>                     <th>Longitude</th></tr></thead>
            <tbody class="mdatacontainer">
                <!--
                    This is the modifyable format of the data:
                -->
<?php
mysqli_data_seek($eventdata,0);
if(isset($eventdata)){
    $i=0;
    while($row=mysqli_fetch_array($eventdata, MYSQL_ASSOC)){
        print "<tr class='dsEvent'><td class='dsId'><input class='graytarget' name='targetId".$i."' id='targetId".$i."' type='text' readonly style='width: 50px' value='".htmlspecialchars($row["Id"]);
        print "' /></td>\r\n<td class='dsTitle'><input name='Visible".$i."' type='text' style='width: 50px' id='Visible".$i."' value='".htmlspecialchars($row["Visible"]);
        print "' /><input name='EventType".$i."' id='EventType".$i."' type='hidden' value='".htmlspecialchars($row["EventType"]);
        if($row["EventType"]==0){
            print "' /></td>\r\n<td class='dsTitle'><input name='Title".$i."' id='Title".$i."' style='width: 200px' type='text' value='".htmlspecialchars($row["Title"]);
            print "' /></td>\r\n<td class='dsYear'><input name='DateYear".$i."' id='DateYear".$i."' style='width: 50px' type='text' value='".htmlspecialchars($row["DateYear"]);
            print "' /></td>\r\n<td class='dsMonth'><input name='DateMonth".$i."' id='DateMonth".$i."' style='width: 50px' class='aMonth' type='text' value='".htmlspecialchars($row["DateMonth"]);
            print "' /></td>\r\n<td class='dsDay'><input name='DateDay".$i."' id='DateDay".$i."' style='width: 50px' class='aDay' type='text' value='".htmlspecialchars($row["DateDay"]);
            print "' /></td>\r\n<td class='dsHour'><input name='DateHour".$i."' id='DateHour".$i."' style='width: 50px' type='text' class='aHour' value='".htmlspecialchars($row["DateHour"]);
            print "' /></td>\r\n<td class='dsMinute'><input name='DateMinute".$i."' id='DateMinute".$i."' style='width: 50px' class='aMinute' type='text' value='".htmlspecialchars($row["DateMinute"]);
            print "' /></td>\r\n<td class='dsCity'><input name='City".$i."' id='City".$i."' style='width: 200px' class='aCity' type='text' value='".htmlspecialchars($row["City"]);
            print "' /></td>\r\n<td class='dsStreetAddress'><input name='StreetAddress".$i."' id='StreetAddress".$i."' style='width: 200px' type='text' value='".htmlspecialchars($row["StreetAddress"]);
            print "' /></td>\r\n<td class='dsLocationDetails'><input name='LocationDetails".$i."' id='LocationDetails".$i."' style='width: 200px' type='text' value='".htmlspecialchars($row["LocationDetails"]);
            print "' /></td>\r\n<td class='dsEventDetails'><input name='EventDetails".$i."' id='EventDetails".$i."' style='width: 200px' type='text' value='".htmlspecialchars($row["EventDetails"]);
            print "' /></td>\r\n<td class='dsLatitude'><input name='uLatitude".$i."' id='Latitude".$i."' style='width: 100px' type='text' value='".htmlspecialchars($row["Latitude"]);
            print "' /></td>\r\n<td class='dsLongitude'><input name='uLongitude".$i."' id='Longitude".$i."' style='width: 100px' type='text' value='".htmlspecialchars($row["Longitude"]);
        } else {
            print "' /></td>\r\n<td colspan='9' class='dsTitle'><input name='Title".$i."' id='Title".$i."' style='width: 400px' type='text' value='".htmlspecialchars($row["Title"]);
            print "' /></td>\r\n<td colspan='3' class='dsDetails'><input name='EventDetails".$i."' id='EventDetails".$i."' style='width: 400px' type='text' value='".htmlspecialchars($row["EventDetails"]);
        }
        print "' /></td><td><button type='button' id='delete".htmlspecialchars($row["Id"])."' onclick='deleteItem(\"".htmlspecialchars($row["Id"])."\")'>Poista</button></td></tr>";
        $i=$i+1;
    }
}
/* Create, read, update, delete... */     
                
?>
                <!-- End of events. -->
            </tbody>
        </table>
        <input type="hidden" id="updateAll" name="updateAll" value="1" />
        <button type="submit" id="updatebtn">Tallenna kaikki</button>
        </form>
        <form id="deleteForm" name="deleteForm"> 
                <input type="hidden" id="deleteId" name="deleteId" />
        </form>
        <h3>Uusi testitapahtuma</h3>
        "Hae paikka"-nappi vaatii, että olet täyttänyt kaupungin ja katuosoitteen. Sillä saat karttakoordinaatit.
        <form id="addEventForm" name="addEventForm">
            <table class="mdatacontainer">
                <thead><tr class="dsHead"><th>Testin nimi</th>              <th>Vuosi</th>               <th>Kuukausi</th>             <th>Päivä</th>             <th>Tunti</th>             <th>Minuutti</th>             <th>Kaupunki</th>                   <th>Katuosoite</th>                         <th>Osoite ohje</th>                                                  <th>Testin tiedot</th>                       <th> </th>       <th>Latitude</th>                     <th>Longitude</th></tr></thead>
                <tbody class="mdatacontainer">
                <tr class='dsInfo'>
                    <tr class='dsEvent'>
                    <td class='dsTitle'><input name='Title' id='Title' type='text' value='' style='width: 200px' />
                    <td class='dsYear'><input name='DateYear' id='DateYear' type='text' value='' style='width: 50px' /></td>
                    <td class='dsMonth'><input name='DateMonth' id='DateMonth' class='aMonth' type='text' value='' style='width: 50px' /></td>
                    <td class='dsDay'><input name='DateDay' id='DateDay' class='aDay' type='text' value='' style='width: 50px' /></td>
                    <td class='dsHour'><input name='DateHour' id='DateHour' type='text' class='aHour' value='' style='width: 50px' /></td>
                    <td class='dsMinute'><input name='DateMinute' id='DateMinute' class='aMinute' type='text' value='' style='width: 50px' /></td>
                    <td class='dsCity'><input name='City' id='City' class='aCity' type='text' value='' style='width: 200px' /></td>
                    <td class='dsStreetAddress'><input name='StreetAddress' id='StreetAddress' type='text' value='' style='width: 200px' /></td>
                    <td class='dsLocationDetails'><input name='LocationDetails' id='LocationDetails' type='text' value='' style='width: 200px' /></td>
                    <td class='dsEventDetails'><input name='EventDetails' id='EventDetails' type='text' value='' style='width: 200px' /></td>
                    <td><button type="button" id="fetchPosBtn">Hae paikka</button></td>
                    <td class='dsLatitude'><input name='uLatitude' id='uLatitude' type='text' value='' style='width: 100px' /></td>
                    <td class='dsLongitude'><input name='uLongitude' id='uLongitude' type='text' value='' style='width: 100px' /></td>
                </tr></tbody>
            </table>
            <input type="hidden" id="addEventAction" name="addEventAction" value="1" />
            <button type="submit" id="insertevt">Lisää testitapahtuma</button>        
        </form>
        <h3>Uusi tiedote:</h3>
        <form id="addInfoForm" name="addInfoForm">
            <table class="mdatacontainer">
                <thead><tr class="dsHead"><th>Tiedotteen otsikko</th><th>Lisätiedot</th></tr></thead>
                <tbody class="mdatacontainer">
                <tr class='dsInfo'>
                    <td class='dsTitle'><input id='Title' name='Title' type='text' value='' style='width: 200px' /></td>
                    <td class='dsDetails'><input id='EventDetails' name='EventDetails' type='text' value='' style='width: 200px' /></td>
                </tr></tbody>
            </table>
            <input type="hidden" id="addInfoAction" name="addInfoAction" value="1" />
            <button type="submit" id="insertinfo">Lisää tiedote</button>
        </form>
        <br/><br/>
        <div id="mapContainer"></div>
        <br/>
        <button onclick="document.location.href='maintenance.php?showold=1'">Näytä menneet</button>
        
        <h3>Yhteydenottopyynnöt:</h3>
        
        <table>
            <thead><tr class="dsHead"><th>Tunniste</th>              <th>Pyyntö</th>               <th>Email</th>             <th>Luotu</th></tr></thead>
            <tbody class="mdatacontainer">
<?php
if(isset($contactRequests)){
    while($row=mysqli_fetch_array($contactRequests, MYSQL_ASSOC)){
            print "<tr class='dsEvent'><td class='dsId'>".htmlspecialchars($row["Id"]);
            print "</td>\r\n<td class='dsTitle'>".htmlspecialchars($row["Request"]);
            print "</td>\r\n<td class='dsYear'>".htmlspecialchars($row["Email"]);
            print "</td>\r\n<td class='dsMonth'>".htmlspecialchars($row["Created"]);
            print "</td>\r\n<td class='dsMonth'><button type='button' id='deleteContact".htmlspecialchars($row["Id"])."' onclick='deleteContact(\"".htmlspecialchars($row["Id"])."\")'>Poista</button></td></tr>";
    }
}                    
?>
            </tbody>
        </table>
        <form id="deleteContactForm" name="deleteContactForm"> 
                <input type="hidden" id="deleteContactId" name="deleteContactId" />
        </form>
        
    </body>
</html>
