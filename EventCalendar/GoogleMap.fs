
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
module GoogleMap
#endif

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
type m = FunScript.TypeScript.Api<"../Typings/modernizr.d.ts">

let (?) (jqx:jq.JQueryStatic) name = 
    jqx.Invoke("#" + name)

type gmaps = j.google.maps

type EventMarker = { 
        Id: string;
        Title: string;
        Coordinates: float*float
        }

// Generate map from google maps
let makeMap markers  =
    let map = 
        gmaps.Map.CreateInstance(
            j.document.getElementById("mapContainer").AsElement(), 
            new gmaps.MapOptions(
                // Map of Finland
                zoom = 5.0, 
                center = gmaps.LatLng.CreateInstance(65.45,25.96), 
                mapTypeControl = true,
                mapTypeId = gmaps.MapTypeId.ROADMAP))
    markers
    |> Seq.iter ( // All the events
        fun i -> 
            //j.window.alert((fst i.Coordinates).ToString() + " and " + (snd i.Coordinates).ToString()) |> ignore
            gmaps.Marker.CreateInstance(
                new gmaps.MarkerOptions(
                    clickable = true, 
                    draggable = false, 
                    flat = true,
                    position = gmaps.LatLng.CreateInstance(i.Coordinates), 
                    map = map, 
                    //icon = "blue_markerA.png",
                    title = i.Title                    
                    )) |> ignore)
    let myTimer = ref 0.0
    let mapresize() = 
        let refresh() =
            //j.window.alert("resized")
            gmaps.event.trigger(map, "resize", null)            
        j.clearTimeout(!myTimer)
        myTimer := j.setTimeout(refresh, 1000)
    (jq.jQuery?("mapContainer")).bind("resize", mapresize) |> ignore
    map

// Add my location to map
let showMyPosition markers = 
    let mymap = makeMap(markers)
    if m.Modernizr.geolocation && not(Seq.isEmpty markers) then
        j.navigator.AsNavigatorGeolocation().geolocation.getCurrentPosition(
            unbox(fun (position:j.Position) ->
                let latitude = position.coords.latitude
                let longitude = position.coords.longitude
                let coords = gmaps.LatLng.CreateInstance(latitude, longitude)
                gmaps.Marker.CreateInstance(
                    new gmaps.MarkerOptions(
                        position = coords, 
                        flat = true,
                        map = mymap, 
                        // Enable if you want your color to be separate from events:
                        //icon = "http://maps.google.com/mapfiles/ms/icons/blue-dot.png",
                        title = "You are here!"
                        )) |> ignore
                // distance to my position:
                let distance(marklat,marklon) =
                    let R = 6371.0; // km
                    let dLat = (marklat - latitude) * j.Math.PI / 180.0;
                    let dLon = (marklon - longitude) * j.Math.PI / 180.0;
                    let lat1 = latitude * j.Math.PI / 180.0;
                    let lat2 = marklat * j.Math.PI / 180.0;

                    let a = j.Math.sin(dLat/2.0) * j.Math.sin(dLat/2.0) +
                            j.Math.sin(dLon/2.0) * j.Math.sin(dLon/2.0) * j.Math.cos(lat1) * j.Math.cos(lat2); 
                    let c = 2.0 * j.Math.atan2(j.Math.sqrt(a), j.Math.sqrt(1.0-a)); 
                    R * c;
                let nearest = markers 
                              |> Seq.map(fun f -> distance(f.Coordinates))
                              |> Seq.minBy(fun f -> f)

                //j.window.alert("Nearest event is " + nearest.ToString() + "km away") |> ignore

                let quiteNear = markers |> Seq.filter(fun f -> let d = distance(f.Coordinates) - nearest
                                                               d < 20.0)

                // Make near events highlighted:
                quiteNear 
                |> Seq.iter(
                    fun f -> 
                        (jq.jQuery?(f.Id)).css("backgroundColor", "#75BDD5") |> ignore
                )
        ))
