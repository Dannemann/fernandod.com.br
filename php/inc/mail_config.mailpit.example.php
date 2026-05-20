<?php
/*
 * Copy this file to php/inc/mail_config.local.php for local development with
 * the Mailpit service from infra/docker-compose.yml.
 */
define('FKD_MAIL_TRANSPORT', 'smtp');
define('FKD_MAIL_SMTP_HOST', '127.0.0.1');
define('FKD_MAIL_SMTP_PORT', '1025');
define('FKD_MAIL_SMTP_ENCRYPTION', '');
define('FKD_MAIL_SMTP_USERNAME', '');
define('FKD_MAIL_SMTP_PASSWORD', '');
define('FKD_MAIL_SMTP_DEBUG', '0');

define('FKD_MAIL_FROM_ADDRESS', 'notificacao@fernandod.com.br');
define('FKD_MAIL_FROM_NAME', 'fernandod.com.br');
define('FKD_MAIL_ADMIN_TO', '<TO>');
?>
