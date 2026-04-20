<?php
/*
Free VPN Decrypter Bot

Developer: Abolfazl Kaj (@AbolfazlKaj)
Channel: https://t.me/IRA_Team

License: MIT
*/

error_reporting(0);
//==========================// token //==========================//
define('API_KEY', "[*Token*]");
define('API_URL', 'https://api.telegram.org/bot' . API_KEY . '/');
//==========================// config //==========================//
$admin = "[*Admin*]";

$channel = "[*Channel*]";

$get_user = json_decode(file_get_contents('https://api.telegram.org/bot' . API_KEY . '/getme'));
$usernamebot = $get_user->result->username;

$web = "[*Web*]";

$sendall_min = 300;
//==========================// database //==========================//
$dbname = "[*DbName*]";
$dbuser = "[*DbUser*]";
$dbpass = "[*DbPass*]";

$connect = new mysqli('localhost', $dbuser, $dbpass, $dbname);
$connect->query("SET NAMES 'utf8'");
$connect->set_charset('utf8mb4');

/*
Free VPN Decrypter Bot

Developer: Abolfazl Kaj (@AbolfazlKaj)
Channel: https://t.me/IRA_Team

License: MIT
*/