
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
        |> fun b -> asJQuery(b).html(@"<div class=""timeAndCity"">" + e.Title + @"</div>
                                       <div class=""details"">" + e.Details + @"</div>")
        |> fun c -> asJQuery(c).appendTo(lst) |> ignore
    let makediv (e:vevent) = // Events
        jQuery("<div />").attr("class", "myevent " + (jsDateFormatCalendar e.StarTime))
        |> fun a -> asJQuery(a).attr("id", e.Id)
        |> fun b -> asJQuery(b).html(@"
            <a id=""L" + j.encodeURIComponent(e.Id) + @""">
            <div class=""technicaldate"">" + (jsDateFormatCalendar e.StarTime) + @"</div></a>
            <div class=""timeAndCity"">" + jsDateFormatToUser e.StarTime + " " + e.City +  @"</div>
            <div class=""location"">" + e.StreetAddress + @" <br />" + e.LocationDetails + @"
            <div class=""info"">" + e.Title + ", " + e.Details + @"</div>
            <script type=""text/javascript"">
            $(function() {
                $(""#opener" + e.Id + @""").click(function() {
                    $(""#iQRCode"").attr(""src"",""" + EventItem.fetchQRCodeUrl(Vevent(e)) + @""");
                    $(""#dialog"").dialog(""open"");
                });
            });
            </script>
            <button id=""opener" + e.Id + @""">" + jq.jQuery.Invoke("#LocalizationQrcode").text().ToString() + "</button>")
        |> fun c -> asJQuery(c).appendTo(lst) |> ignore
    
    EventItem.items
    |> Seq.iter(fun i -> 
                    match i with
                    | Vevent(x) -> makediv x
                    | Note(y) -> makedivi y
                    )

// Show latitudes and longitudes of eventlist
let tellPositions() =
    seq{ for item in EventItem.items do
            match item with
            | Vevent(i) -> 
                yield GoogleLatLong.FetchLatLong(i.StreetAddress + ", " + i.City)
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

// Run the program:
let main() = 
    parseEventList() |> ignore // Fetch event list
    let marks = VeventsToMarkers EventItem.items

    GoogleMap.showMyPosition(marks) // Make google map

    //tellPositions() |> ignore  // This would show latitudes and longitudes

let components = 
    //Data.Components.DataProviders @ 
    Interop.Components.all
do Runtime.Run(components=components)

