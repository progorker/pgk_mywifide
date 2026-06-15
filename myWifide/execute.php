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
set_time_limit(0);

global $g_config, $g_buffer_dir, $g_open_text, $g_source_text, $g_list_text, $g_remove_text, $g_workdir_text, $g_use_open, $g_open_cfg, $g_download_text, $g_load_text;

require_once __DIR__ . '/config.php';

$g_open_cfg = false;

function g_help() {
  $text = <<<EOT
command\tshortcut\tdescription
?      \t(\?)\tSynonym for `help'.
charset\t(\C)\tSwitch to another charset. Might be needed for processing binlog with multi-byte charsets.
clear  \t(\c)\tClear the current input statement.
connect\t(\\r)\tReconnect to the server. Optional arguments are db and host.
delimiter\t(\d)\tSet statement delimiter.
ego    \t(\G)\tSend command to MariaDB server, display result vertically.
exit   \t(\q)\tExit mysql. Same as quit.
go     \t(\g)\t Send command to MariaDB server.
help   \t(\h)\tDisplay this help.
nopager\t(\\n)\tDisable pager, print to stdout.
nowarning\t(\w)\tDon't show warnings after every statement.
pager  \t(\P)\tSet PAGER [to_pager]. Print the query results via PAGER.
print  \t(\p)\tPrint current command.
prompt \t (\R)\tChange your mysql prompt.
quit   \t(\q)\tQuit mysql.
costs  \t(\Q)\tToggle showing query costs after each query
source \t(\.)\tExecute an SQL script file. Takes a file name as an argument.
status \t(\s)\tGet status information from the server.
use    \t(\u)\tUse another database. Takes database name as argument.
warnings\t(\W)\tShow warnings after every statement.
-- pattern\t \tGet code pattern from myTestor.
-- workdir\t \tSet work dir. Argument is selected directory.
-- upload \t \tUpload zip file.
-- download \t \tZip folder & download zip file. Argument is relative path.
-- load   \t \tLoad script file into script editor. Argument is relative path.
-- list   \t \tList buffer directory. Argument is relative path.
-- remove \t \tRemove file. Argument is relative path.
-- save   \t \tSave previous code to file. Does not execute script. Argument is relative path.
-- cat    \t \tDisplay script file. Does not execute script. Argument is relative path.
-- open   \t(-- \o)\tOpen remote database. Arguments are host, port, username, password, database. Execute below script.
EOT;
  $rs = '';
  g_parse_results( $text, $rs );
  return $rs;
}

function g_mytestor_exec( $sql, $decor = true ) {
  global $g_config, $g_use_open, $g_open_cfg;

  $host = $g_config['mytestor.host'];
  $port = $g_config['mytestor.port'];
  $user = $g_config['mytestor.username'];
  $pass = $g_config['mytestor.password'];
  $db = $g_config['mytestor.database'];
  if ( $g_use_open ) {
    return g_mysql_exec( $sql, $host, $port, $user, $pass, $db, false, $decor );
  }
  
  $cmd = $g_config['mytestor.command'];
  if ( strpos( $cmd, 'mariadb' ) !== false ) {
    $cmd .= " --skip-ssl-verify-server-cert";
  } else if ( strpos( $cmd, 'mysql' ) !== false ) {
    $cmd .= " --ssl-mode=DISABLED";  
  }
  $uid = uniqid();
  $fn = $uid . '.sql';
  $ufn = __DIR__ . '/buffers/' . $fn;
  $dir = dirname( $ufn );
  @mkdir( $dir, 0777, true );
  @file_put_contents( $ufn, $sql );
  
  $query = "cd $dir && $cmd --disable-auto-rehash -h $host -P $port --user=$user --password=$pass -e \"use $db; source ./$fn ; \" ";
  $text = @shell_exec($query) . '';
  @unlink( $ufn );
  if ( $decor ) {
    $results = '';
    g_parse_results( $text, $results );
    return $results;
  } else {
    return $text;
  }
}

function g_mysql_exec( $sql, $host, $port, $user, $pass, $db, $cfg_only = false, $decor = true ) {
  global $g_config, $g_use_open, $g_open_cfg;
  $g_use_open = true;
  if ( $g_open_cfg === false ) {
    $g_open_cfg = [];
    $g_open_cfg['mytestor.host'] = $host;
    $g_open_cfg['mytestor.port'] = $port;
    $g_open_cfg['mytestor.username'] = $user;
    $g_open_cfg['mytestor.password'] = $pass;
    $g_open_cfg['mytestor.database'] = $db;      
    if ( $cfg_only ) return '';
  } else {
    $host = $g_open_cfg['mytestor.host'];
    $port = $g_open_cfg['mytestor.port'];
    $user = $g_open_cfg['mytestor.username'];
    $pass = $g_open_cfg['mytestor.password'];
    $db = $g_open_cfg['mytestor.database'];  
  }
  
  $cmd = $g_config['mytestor.command'];
  if ( strpos( $cmd, 'mariadb' ) !== false ) {
    $cmd .= " --skip-ssl-verify-server-cert";
  } else if ( strpos( $cmd, 'mysql' ) !== false ) {
    $cmd .= " --ssl-mode=DISABLED";  
  }
  $uid = uniqid();
  $fn = $uid . '.sql';
  $ufn = __DIR__ . '/buffers/' . $fn;
  $dir = dirname( $ufn );
  @mkdir( $dir, 0777, true );
  @file_put_contents( $ufn, $sql );
  
  $query = "cd $dir && $cmd --disable-auto-rehash -h $host -P $port --user=$user --password=$pass -e \"use $db;  source ./$fn ; \" ";
  $text = @shell_exec($query) . '';
  @unlink( $ufn );
  if ( $decor ) {
    $results = '';
    g_parse_results( $text, $results );
    return $results;
  } else {
    return $text;
  }
}

function g_escape( $sql ) {
  $sql = str_replace( "_", "_._us_._", $sql );
  $sql = str_replace( "\n", "__nl__", $sql );
  $sql = str_replace( "\r", "__cr__", $sql );
  $sql = str_replace( "\t", "__tb__", $sql );
  $sql = str_replace( "\\", "__sl__", $sql );
  $sql = str_replace( '"', "__dq__", $sql );
  $sql = str_replace( "'", "__sq__", $sql );
  $sql = str_replace( "`", "__td__", $sql );
  return $sql;
}

