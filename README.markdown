EventCalendar
------

EventCalendar is a free HTML + JavaScript-based event calendar,
to display brief list of events/venues/gatherings/meetings information, dates and locations.

The data comes from index.html page html-table called: #datasource
You can change index.html to PHP or ASP page and dynamically create your own content.

![Screenshot][1]

To compile this, you need:
------

 - Visual Studio 2012
 - FunScript (F-Sharp to Javascirpt. FunScript uses F#-language, FSharp.Data. It has strongly typed libraries via TypeScript)
 - The compiled version is available at demo-folder.

The runtime uses pure HTML5 + CSS + JavaScript with jQuery, jQuery-UI and Modernizer

Software features:
------
- Event list
- HTML5-Geolocation to find nearest events
- QR-Code generation: Get event details to your phone notification.
- Google maps to display locations of events
- Event calendar to show dates that has events (just JQuery datepicker)
- Ability to fetch Longitude/Latitude of items (not in UI)

Notes:
------

- If you have over 2000 visitors per day, I suppose Google will want something for using their APIs (Map, QR-code)
- I have tested this only with Internet Explorer 10, FireFox 20 and Chrome 26. 
- The data is in HTML-file, so it should be available even when disabled JavaScript (+CSS) (/ Lynx).
- There is not yet implementation of any way to fetch events: Now they are just JavaScript objects.
  I'm planning to make a PHP-page to generate the data (fetch from somewhere).
- I will support Finnish UI, maybe also English... ;-)

What this is not:
------

- This is not generic user control. No Flash. No Silverlight. Not this time.
- Direct plugin of some CMS system. But with light modifications you may use it as one...
- This is not registration/enrollment-system, advertisement-system or news/RSS-source. Social media does that better.


  [1]: https://github.com/Thorium/EventCalendar/blob/master/demo/demo.jpg
