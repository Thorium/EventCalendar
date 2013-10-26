
// Some infrastructure code, aliases to simplify things:
#if INTERACTIVE
#r "../packages/FSharp.Data.1.1.10/lib/net40/FSharp.Data.dll"
#r "FSharp.Data.TypeProviders.dll"
#r "../packages/FunScript.1.0.0.1/lib/net40/FunScript.dll"
#r "../packages/FunScript.1.0.0.1/lib/net40/FunScript.TypeScript.dll"
#r "../packages/FunScript.1.0.0.1/lib/net40/FunScript.TypeScript.Interop.dll"
#else
[<FunScript.JS>]
module Page
#endif

open GoogleMap
open GoogleLatLong
open EventItem

open System
open System.IO
open System.Net
open FSharp.Data
open FunScript
open FunScript.TypeScript

type jq = FunScript.TypeScript.Api<"../Typings/jquery.d.ts">
type j = FunScript.TypeScript.Api< @"
../Typings/lib.d.ts
../Typings/google.maps.d.ts">

let (?) (jqx:jq.JQueryStatic) name = 
    jqx.Invoke("#" + name)

let jQuery (command:string) = jq.jQuery.Invoke(command)

let asJQuery x : jq.JQuery = unbox x 


type gmaps = j.google.maps