function g_unescape( $sql ) {
  $sql = str_replace( "__nl__", "\n", $sql );
  $sql = str_replace( "__cr__", "\r", $sql );
  $sql = str_replace( "__tb__", "\t", $sql );
  $sql = str_replace( "__sl__", "\\", $sql );
  $sql = str_replace( "__dq__", '"', $sql );
  $sql = str_replace( "__sq__", "'", $sql );
  $sql = str_replace( "__td__", "`", $sql );
  $sql = str_replace( "_._us_._", "_", $sql );
  return $sql;
}

function g_param( $key ) {
  if ( isset( $_POST[ $key ] ) ) return $_POST[ $key ];
  if ( isset( $_GET[ $key ] ) ) return $_GET[ $key ];
  return '';
}

function g_finds( $keys, $src, $start = 0 ) {
  $ret = [];
  $srt = [];
  $szl = [];
  $kyl = [];
  $pidx = -1;
  foreach ( $keys as $k ) {
    $idx = strpos( $src, $k, $start );
    if ( $idx !== false ) {
      $ret[] = $idx;
      $srt[] = $idx;
      $szl[] = strlen( $k );
      $kyl[] = $k;
    }
  }
  if ( count( $srt ) > 0 ) {
    sort( $srt );
    $v = $srt[0];
    for ( $i = 0; $i < count( $ret ); $i++ ) {
      if ( $ret[ $i ] == $v ) {
        $pidx = $i;
        break;
      }
    }
  }
  return array( 'idxl' => $ret, 'szl' => $szl, 'kyl' => $kyl, 'pidx' => $pidx );
}

function g_pattern( $p_module, $p_kind, $p_code, $p_variant ) {
  $module = g_escape( $p_module );
  $kind = g_escape( $p_kind );
  $code = g_escape( $p_code );
  $variant = g_escape( $p_variant );
  $sql = "set @v_pattern = '_'; set @v_module = api_testor_unescape('$module'); set @v_kind = api_testor_unescape('$kind'); set @v_code = api_testor_unescape('$code'); set @v_variant = api_testor_unescape('$variant'); call api_testor_pattern( @v_module, @v_kind, @v_code, @v_variant, @v_pattern ); select @v_pattern as pattern\\G";
  $text = g_mytestor_exec( $sql, false );
  $idx = strpos( $text, 'pattern:' );
  if ( $idx !== false ) {
    $text = substr( $text, $idx + 8 );
  }
  return $text;
}

function g_load_source( $sql ) {
  global $g_buffer_dir, $g_source_text;
  
  $nsql = '';
  $start = 0;
  $finds = [' source ', "\n". 'source ', ' \. ', "\n".'\. ' ];
  $finds_2 = [';', "\n", "\r"];
  $rets = g_finds( $finds, $sql, $start );
  while ( count( $rets['idxl'] ) > 0 ) {
    $pidx = $rets['pidx'];
    $key = $rets['kyl'][$pidx];
    $idx = $rets['idxl'][$pidx];
    $sz = $rets['szl'][$pidx];
    $nsql .= substr( $sql, $start, $idx - $start );
    $rets_2 = g_finds( $finds_2, $sql, $idx + $sz );
    $pidx_2 = $rets_2['pidx'];
    if ( count( $rets_2['idxl'] ) > 0 ) {
      $filename = substr( $sql, $idx + $sz, $rets_2['idxl'][$pidx_2] - $idx - $sz);
      $start = $rets_2['idxl'][$pidx_2] + $rets_2['szl'][$pidx_2];
    } else {
      $filename = substr( $sql, $idx + $sz );
      $start = strlen( $sql );
    }
    $filename = trim( $filename );
    $filename = str_replace( '..', '', $filename );
    $filename = str_replace( '..', '', $filename );
    $filename = trim( $filename );
    
    $script = "\n" . @file_get_contents( $g_buffer_dir . '/' . $filename ) . "\n";
    $script = g_refine( $script );
    //$g_source_text .= "\n" . g_mytestor_exec( $script ) . "\n";
    $nsql .= "\n-- loadsrc --\n";
    $nsql .= $script;
        
    $rets = g_finds( $finds, $sql, $start );
  }
  $nsql .= substr( $sql, $start );
  return $nsql;
}

