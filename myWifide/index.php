<?php
/*
 * Copyright (c) 2026 Dinh Thoai Tran <zinospetrel@sdf.org>
 * All rights reserved.
 *
 * + Source URL: https://github.com/progorker/pgk_mywifide/
 *
 * + License: GPL-2.0
 */

require_once __DIR__ . '/mobile_detect.php';
    
if ( check_http_headers_for_mobile() ) {
  header('Location: ./mobile/');
} else {
  header('Location: ./desktop/');
}
?>
