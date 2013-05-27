<?php
//Debug:
ini_set('display_errors', 'On');
 
//Alustetaan sivu:
require 'dataaccess.php';
$DA = new Data_Access;
$DA->openDatabase();

$hideGone = "and (ISNULL(CONCAT(DateYear, '-', DateMonth, '-', DateDay)) or DateYear=0 or STR_TO_DATE(CONCAT(DateYear, '-', DateMonth, '-', DateDay),'%Y-%m-%d') >= NOW() ) ";
$sql="select Id, Title, Visible, EventType, DateYear, DateMonth, DateDay, DateHour, DateMinute, City, StreetAddress, LocationDetails, EventDetails, Latitude, Longitude from EventCalendar where Visible=0 " . $hideGone . "order by STR_TO_DATE(CONCAT(DateYear, '-', DateMonth, '-', DateDay),'%Y-%m-%d') DESC, Id DESC";
$eventdata=$DA->getValues($sql);

?><!DOCTYPE html>

<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <title>Testikalenteri</title>    
    <script src="http://code.jquery.com/jquery-1.9.1.js" type="text/javascript"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js" type="text/javascript"></script>
    <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.6.2/modernizr.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
    <link href="evtcal.css" rel="stylesheet" type="text/css" />
    <script src="eventcalendar.js" type="text/javascript"></script>
    <script src="evtcal2.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(function () {
            $("#dialog").dialog({ autoOpen: false });
            $("#mapContainer").resizable({
                helper: "ui-resizable-helper",
                handles: 's, w, sw',
                minHeight: 570,
                minWidth: 270,
                knobHandles: true,
            });
            $("#wish").submit(function(e){
                e.preventDefault();
                $serdata = $(this).serialize();
                //alert($serdata);
                
                $.ajax({
                    cache: false,
                    type: 'POST',
                    url: 'testikalenteribl.php',
                    data: $serdata,
                    success: function(data){
                        //alert(data);
                        if(data=="safty-fail"){
                            alert("Vastasit turvakysymykseen väärin.");
                        }else{
                            alert("Palaute lähetetty. Pyrimme vastaamaan toiveeseesi.");
                            $("#wish").closest('form').find("input[type=text], textarea").val("");
                        }
                    },
                    error:function(xhr){
                        alert("Lähetys epäonnistui. " + xhr.statusText);
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
            print "<tr class='dsInfo'><td class='dsId'>".htmlspecialchars($row["Id"])."</td>\r\n<td colspan='9' class='dsTitle'>".htmlspecialchars($row["Title"])."</td>\r\n<td colspan='3' class='dsDetails'>".htmlspecialchars($row["EventDetails"])."</td></tr>";
        }
    }
}                    
?>
                <!-- End of events. -->
            </tbody>
        </table>

        <div style="display:inline;float:right">

            Voit zoomata karttaa...
            <div id="mapContainer"></div>
        </div>
        
        <div style="width: 600px;"><div style="display:inline;float:right"><div class="datepicker" id="datepicker"></div></div></div>

        <h1 class="maintopic">Testikalenteri</h1>
        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam molestie orci sit amet urna euismod id pulvinar mauris blandit. Integer ac purus dui. In ac nisl quis purus feugiat euismod quis et metus. In sodales sollicitudin placerat. 
        </p>
        <p>
            Vivamus gravida iaculis tincidunt. Nam sodales, nisi sed luctus sollicitudin, magna quam suscipit tortor, vel condimentum sapien odio tristique neque. Nunc malesuada velit et ligula semper molestie. Donec ipsum dui, cursus at cursus eu, venenatis ac diam. Phasellus dolor metus, auctor sit amet congue id, vestibulum ac est. Integer et placerat sem. Suspendisse dignissim sollicitudin dui, eget rhoncus justo rutrum at. Vestibulum vehicula malesuada ante, vitae porta metus ornare a.
        </p>
        <div id="maineventlist"></div>
        <div id="dialog" title="QR-Code">
            <p>Valokuvaa QR-koodi asianmukaisella ohjelmalla kännykälläsi, niin voit asettaa muistutuksen tapahtumasta.</p>
            <img id="iQRCode" alt="QR-code" />
        </div>
        <div id="LocalizationQrcode" style="visibility: hidden">Näytä QR-koodi</div>
        <div id="LocalizationIcal" style="visibility: hidden">Lataa iCal-tapahtuma</div>
        <div><form id="wish" name="wish">
            <h3>Toivo uutta testiä:</h3>
            Ilmoita toiveessasi paikkaunta ja muut toiveet. 
            Emme lähetä automaattista postia, vaan käsittelemme toiveet ja palaamme asiaan, jos se on aiheellista.
            <div><label for="mailbody">Toive (255 merkkiä): </label><br/><textarea name="mailbody" id="mailbody" maxlength="255" style="height: 100px;width: 300px;"></textarea></div>
            <div><label for="mailfrom">Sähköposti : </label><br/><input type="email" name="mailfrom" id="mailfrom" tooltip="Palaamme asiaan" maxlength="255" style="width: 300px;" /></div>
            <div><label for="answer">Turvakysymys: Paljonko on viisi ynnä kolme (kirjaimin)?  </label><br/><input name="answer" type="text" id="answer" maxlength="255" /></div>
            <div><input type="hidden" name="sendmail" id="sendmail" value="1" /><button id="sendbtn" type="submit">Lähetä</button></div>
            </form>
        </div>
    </body>
</html>