function g_load_help( $sql ) {
  global $g_buffer_dir;
  
  $has_help = false;
  $nsql = '';
  $start = 0;
  $finds = [' help ', "\n".'help ', "\n".'help'."\n", ' \\h ', "\n".'\\h ', "\n".'\\h'."\n", ' ? ', "\n".'? ', "\n".'?'."\n", ' \\? ', "\n".'\\? ', "\n".'\\?'."\n" ];
  $finds_2 = [';', "\n", "\r"];
  $rets = g_finds( $finds, $sql, $start );
  while ( count( $rets['idxl'] ) > 0 ) {
    $pidx = $rets['pidx'];
    $key = $rets['kyl'][$pidx];
    $idx = $rets['idxl'][$pidx];
    $sz = $rets['szl'][$pidx];
    $nsql .= substr( $sql, $start, $idx - $start );
    $rets_2 = g_finds( $finds_2, $sql, $idx + $sz );
    $pidx_2 = $rets_2['pidx'];
    if ( count( $rets_2['idxl'] ) > 0 ) {
      $filename = substr( $sql, $idx + $sz, $rets_2['idxl'][$pidx_2] - $idx - $sz);
      $start = $rets_2['idxl'][$pidx_2] + $rets_2['szl'][$pidx_2];
    } else {
      $filename = substr( $sql, $idx + $sz );
      $start = strlen( $sql );
    }
    $has_help = true;
    $rets = g_finds( $finds, $sql, $start );
  }
  $nsql .= substr( $sql, $start );
  $rets = g_finds( $finds, $nsql, 0 );
  if ( count( $rets['idxl'] ) > 0 ) {
    $nsql = g_load_help( $nsql );
  }
  if ( $has_help ) {
    $nsql = "\n-- loadhelp --\n" . $nsql;
  }
  return $nsql;
}

function g_load_cat( $sql ) {
  global $g_buffer_dir;
  
  $has_cat = false;
  $nsql = '';
  $start = 0;
  $finds = [' -- cat ', "\n".'-- cat ' ];
  $finds_2 = [';', "\n", "\r"];
  $rets = g_finds( $finds, $sql, $start );
  while ( count( $rets['idxl'] ) > 0 ) {
    $pidx = $rets['pidx'];
    $key = $rets['kyl'][$pidx];
    $idx = $rets['idxl'][$pidx];
    $sz = $rets['szl'][$pidx];
    $nsql .= substr( $sql, $start, $idx - $start );
    $rets_2 = g_finds( $finds_2, $sql, $idx + $sz );
    $pidx_2 = $rets_2['pidx'];
    if ( count( $rets_2['idxl'] ) > 0 ) {
      $filename = substr( $sql, $idx + $sz, $rets_2['idxl'][$pidx_2] - $idx - $sz);
      $start = $rets_2['idxl'][$pidx_2] + $rets_2['szl'][$pidx_2];
    } else {
      $filename = substr( $sql, $idx + $sz );
      $start = strlen( $sql );
    }
    $filename = trim( $filename );
    $filename = str_replace( '..', '', $filename );
    $filename = str_replace( '..', '', $filename );
    $filename = trim( $filename );
    $cat = trim( @file_get_contents( $g_buffer_dir . '/' . $filename ) );
    if ( $cat !== '' ) {
      $has_cat = true;
      $nsql .= "\n" . $cat . "\n";
    }
    $rets = g_finds( $finds, $sql, $start );
  }
  $nsql .= substr( $sql, $start );
  $rets = g_finds( $finds, $nsql, 0 );
  if ( count( $rets['idxl'] ) > 0 ) {
    $nsql = g_load_cat( $nsql );
  }
  if ( $has_cat ) {
    $nsql = "\n-- loadcat --\n" . $nsql;
  }
  return $nsql;
}

function g_load_load( $sql ) {
  global $g_buffer_dir, $g_load_text;
  
  $nsql = '';
  $start = 0;
  $finds = [' -- load ', "\n".'-- load ' ];
  $finds_2 = [';', "\n", "\r"];
  $rets = g_finds( $finds, $sql, $start );
  while ( count( $rets['idxl'] ) > 0 ) {
    $pidx = $rets['pidx'];
    $key = $rets['kyl'][$pidx];
    $idx = $rets['idxl'][$pidx];
    $sz = $rets['szl'][$pidx];
    $nsql .= substr( $sql, $start, $idx - $start );
    $rets_2 = g_finds( $finds_2, $sql, $idx + $sz );
    $pidx_2 = $rets_2['pidx'];
    if ( count( $rets_2['idxl'] ) > 0 ) {
      $filename = substr( $sql, $idx + $sz, $rets_2['idxl'][$pidx_2] - $idx - $sz);
      $start = $rets_2['idxl'][$pidx_2] + $rets_2['szl'][$pidx_2];
    } else {
      $filename = substr( $sql, $idx + $sz );
      $start = strlen( $sql );
    }
    $filename = trim( $filename );
    $filename = str_replace( '..', '', $filename );
    $filename = str_replace( '..', '', $filename );
    $filename = trim( $filename );
    $cat = trim( @file_get_contents( $g_buffer_dir . '/' . $filename ) );
    if ( $cat !== '' ) {
      $g_load_text = "\n-- loading --\n" . $cat;
      return '';
    }
    $rets = g_finds( $finds, $sql, $start );
  }
  $nsql .= substr( $sql, $start );
  $rets = g_finds( $finds, $nsql, 0 );
  if ( count( $rets['idxl'] ) > 0 ) {
    $nsql = g_load_load( $nsql );
  }
  return $nsql;
}