//Main event-list, list events as html-panels
let parseEventList() = 
    let lst = jq.jQuery?maineventlist
    let makedivi (e:notification) = // Notifications
        jQuery("<div />").attr("class", "myevent")
        |> fun a -> asJQuery(a).attr("id", e.Id)
        |> fun b -> asJQuery(b).html(@"<ul><li class=""timeAndCity"">" + e.Title + @"</li>
                                       <li class=""details"">" + e.Details + @"</li></ul>
                                       <p class=""testitPistelinja""></p>")
        |> fun c -> asJQuery(c).appendTo(lst) |> ignore
    let makediv (e:vevent) = // Events
        jQuery("<div />").attr("class", "myevent " + (jsDateFormatCalendar e.StarTime))
        |> fun a -> asJQuery(a).attr("id", e.Id)
        |> fun b -> asJQuery(b).html(@"
            <a id=""L" + j.encodeURIComponent(e.Id) + @""">
            <div id='pop" + j.encodeURIComponent(e.Id) + @"' title='Testi' class='event testipopup'>
            <ul>
            <li class=""timeAndCity"">" + jsDateFormatToUser e.StarTime + " " + e.City +  @"</li>
            <li class=""location"">" + e.StreetAddress + @" <br />" + e.LocationDetails + @"</li>
            <li class=""info"">" + e.Details + @"</li>
            <li class=""info"">&nbsp;</li>
            <li class=""info"">" + jQuery("#CalInfoDetails").text().ToString() + @"</li>
            <li class=""info"">" + e.Title + @"</li>
            <li class=""info"">&nbsp;</li>
            </ul><div class=""dialogiAlaosa"">
            <button id=""opener" + e.Id + @""" class=""qrButton""><img id=""openeri" + e.Id + @""" class=""qrButton"" src=""" + jQuery("#LocalizationQrcodeUrl").text().ToString() + @""" alt=""QR""/></button>
            <button id=""ical" + e.Id + @""" class=""icalButton""><img id=""icali" + e.Id + @""" class=""icalButton"" src=""" + jQuery("#LocalizationIcalUrl").text().ToString() + @""" alt=""iCal""/></button>
            </div></div>
            <a id=""op" + j.encodeURIComponent(e.Id) + @""" href=""#""><ul>
            <li class=""technicaldate"">" + (jsDateFormatCalendar e.StarTime) + @"</li>
            <li class=""timeAndCity"">" + jsDateFormatToUserDay e.StarTime + " " + e.City +  @"</li>
            </ul></a><script type=""text/javascript"">
$(function() {

    $(""#pop" + j.encodeURIComponent(e.Id) + @""").dialog({ autoOpen: false });
    $(""#op" + j.encodeURIComponent(e.Id) + @""").click(function(){$(""#pop" + j.encodeURIComponent(e.Id) + @""").dialog(""open"");return false;});
    $(""#opi" + j.encodeURIComponent(e.Id) + @""").hover(function(){$(""#opi" + j.encodeURIComponent(e.Id) + @""").attr('src','" + jQuery("#LocalizationInfoHoverUrl").text().ToString() + @"'); }, function() { $(""#opi" + j.encodeURIComponent(e.Id) + @""").attr('src','" + jQuery("#LocalizationInfoUrl").text().ToString() + @"');});
    $(""#openeri" + e.Id + @""").hover(function(){$(""#openeri" + e.Id + @""").attr('src','" + jQuery("#LocalizationQrcodeHoverUrl").text().ToString() + @"'); }, function() { $(""#openeri" + e.Id + @""").attr('src','" + jQuery("#LocalizationQrcodeUrl").text().ToString() + @"');});
    $(""#icali" + e.Id + @""").hover(function(){$(""#icali" + e.Id + @""").attr('src','" + jQuery("#LocalizationIcalHoverUrl").text().ToString() + @"'); }, function() { $(""#icali" + e.Id + @""").attr('src','" + jQuery("#LocalizationIcalUrl").text().ToString() + @"');});

    $(""#opener" + e.Id + @""").click(function() {
        $(""#iQRCode"").attr(""src"",""" + EventItem.fetchQRCodeUrl(Vevent(e)) + @""");
        $(""#dialog"").dialog(""open"");
    });
    $(""#ical" + e.Id + @""").click(function() {
        window.open(""" + jQuery("#IcalPath").text().ToString() + "?itemId=" + (e.Id + "00") + @""");
    });
});
            </script><p class=""testitPistelinja""></p>
            ")
        |> fun c -> asJQuery(c).appendTo(lst) |> ignore
    
    EventItem.items
    |> Seq.iter(fun i -> 
                    match i with
                    | Vevent(x) -> makediv x
                    | Note(y) -> makedivi y
                    )

// Show latitudes and longitudes of eventlist
let tellPositions (id:string) =
    seq{ for item in EventItem.items do
            match item with
            | Vevent(i) -> 
                yield GoogleLatLong.FetchLatLong(id, i.StreetAddress + ", " + i.City)
            | _ -> ignore()
       }
    |> Seq.toArray

// Convert custom events to custom Google map items.
let VeventsToMarkers(is) =
    let retr = 
        is |> Seq.map( 
            fun i ->
                match i with
                | Vevent(z) -> 
//                    j.window.alert("item: " + (z.Id).ToString()) |> ignore
                    { EventMarker.Id = z.Id; Title = z.Summary(); Coordinates = z.LatLon }
                | _ -> 
//                    j.window.alert("non-map-item") |> ignore
                    { EventMarker.Id = ""; Title = ""; Coordinates = (0.0,0.0) }
        )
        |> Seq.filter(fun i -> i.Id<>"")
        |> Seq.distinct
        |> Seq.toArray
//    j.window.alert("pit " + (Seq.length retr).ToString()) |> ignore
    retr

let fetchNewPosition() =
    j.window.alert("Suoritetaan Google-paikannus, jos ei toimi, niin koita uusiksi...") |> ignore
    let getval item = (jQuery("#" + item).``val``()).ToString()
    GoogleLatLong.FetchLatLong("", getval("StreetAddress") + ", " + getval("City"))
        
// Run the program:
let main() = 
    parseEventList() |> ignore // Fetch event list
    let marks = VeventsToMarkers EventItem.items

    GoogleMap.showMyPosition(marks) // Make google map

    jq.jQuery?fetchPosBtn.click(fetchNewPosition)

    //tellPositions() |> ignore  // This would show latitudes and longitudes

let components = 
    //Data.Components.DataProviders @ 
    Interop.Components.all
do Runtime.Run(components=components)

