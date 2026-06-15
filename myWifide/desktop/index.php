<?php
/*
 * =====] jQuery on client side has license as       ]=====
 *
 * + License: MIT
 * 
 * + License URL: https://jquery.com/license/
 *
 * ========================================================
 *
 * =====] Following PHP functions has license as [=========
 *
 * + check_http_headers_for_mobile()
 * + g_match(string $regex, string $userAgent)
 * + match_user_agent_with_first_found_matching_rule( $userAgent )
 *
 * -----
 *
 * Copyright (c) 2021 Şerban Ghiţă, Nick Ilyin and contributors.
 *
 * + License: MIT
 * 
 * + License URL: https://github.com/serbanghita/Mobile-Detect/blob/4.x/LICENSE
 *
 * + Source URL: https://github.com/serbanghita/Mobile-Detect
 *
 * ========================================================
 *
 * =====] Other PHP, HTML & CSS codes has license as [=====
 *
 * Copyright (c) 2026 Dinh Thoai Tran <zinospetrel@sdf.org>
 * All rights reserved.
 *
 * + Source URL: https://github.com/progorker/pgk_mywifide/
 *
 * + License: GPL-2.0
 *
 * ========================================================
 */

session_start();
global $g_config, $g_unlocked_str, $g_token;
require_once __DIR__ . '/../config.php';
$g_token = uniqid();

$g_unlocked_str = 'true';
$_SESSION['myWifide_'.$g_token] = true;
if ( $g_config['mytestor.locking'] ) {
  $g_unlocked_str = 'false';
  $_SESSION['myWifide_'.$g_token] = false;
}

require_once __DIR__ . '/../mobile_detect.php';
    
if ( check_http_headers_for_mobile() ) {
  header('Location: ./../mobile/');
}
?>
<html>
<head>
  <title>[ myWifide ] IDE @ Wi-Fi for MySQL</title>
  <script src="./../jquery-4.0.0.min.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
  <style>
body {
  margin: 0px;
  padding: 0px;
  font-family: monospace;
  font-size: 12px;
  color: black;
  background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAECAYAAACzzX7wAAAAAXNSR0IArs4c6QAAAClJREFUCJmFjCESAAAIg5jn/788ixoskrYAsm0ASQD0XYKHnHHNKb6FAgU2CgYY5wFmAAAAAElFTkSuQmCC'); 
}

.dtt-page-counter-cover {
  width: 210mm;
  height: 1px;
  margin: 0px auto 0px auto;
}