function g_load_list( $sql ) {
  global $g_buffer_dir, $g_list_text;
  
  $has_list = false;
  $nsql = '';
  $start = 0;
  $finds = [' -- list ', "\n".'-- list ' ];
  $finds_2 = [';', "\n", "\r"];
  $rets = g_finds( $finds, $sql, $start );
  while ( count( $rets['idxl'] ) > 0 ) {
    $pidx = $rets['pidx'];
    $key = $rets['kyl'][$pidx];
    $idx = $rets['idxl'][$pidx];
    $sz = $rets['szl'][$pidx];
    $nsql .= substr( $sql, $start, $idx - $start );
    $rets_2 = g_finds( $finds_2, $sql, $idx + $sz );
    $pidx_2 = $rets_2['pidx'];
    if ( count( $rets_2['idxl'] ) > 0 ) {
      $filename = substr( $sql, $idx + $sz, $rets_2['idxl'][$pidx_2] - $idx - $sz);
      $start = $rets_2['idxl'][$pidx_2] + $rets_2['szl'][$pidx_2];
    } else {
      $filename = substr( $sql, $idx + $sz );
      $start = strlen( $sql );
    }
    $filename = trim( $filename );
    $filename = str_replace( '..', '', $filename );
    $filename = str_replace( '..', '', $filename );
    $filename = trim( $filename );
    $cmd = "ls -1 " . $g_buffer_dir . '/' . $filename;
    $dir = dirname( $g_buffer_dir . '/' . $filename );
    @mkdir( $dir, 0777, true );
    $cat = trim( @shell_exec( $cmd ) . '' );
    if ( $cat === '' ) {
      $cat = '__BLANK__';
    }
    if ( $cat !== '' ) {
      $has_list = true;
      $cat = "[DIR] $filename" . "\n" . $cat;
      $rs = '';
      g_parse_results( $cat, $rs );
      $g_list_text .= "\n" . $rs . "\n";
    }
    $rets = g_finds( $finds, $sql, $start );
  }
  $nsql .= substr( $sql, $start );
  $rets = g_finds( $finds, $nsql, 0 );
  if ( count( $rets['idxl'] ) > 0 ) {
    $nsql = g_load_list( $nsql );
  }
  if ( $has_list ) {
    $nsql = "\n-- loadlist --\n" . $nsql;
  }
  return $nsql;
}

function g_load_remove( $sql ) {
  global $g_buffer_dir, $g_remove_text;
  
  $nsql = '';
  $start = 0;
  $finds = [' -- remove ', "\n".'-- remove ' ];
  $finds_2 = [';', "\n", "\r"];
  $rets = g_finds( $finds, $sql, $start );
  while ( count( $rets['idxl'] ) > 0 ) {
    $pidx = $rets['pidx'];
    $key = $rets['kyl'][$pidx];
    $idx = $rets['idxl'][$pidx];
    $sz = $rets['szl'][$pidx];
    $nsql .= substr( $sql, $start, $idx - $start );
    $rets_2 = g_finds( $finds_2, $sql, $idx + $sz );
    $pidx_2 = $rets_2['pidx'];
    if ( count( $rets_2['idxl'] ) > 0 ) {
      $filename = substr( $sql, $idx + $sz, $rets_2['idxl'][$pidx_2] - $idx - $sz);
      $start = $rets_2['idxl'][$pidx_2] + $rets_2['szl'][$pidx_2];
    } else {
      $filename = substr( $sql, $idx + $sz );
      $start = strlen( $sql );
    }
    $filename = trim( $filename );
    $filename = str_replace( '..', '', $filename );
    $filename = str_replace( '..', '', $filename );
    $filename = trim( $filename );
    $kind = '';
    if ( is_dir( $g_buffer_dir . '/' . $filename ) ) {
      $dir = $g_buffer_dir . '/' . $filename;
      $cmd = "rm -rf $dir";
      $kind = '[DIR]';
      @shell_exec( $cmd );
    } else if ( is_file( $g_buffer_dir . '/' . $filename ) ) {
      @unlink( $g_buffer_dir . '/' . $filename );
      $kind = '[FILE]';
    }
    $cat = "$kind Remove" . "\n" . $filename;
    $rs = '';
    g_parse_results( $cat, $rs );
    $g_remove_text .= "\n" . $rs . "\n";
    $nsql .= "\n-- loadremove --\n";
    $rets = g_finds( $finds, $sql, $start );
  }
  $nsql .= substr( $sql, $start );
  $rets = g_finds( $finds, $nsql, 0 );
  if ( count( $rets['idxl'] ) > 0 ) {
    $nsql = g_load_remove( $nsql );
  }
  return $nsql;
}

