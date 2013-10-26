﻿
// Some infrastructure code, aliases to simplify things:
#if INTERACTIVE
#r "../packages/FSharp.Data.1.1.10/lib/net40/FSharp.Data.dll"
#r "FSharp.Data.TypeProviders.dll"
#r "../packages/FunScript.1.0.0.1/lib/net40/FunScript.dll"
#r "../packages/FunScript.1.0.0.1/lib/net40/FunScript.TypeScript.dll"
#r "../packages/FunScript.1.0.0.1/lib/net40/FunScript.TypeScript.Interop.dll"
#else
[<FunScript.JS>]
module GoogleLatLong
#endif

open System
open System.IO
open System.Net
open FSharp.Data
open FunScript
open FunScript.TypeScript

type jq = FunScript.TypeScript.Api<"../Typings/jquery.d.ts">
type j = FunScript.TypeScript.Api< @"
../Typings/lib.d.ts">

let jQuery (command:string) = jq.jQuery.Invoke(command)

//JSON Reply domain (FunScript doesn't seem to support JsonProvider yet)
type public Location = public {
     mutable lat : float
     mutable lng : float
     }

type public Geometry = public {
     mutable location : Location
     mutable location_type : string
     mutable viewport : obj
     }

type public Result = public {
     mutable address_components : obj array
     mutable formatted_address : string
     mutable geometry : Geometry
     mutable types : obj array
     }

type public LocationReply = public {
     mutable results : Result array
}


// Fetch latitude and longitude based on address

let internal fetchCoordinates address (callback:'a -> unit) =
    let settings = jq.JQueryAjaxSettings()
    settings.set_success(fun data _ _ -> callback(unbox data))
    //settings.dataType <- "jsonp"
    let url = "https://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=" + j.encodeURIComponent(address)
    jq.jQuery.ajax(url, settings)   


let internal parseGoogleLocation (id:string) (data:LocationReply) = 
    match Seq.isEmpty data.results with
    | true -> "No results" |>ignore
    | false ->
        let replies = Seq.head data.results
        let latlon = replies.geometry.location.lat, replies.geometry.location.lng
        jQuery("#uLatitude"+id).``val``(fst(latlon).ToString()) |> ignore
        jQuery("#uLongitude"+id).``val``(snd(latlon).ToString()) |> ignore
        j.window.alert("Asetettu paikka osoitteelle: " + replies.formatted_address)
        //latlon
    
let public FetchLatLong(id, address) = 
    let result = fetchCoordinates address (parseGoogleLocation id)
    result