.dtt-page {
  width: 210mm;
  height: 297mm;
  height: 165mm;
  background: url('data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEBLAEsAAD/4QDyRXhpZgAASUkqAAgAAAAIAA4BAgASAAAAbgAAABIBAwABAAAAAQAAABoBBQABAAAAgAAAABsBBQABAAAAiAAAACgBAwABAAAAAgAAADEBAgANAAAAkAAAADIBAgAUAAAAngAAAGmHBAABAAAAsgAAAAAAAABDcmVhdGVkIHdpdGggR0lNUAAsAQAAAQAAACwBAAABAAAAR0lNUCAyLjEwLjM2AAAyMDI2OjAyOjI2IDA1OjAxOjEzAAIAhpIHABkAAADQAAAAAaADAAEAAAABAAAAAAAAAAAAAAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVAA/+EMz2h0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8APD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNC40LjAtRXhpdjIiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIgeG1sbnM6ZGM9Imh0dHA6Ly9wdXJsLm9yZy9kYy9lbGVtZW50cy8xLjEvIiB4bWxuczpHSU1QPSJodHRwOi8vd3d3LmdpbXAub3JnL3htcC8iIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1wTU06RG9jdW1lbnRJRD0iZ2ltcDpkb2NpZDpnaW1wOjkyZDMwMjA3LWFmMmQtNDY5Ni1hMzJmLTMyMzYwOWJmNzE1YiIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo5ZmY0YzA0Yy1mZjExLTRiOTYtOTA3Zi1mMDkzNjUzMTE5Y2IiIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDpmOWI0ODY4MS05MzA2LTQzY2MtOTFiZi1iOTFlOWVhMTMzYmMiIGRjOkZvcm1hdD0iaW1hZ2UvanBlZyIgR0lNUDpBUEk9IjIuMCIgR0lNUDpQbGF0Zm9ybT0iTGludXgiIEdJTVA6VGltZVN0YW1wPSIxNzcyMDU2ODc1MDg4MzE5IiBHSU1QOlZlcnNpb249IjIuMTAuMzYiIHhtcDpDcmVhdG9yVG9vbD0iR0lNUCAyLjEwIiB4bXA6TWV0YWRhdGFEYXRlPSIyMDI2OjAyOjI2VDA1OjAxOjEzKzA3OjAwIiB4bXA6TW9kaWZ5RGF0ZT0iMjAyNjowMjoyNlQwNTowMToxMyswNzowMCI+IDx4bXBNTTpIaXN0b3J5PiA8cmRmOlNlcT4gPHJkZjpsaSBzdEV2dDphY3Rpb249InNhdmVkIiBzdEV2dDpjaGFuZ2VkPSIvIiBzdEV2dDppbnN0YW5jZUlEPSJ4bXAuaWlkOjY5ZjczZmYxLTZjMDMtNGMzMC05YjE3LTNlN2M2MTEzZmYxOSIgc3RFdnQ6c29mdHdhcmVBZ2VudD0iR2ltcCAyLjEwIChMaW51eCkiIHN0RXZ0OndoZW49IjIwMjYtMDItMjZUMDU6MDE6MTUrMDc6MDAiLz4gPC9yZGY6U2VxPiA8L3htcE1NOkhpc3Rvcnk+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDw/eHBhY2tldCBlbmQ9InciPz7/4gKwSUNDX1BST0ZJTEUAAQEAAAKgbGNtcwRAAABtbnRyUkdCIFhZWiAH6gACABkAFQA3AABhY3NwQVBQTAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA9tYAAQAAAADTLWxjbXMAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA1kZXNjAAABIAAAAEBjcHJ0AAABYAAAADZ3dHB0AAABmAAAABRjaGFkAAABrAAAACxyWFlaAAAB2AAAABRiWFlaAAAB7AAAABRnWFlaAAACAAAAABRyVFJDAAACFAAAACBnVFJDAAACFAAAACBiVFJDAAACFAAAACBjaHJtAAACNAAAACRkbW5kAAACWAAAACRkbWRkAAACfAAAACRtbHVjAAAAAAAAAAEAAAAMZW5VUwAAACQAAAAcAEcASQBNAFAAIABiAHUAaQBsAHQALQBpAG4AIABzAFIARwBCbWx1YwAAAAAAAAABAAAADGVuVVMAAAAaAAAAHABQAHUAYgBsAGkAYwAgAEQAbwBtAGEAaQBuAABYWVogAAAAAAAA9tYAAQAAAADTLXNmMzIAAAAAAAEMQgAABd7///MlAAAHkwAA/ZD///uh///9ogAAA9wAAMBuWFlaIAAAAAAAAG+gAAA49QAAA5BYWVogAAAAAAAAJJ8AAA+EAAC2xFhZWiAAAAAAAABilwAAt4cAABjZcGFyYQAAAAAAAwAAAAJmZgAA8qcAAA1ZAAAT0AAACltjaHJtAAAAAAADAAAAAKPXAABUfAAATM0AAJmaAAAmZwAAD1xtbHVjAAAAAAAAAAEAAAAMZW5VUwAAAAgAAAAcAEcASQBNAFBtbHVjAAAAAAAAAAEAAAAMZW5VUwAAAAgAAAAcAHMAUgBHAEL/2wBDAAMCAgMCAgMDAwMEAwMEBQgFBQQEBQoHBwYIDAoMDAsKCwsNDhIQDQ4RDgsLEBYQERMUFRUVDA8XGBYUGBIUFRT/2wBDAQMEBAUEBQkFBQkUDQsNFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBT//gAUQ3JlYXRlZCB3aXRoIEdJTVAA/8IAEQgADwAPAwERAAIRAQMRAf/EABcAAAMBAAAAAAAAAAAAAAAAAAABAgj/xAAVAQEBAAAAAAAAAAAAAAAAAAAAAf/aAAwDAQACEAMQAAAB1MjUSl//xAAXEAEBAQEAAAAAAAAAAAAAAAABABEx/9oACAEBAAEFAgMwjl//xAAUEQEAAAAAAAAAAAAAAAAAAAAg/9oACAEDAQE/AR//xAAUEQEAAAAAAAAAAAAAAAAAAAAg/9oACAECAQE/AR//xAAYEAACAwAAAAAAAAAAAAAAAAAAARAhMf/aAAgBAQAGPwJUYKP/xAAYEAEBAAMAAAAAAAAAAAAAAAAAMWGRof/aAAgBAQABPyGaMTTmR//aAAwDAQACAAMAAAAQe4//xAAUEQEAAAAAAAAAAAAAAAAAAAAg/9oACAEDAQE/EB//xAAXEQEBAQEAAAAAAAAAAAAAAAABABAR/9oACAECAQE/EFus5//EABoQAQACAwEAAAAAAAAAAAAAAAEAESFRsUH/2gAIAQEAAT8QQiihbRqGTkmz45Foq4n/2Q==');
  background-color: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(400px);
  -webkit-backdrop-filter: blur(400px);
  margin: 10px auto 10px auto;
  outline: solid 1px black;
  border-right: solid 1px black;
  border-bottom: solid 1px black;
  overflow: hidden;
}