function g_load_download( $sql ) {
  global $g_config, $g_buffer_dir, $g_download_text;
  
  $zip_cmd = $g_config['mytestor.zip_cmd'];
  
  $nsql = '';
  $start = 0;
  $finds = [' -- download ', "\n".'-- download ' ];
  $finds_2 = [';', "\n", "\r"];
  $rets = g_finds( $finds, $sql, $start );
  while ( count( $rets['idxl'] ) > 0 ) {
    $pidx = $rets['pidx'];
    $key = $rets['kyl'][$pidx];
    $idx = $rets['idxl'][$pidx];
    $sz = $rets['szl'][$pidx];
    $nsql .= substr( $sql, $start, $idx - $start );
    $rets_2 = g_finds( $finds_2, $sql, $idx + $sz );
    $pidx_2 = $rets_2['pidx'];
    if ( count( $rets_2['idxl'] ) > 0 ) {
      $filename = substr( $sql, $idx + $sz, $rets_2['idxl'][$pidx_2] - $idx - $sz);
      $start = $rets_2['idxl'][$pidx_2] + $rets_2['szl'][$pidx_2];
    } else {
      $filename = substr( $sql, $idx + $sz );
      $start = strlen( $sql );
    }
    $filename = trim( $filename );
    $filename = str_replace( '..', '', $filename );
    $filename = str_replace( '..', '', $filename );
    $filename = trim( $filename );
    $src_dir = $g_buffer_dir . '/' . $filename;
    if ( is_dir( $src_dir ) ) {
      $tmp_dir = __DIR__ . '/tmp/' . uniqid();
      @mkdir( $tmp_dir, 0777, true );
      $code = substr( strrev( uniqid() ), 0, 4 );
      $zip_dir = $tmp_dir . '/' . $code;
      @mkdir( $zip_dir, 0777, true );
      $cmd = "cp -rf $src_dir/* $zip_dir/";
      @shell_exec( $cmd );
      $zip_file = $code . '.zip';
      $cmd = "cd $tmp_dir && $zip_cmd -r $zip_file $code";      
      @shell_exec( $cmd );
      $zip_file = $tmp_dir . '/' . $zip_file;
      $dl_dir = __DIR__ . '/downloads';
      @mkdir( $dl_dir, 0777, true );
      $cmd = "cp -f $zip_file $dl_dir/";
      @shell_exec( $cmd );
      $zip_file = $code . '.zip';
      $uri = $_SERVER['REQUEST_URI'];
      $idx = strrpos( $uri, '/' );
      if ( $idx !== false ) {
        $uri = substr( $uri, 0, $idx );
      }
      $protocol = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ) ? 'https' : 'http';
      $dl_url = $protocol . '://' . $_SERVER['HTTP_HOST'] . $uri . '/downloads/' . $zip_file;
      $cat = "[ Download ] " . $filename . "\n" . $dl_url;
      $rs = '';
      g_parse_results( $cat, $rs );
      $g_download_text .= "\n" . $rs . "\n";
      $cmd = "rm -rf $tmp_dir";
      @shell_exec( $cmd );
    }
    $nsql .= "\n-- loaddownload --\n";
    $rets = g_finds( $finds, $sql, $start );
  }
  $nsql .= substr( $sql, $start );
  $rets = g_finds( $finds, $nsql, 0 );
  if ( count( $rets['idxl'] ) > 0 ) {
    $nsql = g_load_download( $nsql );
  }
  return $nsql;
}

function g_load_workdir( $sql ) {
  global $g_buffer_dir, $g_workdir_text;
  
  $nsql = '';
  $start = 0;
  $finds = [' -- workdir ', "\n".'-- workdir ' ];
  $finds_2 = [';', "\n", "\r"];
  $rets = g_finds( $finds, $sql, $start );
  while ( count( $rets['idxl'] ) > 0 ) {
    $pidx = $rets['pidx'];
    $key = $rets['kyl'][$pidx];
    $idx = $rets['idxl'][$pidx];
    $sz = $rets['szl'][$pidx];
    $nsql .= substr( $sql, $start, $idx - $start );
    $rets_2 = g_finds( $finds_2, $sql, $idx + $sz );
    $pidx_2 = $rets_2['pidx'];
    if ( count( $rets_2['idxl'] ) > 0 ) {
      $filename = substr( $sql, $idx + $sz, $rets_2['idxl'][$pidx_2] - $idx - $sz);
      $start = $rets_2['idxl'][$pidx_2] + $rets_2['szl'][$pidx_2];
    } else {
      $filename = substr( $sql, $idx + $sz );
      $start = strlen( $sql );
    }
    $filename = trim( $filename );
    $filename = str_replace( '..', '', $filename );
    $filename = str_replace( '..', '', $filename );
    $filename = trim( $filename );
    if ( is_dir( $g_buffer_dir . '/' . $filename ) ) {
      $dir = $g_buffer_dir . '/' . $filename;
      $g_buffer_dir = $dir;
      $cat = "[DIR] Work" . "\n" . $filename;
      $rs = '';
      g_parse_results( $cat, $rs );
      $g_workdir_text .= "\n" . $rs . "\n";
    }
    $nsql .= "\n-- loadworkdir --\n";
    $rets = g_finds( $finds, $sql, $start );
  }
  $nsql .= substr( $sql, $start );
  $rets = g_finds( $finds, $nsql, 0 );
  if ( count( $rets['idxl'] ) > 0 ) {
    $nsql = g_load_workdir( $nsql );
  }
  return $nsql;
}

