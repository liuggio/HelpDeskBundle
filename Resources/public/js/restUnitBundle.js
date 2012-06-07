"use strict";
function isset () {
    // !No description available for isset. @php.js developers: Please update the function summary text file.
    //
    // version: 1109.2015
    // discuss at: http://phpjs.org/functions/isset    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: FremyCompany
    // +   improved by: Onno Marsman
    // +   improved by: Rafał Kukawski
    // *     example 1: isset( undefined, true);    // *     returns 1: false
    // *     example 2: isset( 'Kevin van Zonneveld' );
    // *     returns 2: true
    var a = arguments,
        l = a.length,        i = 0,
        undef;

    if (l === 0) {
        throw new Error('Empty isset');    }

    while (i !== l) {
        if (a[i] === undef || a[i] === null) {
            return false;        }
        i++;
    }
    return true;
}

var restclient = {
    headerLabelMaxLength: 35,
    headerMenuMaxLength: 25,
    uniqueHeaders: ['authorization'],
    navTop: null,
    urls: {
        execute: ['unit-test/api/v1/pid/execute', 'POST', 'json'],
        kill: ['unit-test/api/v1/pid/{pid}', 'DELETE', 'json'],
        pids: ['unit-test/api/v1/pids', 'GET', 'json'],
        status: ['unit-test/api/v1/pid/{pid}',  'GET', 'json'],
        json: ['unit-test/api/v1/pid/{pid}/log/json',  'GET', 'text'],
        output: ['unit-test/api/v1/pid/{pid}/log/output',  'GET', 'text'],
        error: ['unit-test/api/v1/pid/{pid}/log/error',  'GET', 'text']
    },
    hotkey: {
        send:     's',
        url:      'u',
        method:   'm',
        reqBody:  'b',
        rep1:     '1',
        rep2:     '2',
        rep3:     '3',
        rep4:     '4',
        toggleRequest: 'alt+q',
        toggleResponse: 'alt+s'
    },
    init: function () {

        $('#request-button').bind('click', restclient.sendRequest);

        $('#refresh-processes-button').bind('click', restclient.getProcesses);

        restclient.getProcesses();

    },
    appendLog: function ($data) {
        $('#logs').append("<li>" + $data+"</li>");
    },
    sendRequest: function () {

        var urlRequested = address + restclient.urls.execute[0];
        $('#request-button').attr("disabled", "disabled");

        var request = $.ajax({
            url: urlRequested,
            type: restclient.urls.execute[1],
            dataType: restclient.urls.execute[2],
            queue: "autocomplete"
        }).done(function( data ) {
                $('#request-button').removeAttr("disabled");
                restclient.appendLog('Executed pid - ' + data.pid);
                restclient.getProcesses();
        });

        request.fail(function(jqXHR, textStatus, errorThrown) {
            restclient.appendLog('Error executing pid - ' + errorThrown);
            $('#request-button').removeAttr("disabled");
        });
    },
    ajaxRequest: function () {

    },
    loadFileAndAppendTextTo: function (pid, urlObject, div) {

        var urlRequested = address + urlObject[0];
        urlRequested = urlRequested.replace('{pid}', pid);
        var request = $.ajax({
            url: urlRequested,
            type: urlObject[1],
            dataType: urlObject[2]
        });
        request.fail(function(jqXHR, textStatus, errorThrown) {
            restclient.appendLog('Error loading file' + errorThrown);
            $(div).text('failed to fetch data');
        });
        request.done(function( data ) {
            restclient.appendLog('loaded file #' + pid);
            console.log(data);
            $(div).text(data);
        });

        return request;

    },
    initwatch: function (pid) {
        $('#refresh-watch-button').bind('click', restclient.watch(pid));
        restclient.watch(pid);

    },
    watch: function (pid) {

        restclient.loadFileAndAppendTextTo(pid, restclient.urls.output, "#Output-log-container");
        restclient.loadFileAndAppendTextTo(pid, restclient.urls.error, "#Error-log-container");
        restclient.loadjsonUnitTest(pid);

    },
    loadjsonUnitTest: function (pid) {


        var urlObject = restclient.urls.json;
        var urlRequested = address + urlObject[0];
        urlRequested = urlRequested.replace('{pid}', pid);
        var request = $.ajax({
            url: urlRequested,
            type: urlObject[1]
        });
        request.fail(function(jqXHR, textStatus, errorThrown) {
            restclient.appendLog('Error loading json file' + errorThrown);
        });
        request.done(function( data ) {
            restclient.appendLog('loaded file #' + pid);
            restclient.appendLog('-' + data);
            restclient.ExtractJsonFromData(data);
            console.log(data);
        });

    },
    ExtractJsonFromData: function (data) {
        $("#json-table tbody").empty();
        $("#json-table thead").empty();
        var head='';
        var body='';
        try{
            data = String(data);
            data = data.replace(/}{/gm,"},{");
            data = "["+data+"]";

            var jsonData = $.parseJSON(data);
        }catch (e) {
            restclient.appendLog('Impossible to parse JsonData' + e);
            return false;
        }
        restclient.fillJsonTable(jsonData);
    },
    fillJsonTable: function (jsonData) {
        $("#json-table tbody").empty();
        $.each(jsonData, function(index, value) {
            console.log('*****************');
            console.log( + index +"val:" + value);
            var onerow = restclient.createARowIntoTable(value);

            $("#json-table tbody").append(onerow);
        });
//
//
//
//        $.each(jsonData, function(index, value) {
//            console.log("ind1:" + index +"val:" + value);
//
//            var onerow = restclient.createARowIntoTable(value);
//            $("#json-table tbody").append(onerow);
//            restclient.appendLog('tornato qui.');
//
////            $.each(value, function(index2, value2) {
////                console.log("ind2:" + index2 +"val:" + value2);
////            });
//
//        });
    },
    createARowIntoTable: function (rowData) {
        //if is an array
        restclient.appendLog('arrivato qui.');
        var arrayT = new Array();
        arrayT[0] =  isset(rowData.event)? rowData.event:'';
        arrayT[1] =  isset(rowData.event)? rowData.suite:'';
        arrayT[2] =  isset(rowData.tests)? rowData.tests: (isset(rowData.test)? rowData.test:'') ;
        arrayT[3] =  isset(rowData.status)? rowData.status: '' ;
        var bgcolor = (arrayT[3]=='fail')? ' bgcolor="red" ':(arrayT[3]=='pass')? ' bgcolor="green" ':'';

        arrayT[4] =  isset(rowData.time)? rowData.time: '' ;
        arrayT[5] =  isset(rowData.message)? rowData.message: '' ;
        arrayT[5] +=  isset(rowData.output)? rowData.output: '' ;


        var trace = '';
        if (isset(rowData.trace)) {
            $.each(rowData.trace, function(ind, val) {
                trace += '<li><ul>';
                $.each(val, function(ind2, val2) {
                    trace += '<li><b>'+ind2+'</b> '+val2+ '</li>';
                });
                trace += '</ul></li>';
            });
            trace = '<tr><td '+bgcolor+'>-</td><td colspan="6"><ul>'+trace+'</ul></td></tr>';
        }

        if (arrayT[0] == 'test') {
            var ROW = '<tr>\
                <td '+bgcolor+'>'+arrayT[0]+'</td>\
                <td colspan="3">'+arrayT[1]+'\
                 <br \>'+arrayT[2]+'</td>\
                <td>'+arrayT[3]+'</td>\
                <td>'+arrayT[4]+'</td>\
                <td>'+arrayT[5]+'</td>\
            </tr>'+trace;
        } else if (arrayT[0] == 'suiteStart'){
            var ROW = '<tr '+bgcolor+'>\
                <td colspan="7"><b>'+arrayT[0] + '</b> '+ arrayT[1]+'  n of test:'+arrayT[2]+'</td>\
            </tr>'+trace;
        }



        return ROW;

    },


    kill: function (pid) {
        var urlRequested = address + restclient.urls.kill[0];
        urlRequested = urlRequested.replace('{pid}', pid);
         var request = $.ajax({
            url: urlRequested,
            type: restclient.urls.kill[1],
            dataType: restclient.urls.kill[2]
        }).done(function( data ) {
                restclient.appendLog('killed processes #' + pid);
                console.log(data);
                restclient.getProcesses();
            });

        request.fail(function(jqXHR, textStatus, errorThrown) {
            restclient.appendLog('Error killing pid - ' + errorThrown);
        });

    },

    getProcesses: function () {
        var urlRequested = address + restclient.urls.pids[0];
        $('#refresh-processes-button').attr("disabled", "disabled");

        var request = $.ajax({
            url: urlRequested,
            type: restclient.urls.pids[1],
            dataType: restclient.urls.pids[2],
            queue: "autocomplete"
        }).done(function( data ) {
                $('#refresh-processes-button').removeAttr("disabled");
                restclient.appendLog('Refreshed processes');
                console.log(data);
                restclient.fillProcesses(data);
            });

        request.fail(function(jqXHR, textStatus, errorThrown) {
            restclient.appendLog('Error executing pid - ' + errorThrown);
            $('#refresh-processes-button').removeAttr("disabled");
        });
    },

    fillProcesses: function (data) {
        $("#processes-table tbody").empty();
        $.each(data, function(index, value) {
            var icon = "icon-ok-sign";
            if (!value.running) {
                var icon = "icon-minus-sign";
            }
            var row = '<tr>\
                <td class="center">'+value.pid+'</td>\
            <td class="center"><i class="'+icon+'"></i></td>\
            <td class="center">'+value.directory+'</td>\
            <td class="center">'+value.startedAt+'</td>\
            <td class="center">\
                <a  class="btn btn-mini btn-primary" onclick="restclient.initwatch(\''+value.pid+'\')"><i class="icon-info-sign icon-white"></i> Show</a>\
            ';
            if (value.running) {
                row = row + ' <a class="btn btn-mini" onclick="restclient.kill(\''+value.pid+'\')"><i class="icon-trash"></i></a>';
            }
            row = row + '</td></tr>';
            $("#processes-table").append(row);
        });
    },






    toggleRequest: function (e) {
        var toggle = $('.toggle-request');
        $('#request-container').slideToggle('slow', function () {
            toggle.text(toggle.text() == '-' ? '+' : '-');
        });
        if (e) e.preventDefault();
        return false;
    },
    toggleResponse: function (e) {
        var toggle = $('.toggle-response');
        $('#response-container').slideToggle('slow', function () {
            toggle.text(toggle.text() == '-' ? '+' : '-');
        });
        if (e) e.preventDefault();
        return false;
    }
};

window.addEventListener("load", function () { restclient.init();  }, false);
window.addEventListener("unload", function () { }, false);
