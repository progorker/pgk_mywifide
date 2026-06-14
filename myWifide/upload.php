<?php
/*
 * Copyright (c) 2026 Dinh Thoai Tran <zinospetrel@sdf.org>
 * All rights reserved.
 *
 * + Source URL: https://github.com/progorker/pgk_mywifide/
 *
 * + License: GPL-2.0
 */

set_time_limit(0);

header( 'Content-Type: text/plain' );

function copy_folder( $src_dir, $tag_dir ) {
  $text = trim( @shell_exec( "ls -1 $src_dir" ) . '' );
  $lines = explode( "\n", $text );
  foreach ( $lines as $ln ) {
    $ln = trim( $ln );
    if ( $ln === '' || $ln === '.' || $ln === '..' ) continue;
    if ( strpos( strtolower( $ln ), '.php' ) !== false ) continue;
    if ( strpos( strtolower( $ln ), '.html' ) !== false ) continue;
    if ( strpos( strtolower( $ln ), '.htm' ) !== false ) continue;
    if ( strpos( strtolower( $ln ), '.js' ) !== false ) continue;
    if ( strpos( strtolower( $ln ), '.css' ) !== false ) continue;

    $src_file = $src_dir . '/' . $ln;
    $tag_file = $tag_dir . '/' . $ln;
    if ( is_file( $src_file ) ) {
      if ( strpos( strtolower( $ln ), '.sql' ) === false ) continue;
      copy_file( $src_file, $tag_file );
    } else if ( is_dir( $src_file ) ) {
      @mkdir( $tag_file, 0777, true );
      copy_folder( $src_file, $tag_file );
    }
  }
}

function copy_file( $src_file, $tag_file ) {
  $cmd = "cp -f $src_file $tag_file";
  @shell_exec( $cmd );
}

if ( strtolower( $_SERVER['REQUEST_METHOD'] ) === 'post' ) {
  if ( isset( $_FILES['zip'] ) ) {
    $tmp_file = $_FILES['zip']['tmp_name'];
    $filename = $_FILES['zip']['name'];
    $fileext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
    if ( $fileext === 'zip' ) {
      $tmp_dir = __DIR__ . '/tmp/' . uniqid();
      @mkdir( $tmp_dir, 0777, true );
      $zip_file = $tmp_dir . '/' . $filename;
      if ( move_uploaded_file( $tmp_file, $zip_file ) ) {
        $code = substr( strrev( uniqid() ), 0, 4 );
        $cmd = "cd $tmp_dir && unzip $filename";
        @shell_exec( $cmd );
        @unlink( $zip_file );
        $text = trim( @shell_exec( "ls -1 $tmp_dir" ) . '' );
        $lines = explode( "\n", $text );
        if ( count( $lines ) === 1 ) {
          $dir = trim( $lines[0] );
          if ( $dir !== '' ) {
            $src_dir = $tmp_dir . '/' . $dir;
            $tag_dir = __DIR__ . '/buffers/' . $code;
            @mkdir( $tag_dir, 0777, true );
            copy_folder( $src_dir, $tag_dir );
            echo "\n", "[ $filename ] file is uploaded to __BUFFER_DIR__/$code folder!", "\n";
          } else {
            echo "\n", "Failed to unzip [ $filename ] file!", "\n";
          }
        } else {
          echo "\n", "Failed to unzip [ $filename ] file!", "\n";
        }
      } else {
        echo "\n", "Failed to upload [ $filename ] file!", "\n";
      }
      $cmd = "rm -rf $tmp_dir";
      @shell_exec( $cmd );
    } else {
      echo "\n", "[$fileext] file is not supported!", "\n";
    }
  } else {
    echo "\n", "There is no uploaded zip file!", "\n";
  }
} else {
  echo "\n", "There is no uploaded zip file! Method is not post!", "\n";
}
?>