.dtt-page-inner {
  margin: 5px;
  padding: 0px;
  font-family: monospace;
  font-size: 12px;
  color: black;
  white-space: pre-wrap;
  line-height: 18px;
  width: 102mm;
  height: 297mm;
  overflow: hidden;
  float: left;
}

.dtt-page-counter-inner {
  width: 1px;
  height: 1px;
  position: absolute;
}

.dtt-page-counter {
  width: 1px;
  height: 1px;
  position: absolute;
  top: 5px;
  left: -20px;
}

.dtt-page-counter div {
  background-color: white;
  border: solid 1px black;
  border-radius: 10px 0px 0px 10px;
  width: 30px;
  height: 14px;
  padding: 5px;
  text-align: left; 
  cursor: pointer;
  cursor: hand;
}

.dtt-grey {
  color: grey;
}

#dtt-minute {
  font-weight: bold;
  font-size: 18px;
}

#dtt-second {
  font-weight: bold;
  font-size: 18px;
}

#page-s .dtt-result {
  border: dashed 2px gainsboro;
  border-radius: 10px;
  background-color: white-smoke;
}

#page-s .dtt-script {
  border: dashed 2px gainsboro;
}

.dtt-textbox {
  font-family: monospace;
  font-size: 12px;
  color: black;
  background-color: white;
  border: solid 1px gainsboro;
  border-radius: 5px;
  padding: 2px 5px 2px 5px;
}

.dtt-button {
  font-family: monospace;
  font-size: 12px;
  color: black;
  background-color: white;
  border: solid 2px green;
  border-left: solid 5px green;
  border-radius: 0px 5px 5px 0px;
  padding: 5px 10px 5px 10px;
}

  </style>
  <script>
let g_token = '<?php print( $g_token ); ?>';
let g_unlocked = <?php print( $g_unlocked_str ); ?>;

function g_unlock( cb ) {
  if ( g_unlocked ) return true;
  let pwd = prompt( 'Please enter password to unlock feature: ');
  let v_cb = cb;
  $.post( './../unlock.php', { 'token': g_token, 'pwd': pwd } ).done(function(response) {
    if ( response == 'y' ) {
      g_unlocked = true;
      setTimeout( v_cb, 100 );
    } else {
      alert( 'Password does not match! Feature is not unlocked!' );
    }
  }).fail(function(jqXHR, textStatus, errorThrown) {
    alert( 'Failed to unlock feature!' );
  });  
  return false;
}

function g_load() {
  $(window).resize( function() {
    g_resize();
  } );
  g_resize();
  g_show_page( 'o' );
}

function g_show_page_raw( code ) {
  $('.dtt-cover').hide();
  $('#page-' + code).show();
}

function g_show_page( code ) {
  if ( code !== 'o' ) {
     let cb = "g_show_page_raw('" + code + "');";
     if ( ! g_unlock( cb ) ) return;
  }
  $('.dtt-cover').hide();
  $('#page-' + code).show();
}

