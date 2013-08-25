/// <reference path="http://code.jquery.com/jquery-1.9.1.js" /> 
/// <reference path="http://code.jquery.com/ui/1.10.3/jquery-ui.js" /> 
/// <reference path="http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.6.2/modernizr.min.js" /> 
$(document).ready(function () {
    var dates = $(".technicaldate").text();
    function hasEventDay(date) {
        var f1 = "" + $.datepicker.formatDate('yymmdd', date).toString();
        var f2 = "" + $.datepicker.formatDate('yymd', date).toString();
        if (dates.indexOf(f1) !== -1 || dates.indexOf(f2) !== -1) {
            return [true, 'eventDate', ""];
        }

        return [true, '', ""];
    }
    $(function () {
        $.datepicker.regional.fi = {
            closeText: '',
            prevText: '',
            nextText: '',
            currentText: '',
            monthNames: ['Tammikuu', 'Helmikuu', 'Maaliskuu', 'Huhtikuu', 'Toukokuu', 'Kes&auml;kuu', 'Hein&auml;kuu', 'Elokuu', 'Syyskuu', 'Lokakuu', 'Marraskuu', 'Joulukuu'],
            monthNamesShort: ['Tammi', 'Helmi', 'Maalis', 'Huhti', 'Touko', 'Kes&auml;', 'Hein&auml;', 'Elo', 'Syys', 'Loka', 'Marras', 'Joulu'],
            dayNamesShort: ['Su', 'Ma', 'Ti', 'Ke', 'To', 'Pe', 'Su'],
            dayNames: ['Sunnuntai', 'Maanantai', 'Tiistai', 'Keskiviikko', 'Torstai', 'Perjantai', 'Lauantai'],
            dayNamesMin: ['Su', 'Ma', 'Ti', 'Ke', 'To', 'Pe', 'La'],
            weekHeader: 'Vk',
            dateFormat: 'yymmdd',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: '',
        };
        $.datepicker.setDefaults($.datepicker.regional.fi);
        $("#datepicker").datepicker({
            onSelect: function (dateText, instr) {
                $("." + dateText).attr("class", "myDateEvent");
            },
            inline: true,
            beforeShowDay: hasEventDay
        });
    });
});

//$(function () {
//    $("img").not(":visible").each(function () {
//        $(this).data("src", this.src);
//        this.src = "";
//    });

//    var reveal = function(selector) {
//        var img = $(selector);
//        img[0].src = img.data("src");
//    };
//});