function g_load_save( $sql ) {
  global $g_buffer_dir;
  
  $has_save = false;
  $nsql = '';
  $start = 0;
  $finds = [' -- save ', "\n".'-- save ' ];
  $finds_2 = [';', "\n", "\r"];
  $rets = g_finds( $finds, $sql, $start );
  while ( count( $rets['idxl'] ) > 0 ) {
    $pidx = $rets['pidx'];
    $key = $rets['kyl'][$pidx];
    $idx = $rets['idxl'][$pidx];
    $sz = $rets['szl'][$pidx];
    $nsql .= substr( $sql, $start, $idx - $start );
    $rets_2 = g_finds( $finds_2, $sql, $idx + $sz );
    $pidx_2 = $rets_2['pidx'];
    if ( count( $rets_2['idxl'] ) > 0 ) {
      $filename = substr( $sql, $idx + $sz, $rets_2['idxl'][$pidx_2] - $idx - $sz);
      $start = $rets_2['idxl'][$pidx_2] + $rets_2['szl'][$pidx_2];
    } else {
      $filename = substr( $sql, $idx + $sz );
      $start = strlen( $sql );
    }
    $filename = trim( $filename );
    $filename = str_replace( '..', '', $filename );
    $filename = str_replace( '..', '', $filename );
    $filename = trim( $filename );
    $fileext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
    if ( $fileext === 'sql' ) {    
      $dir = @dirname( $g_buffer_dir . '/' . $filename ) . '';
      @mkdir( $dir, 0777, true );
      @file_put_contents( $g_buffer_dir . '/' . $filename, "\n" . trim( $nsql ) . "\n" );
      $has_save = true;
    }
    $rets = g_finds( $finds, $sql, $start );
  }
  $nsql .= substr( $sql, $start );
  $rets = g_finds( $finds, $nsql, 0 );
  if ( count( $rets['idxl'] ) > 0 ) {
    $nsql = g_load_save( $nsql );
  }
  if ( $has_save ) {
    $nsql = "\n-- loadsave --\n" . $nsql;
  }
  return $nsql;
}

function g_load_pattern( $sql ) {
  global $g_buffer_dir;
  
  $nsql = '';
  $start = 0;
  $finds = [ ' -- pattern ', "\n".'-- pattern ' ];
  $finds_2 = [';', "\n", "\r"];
  $rets = g_finds( $finds, $sql, $start );
  while ( count( $rets['idxl'] ) > 0 ) {
    $pidx = $rets['pidx'];
    $key = $rets['kyl'][$pidx];
    $idx = $rets['idxl'][$pidx];
    $sz = $rets['szl'][$pidx];
    $nsql .= substr( $sql, $start, $idx - $start );
    $rets_2 = g_finds( $finds_2, $sql, $idx + $sz );
    $pidx_2 = $rets_2['pidx'];
    if ( count( $rets_2['idxl'] ) > 0 ) {
      $filename = substr( $sql, $idx + $sz, $rets_2['idxl'][$pidx_2] - $idx - $sz);
      $start = $rets_2['idxl'][$pidx_2] + $rets_2['szl'][$pidx_2];
    } else {
      $filename = substr( $sql, $idx + $sz );
      $start = strlen( $sql );
    }
    $filename = trim( $filename );
    $fields = explode( " ", $filename );
    if ( count( $fields ) >= 5 ) {
      $module = $fields[0];
      $kind = $fields[1];
      $code = $fields[2];
      $variant = $fields[3];
      $filename = $fields[4];
      $filename = trim( $filename );
      $filename = str_replace( '..', '', $filename );
      $filename = str_replace( '..', '', $filename );
      $filename = trim( $filename );
      $fileext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
      if ( $fileext === 'sql' ) {    
        $dir = @dirname( $g_buffer_dir . '/' . $filename ) . '';
        @mkdir( $dir, 0777, true );
        $pattern = "\n" . trim( g_pattern( $module, $kind, $code, $variant ) ) . "\n";
        @file_put_contents( $g_buffer_dir . '/' . $filename, $pattern );
      } 
    }
    $rets = g_finds( $finds, $sql, $start );
  }
  $nsql .= substr( $sql, $start );
  $rets = g_finds( $finds, $nsql, 0 );
  if ( count( $rets['idxl'] ) > 0 ) {
    $nsql = g_load_pattern( $nsql );
  }
  return $nsql;
}

function g_load_open( $sql ) {
  global $g_buffer_dir, $g_open_text, $g_use_open, $g_open_cfg;
  
  $g_open_text = '';
  $has_open = false;
  $nsql = '';
  $start = 0;
  $finds = [ ' -- open ', "\n".'-- open ', ' -- \\o ', "\n".'-- \\o ' ];
  $finds_2 = [';', "\n", "\r"];
  $rets = g_finds( $finds, $sql, $start );
  while ( count( $rets['idxl'] ) > 0 ) {
    $pidx = $rets['pidx'];
    $key = $rets['kyl'][$pidx];
    $idx = $rets['idxl'][$pidx];
    $sz = $rets['szl'][$pidx];
    $nsql .= substr( $sql, $start, $idx - $start );
    $rets_2 = g_finds( $finds_2, $sql, $idx + $sz );
    $pidx_2 = $rets_2['pidx'];
    if ( count( $rets_2['idxl'] ) > 0 ) {
      $filename = substr( $sql, $idx + $sz, $rets_2['idxl'][$pidx_2] - $idx - $sz);
      $start = $rets_2['idxl'][$pidx_2] + $rets_2['szl'][$pidx_2];
    } else {
      $filename = substr( $sql, $idx + $sz );
      $start = strlen( $sql );
    }
    $filename = trim( $filename );
    $fields = explode( " ", $filename );
    if ( count( $fields ) >= 5 ) {
      $host = $fields[0];
      $port = $fields[1];
      $user = $fields[2];
      $pass = $fields[3];
      $db = $fields[4];
      $script = substr( $sql, $start );
      $g_open_cfg = false;
      $g_open_text = g_mysql_exec( $script, $host, $port, $user, $pass, $db, true );
      $has_open = true;
      $start = strlen( $sql );
      $nsql .= $script;
    }
    $rets = g_finds( $finds, $sql, $start );
  }
  $nsql .= substr( $sql, $start );
  $rets = g_finds( $finds, $nsql, 0 );
  if ( count( $rets['idxl'] ) > 0 ) {
    $nsql = g_load_open( $nsql );
  }
  if ( $has_open ) {
    $nsql = $nsql . "\n-- loadopen --\n";
  }
  return $nsql;
}

