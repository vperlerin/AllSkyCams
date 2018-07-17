<?php


// Mailer
// SMTP
define('SMTP_HOST','smtp.gmail.com');
define('SMTP_USER','noreply@castlecomm.com');
define('SMTP_PWD','1ancel011ancel01');
define('SMTPSecure','tls');
define('SMTP_TLS_PORT','587');

// Pathing Variables
define ('VIEWS', ROOT_DIR . '/application/views');

// Encryption Variables
define ('CRYPT_ALGO', 'tripledes');
define ('CRYPT_MODE', 'cbc');
define ('CRYPT_IV',   '37128423');
define ('ENC_KEY',	  'allons_enfants_de_la_patrie');

// Cookie Variables
define ('COOKIE_EXPIRES', 3600);

// Google Analytics
// ex:  ga('create', 'UA-51899134-1', 'imo.amsmeteors.org');
//   => ga('create', GOOGLE_ANALYTICS_UA, GOOGLE_ANALYTICS_URL);
define ('GOOGLE_ANALYTICS_UA', 'UA-22624392-1');
define ('GOOGLE_ANALYTICS_URL', 'www.allskycams.com');