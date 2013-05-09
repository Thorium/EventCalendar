
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


let internal parseGoogleLocation (data:LocationReply) = 
    let replies = Seq.head data.results
    let latlon = replies.geometry.location.lat, replies.geometry.location.lng
    j.window.alert(replies.formatted_address + "\r\nLatitude: " + fst(latlon).ToString() + " Longitude: " + snd(latlon).ToString())
    //latlon
    
let public FetchLatLong address = 
    let result = fetchCoordinates address (parseGoogleLocation)
    result