function g_refine( $sql ) {
  $sql = g_load_open( "\n" . $sql . "\n" );
  $sql = g_load_workdir( "\n" . $sql . "\n" );
  $sql = g_load_pattern( "\n" . $sql . "\n" );
  $sql = str_replace( '-- source ', '-- _source_ ', $sql );
  $sql = str_replace( '-- \\. ', '-- _source_ ', $sql );
  
  $sql = g_load_source( "\n" . $sql . "\n" );
  while ( strpos( $sql, 'source ' ) !== false || strpos( $sql, '\\. ' ) !== false ) {
    $sql = g_load_source( "\n" . $sql . "\n" );
    $sql = str_replace( '-- source ', '-- _source_ ', $sql );
    $sql = str_replace( '-- \\. ', '-- _source_ ', $sql );
  }
  
  $sql = g_load_cat( "\n" . $sql . "\n" );
  $sql = g_load_save( "\n" . $sql . "\n" );
  $sql = g_load_list( "\n" . $sql . "\n" );
  $sql = g_load_remove( "\n" . $sql . "\n" );
  $sql = g_load_download( "\n" . $sql . "\n" );
  $sql = g_load_load( "\n" . $sql . "\n" );
  $sql = g_load_help( "\n" . $sql . "\n" );
  
  $sql = str_replace( ' source ', ' -- _source_ ', $sql );
  $sql = str_replace( 'source ', '-- _source_ ', $sql );
  $sql = str_replace( ' source', ' -- _source_', $sql );

  $sql = str_replace( ' \\. ', ' -- _source_ ', $sql );
  $sql = str_replace( '\\. ', '-- _source_ ', $sql );
  $sql = str_replace( ' \\.', ' -- _source_', $sql );

  $sql = str_replace( ' system ', ' -- _system_ ', $sql );
  $sql = str_replace( 'system ', '-- _system_ ', $sql );
  $sql = str_replace( ' system', ' -- _system_', $sql );

  $sql = str_replace( ' \\! ', ' -- _system_ ', $sql );
  $sql = str_replace( '\\! ', '-- _system_ ', $sql );
  $sql = str_replace( ' \\!', ' -- _system_', $sql );

  $sql = str_replace( ' edit ', ' -- _edit_ ', $sql );
  $sql = str_replace( 'edit ', '-- _edit_ ', $sql );
  $sql = str_replace( ' edit', ' -- _edit_', $sql );

  $sql = str_replace( ' \\e ', ' -- _edit_ ', $sql );
  $sql = str_replace( '\\e ', '-- _edit_ ', $sql );
  $sql = str_replace( ' \\e', ' -- _edit_', $sql );

  $sql = str_replace( ' tee ', ' -- _tee_ ', $sql );
  $sql = str_replace( 'tee ', '-- _tee_ ', $sql );
  $sql = str_replace( ' tee', ' -- _tee_', $sql );

  $sql = str_replace( ' \\T ', ' -- _tee_ ', $sql );
  $sql = str_replace( '\\T ', '-- _tee_ ', $sql );
  $sql = str_replace( ' \\T', ' -- _tee_', $sql );

  $sql = str_replace( ' notee ', ' -- _notee_ ', $sql );
  $sql = str_replace( 'notee ', '-- _notee_ ', $sql );
  $sql = str_replace( ' notee', ' -- _notee_', $sql );

  $sql = str_replace( ' \\t ', ' -- _notee_ ', $sql );
  $sql = str_replace( '\\t ', '-- _notee_ ', $sql );
  $sql = str_replace( ' \\t', ' -- _notee_', $sql );

  $sql = str_replace( ' sandbox ', ' -- _sandbox_ ', $sql );
  $sql = str_replace( 'sandbox ', '-- _sandbox_ ', $sql );
  $sql = str_replace( ' sandbox', ' -- _sandbox_', $sql );

  $sql = str_replace( ' \\- ', ' -- _sandbox_ ', $sql );
  $sql = str_replace( '\\- ', '-- _sandbox_ ', $sql );
  $sql = str_replace( ' \\-', ' -- _sandbox_', $sql );

  $sql = str_replace( ' rehash ', ' -- _rehash_ ', $sql );
  $sql = str_replace( 'rehash ', '-- _rehash_ ', $sql );
  $sql = str_replace( ' rehash', ' -- _rehash_', $sql );

  $sql = str_replace( ' \\# ', ' -- _rehash_ ', $sql );
  $sql = str_replace( '\\# ', '-- _rehash_ ', $sql );
  $sql = str_replace( ' \\#', ' -- _rehash_', $sql );

  return "\n" . trim( $sql ) . "\n";
}

