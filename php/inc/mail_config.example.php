<?php
/*
 * Copy this file to php/inc/mail_config.local.php in production and fill in
 * the SMTP values from the hosting control panel. Do not commit the local file.
 */
define('FKD_MAIL_TRANSPORT', 'smtp');
define('FKD_MAIL_SMTP_HOST', 'smtp.titan.email');
define('FKD_MAIL_SMTP_PORT', '465');
define('FKD_MAIL_SMTP_ENCRYPTION', 'ssl');
define('FKD_MAIL_SMTP_USERNAME', 'notificacao@fernandod.com.br');
define('FKD_MAIL_SMTP_PASSWORD', '<PASSWORD_HERE>');

define('FKD_MAIL_FROM_ADDRESS', 'notificacao@fernandod.com.br');
define('FKD_MAIL_FROM_NAME', 'fernandod.com.br');
define('FKD_MAIL_ADMIN_TO', '<TO>');
?>