function g_resize() {
  let sw = $(window).width();
  let sh = $(window).height();
  $('body').width( sw - 5 );
  $('body').height( sh - 5 );
  $('body').css( 'overflow', 'hidden' );
  $('.dtt-page').width( sw - 40 );
  $('.dtt-page').height( sh - 15 );
  $('.dtt-page').css( 'margin', '5px 0px 5px 25px' );
  $('.dtt-page').css( 'overflowX', 'hidden' );
  $('.dtt-page').css( 'overflowY', 'scroll' );
  $('.dtt-page-counter-cover').width( sw - 40 );
  $('.dtt-page-inner').width( sw - 40 - 35 );
  $('.dtt-page-inner').css( 'minHeight', (sh - 15) + 'px');
  $('#page-o .dtt-page-inner').css( 'minHeight', '2200px');
  $('.dtt-page-inner').css( 'border', 'dotted 2px gainsboro' );
  $('.dtt-page-inner').css( 'padding', '5px' );
  $('#page-s .dtt-script').width( sw - 40 - 35 - 15 + 10 );
  $('#page-s .dtt-script').height( parseInt( ( sh - 15 - 15 - 15 ) / 2 ) - 10 );
  $('#page-s .dtt-result').width( sw - 40 - 35 - 15 + 10 );
  $('#page-s .dtt-result').height( parseInt( ( sh - 15 - 15 - 15 ) / 2 ) - 10 );
  $('#page-s .dtt-page-inner').height( sh - 15 - 15 - 10 );
  $('#page-s .dtt-result').css( 'marginTop', '10px' );
  $('#page-s .dtt-page-inner').css( 'minHeight', (sh - 15 - 15 - 15) + 'px');
  $('#page-s .dtt-page').css( 'overflowY', 'hidden' );
}

function g_refine( sql ) {
  let nsql = "\n" + sql + "\n";
  if ( nsql.indexOf( "\n-- upload" ) >= 0 || nsql.indexOf( " -- upload" ) >= 0 ) {
    $('#dtt-file').val('');
    g_show_page('u');
    sql = sql.replaceAll( '-- upload', '-- _upload_ --' );
  }
  return sql;
}

function g_execute() {
  $('#page-s .dtt-result').val('');
  let code = $('#page-s .dtt-script').val();
  code = g_refine( code );
  $('#page-s .dtt-result').val( "\n" + 'Executing SQL ...' + "\n" );
  $.post( './../execute.php', { 'token': g_token, 's': code } ).done(function(response) {
    if ( response.indexOf( "\n" + '-- loading --' + "\n" ) >= 0 ) {
      let text = response.replaceAll( "\n" + '-- loading --' + "\n", '' );
      let old_script = $('#page-s .dtt-script').val();
      $('#page-s .dtt-script').val(text);
      $('#page-s .dtt-result').val( 'Script is loaded ...' + "\n\nOld scripts are as following:\n-----------\n" + old_script );
    } else {
      $('#page-s .dtt-result').val(response);
    }
  }).fail(function(jqXHR, textStatus, errorThrown) {
    let message = "\n" + 'Status Code: ' + jqXHR.status + "\n" + 'Status Text: ' + textStatus + "\n" + 'Error Thrown: ' + errorThrown + "\n" + 'Server Response: ' + jqXHR.responseText + "\n";
    $('#page-s .dtt-result').val( message );    
  });
}
  </script>
