
// Some infrastructure code, aliases to simplify things:
#if INTERACTIVE
#r "../packages/FSharp.Data.1.1.4/lib/net40/FSharp.Data.dll"
#r "FSharp.Data.TypeProviders.dll"
#r "../packages/FunScript.0.2.0.4/lib/net45/FunScript.dll"
#r "../packages/FunScript.0.2.0.4/lib/net45/FunScript.TypeScript.dll"
#r "../packages/FunScript.0.2.0.4/lib/net45/FunScript.TypeScript.Interop.dll"
#r "../packages/FSharp.Data.1.1.4/lib/net40/FSharp.Data.dll"
#else
[<FunScript.JS>]
module EventItem
#endif

open FunScript
open FunScript.TypeScript

type j = FunScript.TypeScript.Api< "../Typings/lib.d.ts">
type jq = FunScript.TypeScript.Api<"../Typings/jquery.d.ts">

let mydate(y,m,d,h,n) = j.Date.CreateInstance((float)y,(float)m,(float)d,(float)h,(float)n,0.0)
let mydated(y,m,d) = j.Date.CreateInstance((float)y,(float)m,(float)d)

// Javascript date-formatting:
let private formatDateData (md:j.Date) duration =
    let lz i =
        if(i<10.0) then "0" + i.ToString()
        else i.ToString()
    md.getFullYear().ToString(),
    md.getMonth() |> lz, //(md.getMonth()+1.0) |> lz,
    md.getDate() |> lz,
    md.getHours() + duration |> lz,
    md.getMinutes() |> lz,
    md.getSeconds() |> lz

let jsDateFormatCalendar (md:j.Date) =
    let (y,m,d,h,n,s) = formatDateData md 0.0
    y + "" + m + "" + d

let jsDateFormatToJson (md:j.Date) duration =
    let (y,m,d,h,n,s) = formatDateData md duration
    y + m + d + "T" + h + n + s

let jsDateFormatToUser (md:j.Date) =
    let (y,m,d,h,n,s) = formatDateData md 0.0
    d + "." + m + "." + y + " " + h + "." + n

// There are two types of events: events and general notifications
type notification(id, title, details) =
        member x.Id = id
        member x.Title = title
        member x.Details = details
    with
        member x.Info() = title + ", " + details

type vevent(id, title, starttime:j.Date, city, streetAddress, locationdetails, details, latlon:float*float) =
        member x.Id = id
        member x.Title = title //Start time in EET timezone, and event duration in hours:
        member x.StarTime = starttime
        member x.DurationHours = 1.0
        member x.City = city
        member x.StreetAddress = streetAddress
        member x.LocationDetails = locationdetails
        member x.Details = details
        member val LatLon = latlon with get, set
    with // Event as iCal, for QR-code image generation
#indent "off"
        member x.iCal() = @"BEGIN:VEVENT
UID: " + x.Id + @"@test.com
SUMMARY:" + x.Title + @"
TZID:EET-1276
DTSTART:" + (jsDateFormatToJson x.StarTime 0.0) + @"
DTEND:" + (jsDateFormatToJson x.StarTime x.DurationHours) + @"
LOCATION:" + x.StreetAddress + ", " + x.City + @"
DESCRIPTION:" + x.LocationDetails + ", " + x.Details + @"
END:VEVENT"
#indent "on"
        member x.Summary() = // Summary, used in map pop-up
            let locdet = 
                if x.LocationDetails.Length > 0 then x.LocationDetails + ", \r\n"
                else ""
            (jsDateFormatToUser x.StarTime) + " \r\n" + x.Title + " (" + x.City + ")\r\n" + locdet + x.Details

type calEvent =
| Vevent of vevent
| Note of notification
    
let fetchQRCodeUrl (itm:calEvent) = // This will make image src for event
    let size = (150).ToString()
    let baseurl = "http://chart.apis.google.com/chart?chs="+size+"x"+size+"&cht=qr&choe=UTF-8&chl="
    match itm with
    | Vevent(item) -> 
        baseurl + j.encodeURIComponent(item.iCal())
    | Note(item) ->
        baseurl + j.encodeURIComponent(item.Info())

// These are the events, should be generated from PHP or something
let items = 
    [|
        new vevent((1231).ToString(), "Test", mydate(2013,05,07,17,30), "Helsinki", "Rikhardinkatu 3", "Rikhardinkadun kirjasto, Salonki, 3. krs", "(viitenumero: 11743)", (60.1661552,24.9463225)) |> Vevent;
        new vevent((1232).ToString(), "Test", mydate(2013,05,16,17,30), "Tampere", "Kirjastotalo Metso, Pirkankatu 2", "Toivonen-sali, muumikerros", "(viitenumero: 11620)", (61.4977606,23.7507924)) |> Vevent;
        new vevent((1233).ToString(), "Test", mydate(2013,05,22,18,00), "Lappeenranta", "Villimiehenkatu 1", "", "(viitenumero: 11905)", (61.0573018,28.1917542)) |> Vevent;
        new vevent((1235).ToString(), "Test", mydate(2013,05,25,13,00), "Seinäjoki", "Alvar Aallon katu 14", "Pääkirjasto Apila, Jaaksi 3 (pieni kokoustila/atk-luokka)", "(viitenumero: 11879)", (62.7858996,22.8402672)) |> Vevent;
        new vevent((1236).ToString(), "Test", mydate(2013,05,25,13,00), "Turku", "Rauhankatu 1", "", "", (60.4526784,22.2573233)) |> Vevent;
        new vevent((1234).ToString(), "Test", mydate(2013,05,25,14,30), "Helsinki", "Rikhardinkatu 3", "Rikhardinkadun kirjasto, Salonki, 3. krs", "(viitenumero: 11895)", (60.1661552,24.9463225)) |> Vevent;
        new notification((1237).ToString(), "Global test day 1.10.2013", "") |> Note;
    |]
