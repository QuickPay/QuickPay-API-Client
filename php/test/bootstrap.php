<?php

set_include_path(realpath(dirname(__FILE__) . '/..') . PATH_SEPARATOR . get_include_path());

spl_autoload_extensions('.php');
spl_autoload_register('spl_autoload');

##
## QuickPay API credentials
##
// URL to QuickPay API
define('QP_API_BASE_URI', 'http://api.tla.goddard.pil.dk');

// QuickPay API authentication user (email address)
define('QP_API_USER', 'merchant1@pil.dk');

// QuickPay API authentication password
define('QP_API_PASSWORD', '1234');