</head>
<body onload="g_load()">
  <div id="page-o" class="dtt-cover">
  <div class="dtt-page-counter-cover"><div class="dtt-page-counter-inner"><div style="top: 5px" class="dtt-page-counter" onclick="g_show_page('o');"><div>&nbsp;O</div></div></div></div>
  <div class="dtt-page"><div class="dtt-page-inner">=============================_============
  _ __ _ _ ___  __ _ ___ _ _| |_____ _ _ 
 | '_ \ '_/ _ \/ _` / _ \ '_| / / -_) '_|
 | .__/_| \___/\__, \___/_| |_\_\___|_|  
=|_|===========|___/======================
      Testor - Unit Testing Platform
             ----- oOo ------
    [ myWifide ] IDE @ Wi-Fi for MySQL
==========================================


-|_|-----------|___/----------------------
                 Pages
------------------------------------------

+ <a href="#" onclick="g_show_page('o'); return false;">Overview<a> : the entrance of myWifide

+ <a href="#" onclick="g_show_page('s'); return false;">Script<a> : the code editor of myWifide

  o Tap on 'E' tab on the left to execute SQL script.


-|_|-----------|___/----------------------
                 Help
------------------------------------------

+--------------+----------+-----------------------------------------------------------------------------------------------------+
| command      | shortcut | description                                                                                         |
+--------------+----------+-----------------------------------------------------------------------------------------------------+
| ?            | (\?)     | Synonym for `help'.                                                                                 |
| charset      | (\C)     | Switch to another charset. Might be needed for processing binlog with multi-byte charsets.          |
| clear        | (\c)     | Clear the current input statement.                                                                  |
| connect      | (\r)     | Reconnect to the server. Optional arguments are db and host.                                        |
| delimiter    | (\d)     | Set statement delimiter.                                                                            |
| ego          | (\G)     | Send command to MariaDB server, display result vertically.                                          |
| exit         | (\q)     | Exit mysql. Same as quit.                                                                           |
| go           | (\g)     |  Send command to MariaDB server.                                                                    |
| help         | (\h)     | Display this help.                                                                                  |
| nopager      | (\n)     | Disable pager, print to stdout.                                                                     |
| nowarning    | (\w)     | Don't show warnings after every statement.                                                          |
| pager        | (\P)     | Set PAGER [to_pager]. Print the query results via PAGER.                                            |
| print        | (\p)     | Print current command.                                                                              |
| prompt       |  (\R)    | Change your mysql prompt.                                                                           |
| quit         | (\q)     | Quit mysql.                                                                                         |
| costs        | (\Q)     | Toggle showing query costs after each query                                                         |
| source       | (\.)     | Execute an SQL script file. Takes a file name as an argument.                                       |
| status       | (\s)     | Get status information from the server.                                                             |
| use          | (\u)     | Use another database. Takes database name as argument.                                              |
| warnings     | (\W)     | Show warnings after every statement.                                                                |
| -- pattern   |          | Get code pattern from myTestor.                                                                     |
| -- workdir   |          | Set work dir. Argument is selected directory.                                                       |
| -- upload    |          | Upload zip file.                                                                                    |
| -- download  |          | Zip folder & download zip file. Argument is relative path.                                          |
| -- load      |          | Load script file into script editor. Argument is relative path.                                     |
| -- list      |          | List buffer directory. Argument is relative path.                                                   |
| -- remove    |          | Remove file. Argument is relative path.                                                             |
| -- save      |          | Save previous code to file. Does not execute script. Argument is relative path.                     |
| -- cat       |          | Display script file. Does not execute script. Argument is relative path.                            |
| -- open      | (-- \o)  | Open remote database. Arguments are host, port, username, password, database. Execute below script. |
+--------------+----------+-----------------------------------------------------------------------------------------------------+

  </div></div>
  </div>

  <div id="page-s" class="dtt-cover" style="display: none">
  <div class="dtt-page-counter-cover"><div class="dtt-page-counter-inner"><div style="top: 5px" class="dtt-page-counter" onclick="g_show_page('o');"><div>&nbsp;O</div></div></div></div>

  <div class="dtt-page-counter-cover"><div class="dtt-page-counter-inner"><div style="top: 5px" class="dtt-page-counter" onclick="g_show_page('o');"><div>&nbsp;O</div></div><div style="top: 35px" class="dtt-page-counter" onclick="g_show_page('s');"><div>&nbsp;S</div></div><div style="top: 85px" class="dtt-page-counter" onclick="g_execute();"><div>&nbsp;E</div></div></div></div>
  <div class="dtt-page"><div class="dtt-page-inner"><textarea class="dtt-script"></textarea><textarea class="dtt-result"></textarea></div></div></div>

  <div id="page-u" class="dtt-cover" style="display: none">
  <div class="dtt-page-counter-cover"><div class="dtt-page-counter-inner"><div style="top: 5px" class="dtt-page-counter" onclick="g_show_page('o');"><div>&nbsp;O</div></div><div style="top: 35px" class="dtt-page-counter" onclick="g_show_page('u');"><div>&nbsp;U</div></div><div style="top: 85px" class="dtt-page-counter" onclick="g_show_page('s');"><div>&nbsp;S</div></div></div></div>
  <div class="dtt-page"><div class="dtt-page-inner"><form enctype="multipart/form-data" target="_blank" method="post" action="./../upload.php">+ Zip File:
<input type="file" id="dtt-file" name="zip" class="dtt-textbox" />
  
<input name="submit" type="submit" value="Upload" class="dtt-button" onclick="g_show_page('s');" />
</form> 
  </div></div>
  </div>

</body>
</html>