function g_fill_table( $cols, $rows, $fsz, &$p_results ) {
  $p_results .= "\n\n";
  $p_results .= '+';
  for ( $i = 0; $i < count( $cols ); $i++ ) {
    $p_results .= str_pad( '-', $fsz[ $i ] + 2, '-' ) . '+';
  }
  $p_results .= "\n";
  $p_results .= '|';
  for ( $i = 0; $i < count( $cols ); $i++ ) {
    $p_results .= str_pad( ' ' . $cols[ $i ] . ' ', $fsz[ $i ] + 2, ' ' ) . '|';
  }
  $p_results .= "\n";
  $p_results .= '+';
  for ( $i = 0; $i < count( $cols ); $i++ ) {
    $p_results .= str_pad( '-', $fsz[ $i ] + 2, '-' ) . '+';
  }
  $p_results .= "\n";
  for ( $j = 0; $j < count( $rows ); $j++ ) {
    $rw = $rows[ $j ];
    $p_results .= '|';
    for ( $i = 0; $i < count( $rw ); $i++ ) {
      $p_results .= str_pad( ' ' . $rw[ $i ] . ' ', $fsz[ $i ] + 2, ' ' ) . '|';
    }
    $p_results .= "\n";
  }
  $p_results .= '+';
  for ( $i = 0; $i < count( $cols ); $i++ ) {
    $p_results .= str_pad( '-', $fsz[ $i ] + 2, '-' ) . '+';
  }
  $p_results .= "\n";
}

function g_parse_results( $text, &$p_results ) {
  $lines = explode( "\n", $text );
  $fld_cnt = -1;
  $cols = [];
  $rows = [];
  $fsz = [];
  $p_results = '';
  foreach ( $lines as $ln ) {
    if ( trim( $ln ) === '' ) continue;
    $fields = explode( "\t", $ln );
    if ( count( $fields ) !== $fld_cnt ) {
      if ( $fld_cnt > 0 ) {
        g_fill_table( $cols, $rows, $fsz, $p_results );
      }
      $rows = [];
      $cols = [];
      $fsz = [];
      foreach ( $fields as $fd ) {
        $cols[] = $fd;
        $fsz[] = strlen( $fd );
      }
      $fld_cnt = count( $fields );
      continue;
    } 
    $rw = [];
    for ( $i = 0; $i < count( $fields ); $i++ ) {
      $fd = $fields[ $i ];
      $sz = strlen( $fd );
      if ( $sz > $fsz[ $i ] ) {
        $fsz[ $i ] = $sz;
      }
      $rw[] = $fd;
    }
    $rows[] = $rw;
  }
  if ( $fld_cnt > 0 ) {
    g_fill_table( $cols, $rows, $fsz, $p_results );
  }
}

header('Content-Type: text/plain');

$token = g_param('token');
if ( !isset( $_SESSION['myWifide_'.$token] ) || $_SESSION['myWifide_'.$token] === false ) {
  exit();
}


$g_buffer_dir = __DIR__ . '/buffers';
@mkdir( $g_buffer_dir, 0777, true );

$g_use_open = false;
$g_load_text = '';
$g_download_text = '';
$g_source_text = '';
$g_list_text = '';
$g_remove_text = '';
$g_workdir_text = '';

$sql = g_param('s');
$sql = g_refine( $sql );
if ( strpos( $g_load_text, "\n-- loading --\n" ) !== false ) {
  echo $g_load_text;
  exit;
}
$result = '';
if ( strpos( $sql, "\n-- loadsrc --\n" ) !== false ) {
  $cat = "[SRC]" . "\n" . $g_source_text;
  $rs = '';
  g_parse_results( $cat, $rs );
  $result = $result . "\n" . trim( $rs ) . "\n";
}
if ( strpos( $sql, "\n-- loadcat --\n" ) !== false ||  strpos( $sql, "\n-- loadsave --\n" ) !== false || strpos( $sql, "\n-- sql --\n" ) !== false ) {
  if ( strpos( $sql, "\n-- rawsrc --\n" ) !== false ) {
    $cat = "[SQL] Start                                        ";
    $rs = '';
    g_parse_results( $cat, $rs );
    $result .= "\n" . trim( $rs ) . "\n";
    $result .= $sql;
    $cat = "[SQL] End                                          ";
    $rs = '';
    g_parse_results( $cat, $rs );
    $result .= "\n" . trim( $rs ) . "\n";
  } else {
    $cat = "[SQL]" . "\n" . $sql;
    $rs = '';
    g_parse_results( $cat, $rs );
    $result .= "\n" . trim( $rs ) . "\n";
  }
} else {
  $result .= "\n" . trim( g_mytestor_exec( $sql ) ) . "\n";
}
if ( strpos( $sql, "\n-- loadhelp --\n" ) !== false ) {
  $result = "\n" . trim( g_help() ) . "\n" . $result;
}
if ( strpos( $sql, "\n-- loadopen --\n" ) !== false ) {
  $result = $result . "\n" . trim( $g_open_text ) . "\n";
}
if ( strpos( $sql, "\n-- loadlist --\n" ) !== false ) {
  $result = $result . "\n" . trim( $g_list_text ) . "\n";
}
if ( strpos( $sql, "\n-- loadremove --\n" ) !== false ) {
  $result = $result . "\n" . trim( $g_remove_text ) . "\n";
}
if ( strpos( $sql, "\n-- loaddownload --\n" ) !== false ) {
  $result = $result . "\n" . trim( $g_download_text ) . "\n";
}
if ( strpos( $sql, "\n-- loadworkdir --\n" ) !== false ) {
  $result = $result . "\n" . trim( $g_workdir_text ) . "\n";
}
if ( strpos( $sql, "\n-- sql --\n" ) !== false ) {
  echo "\n", "=====] SQL [=====", "\n", $sql, "\n", "====================", "\n";
}
echo $result;
?>
