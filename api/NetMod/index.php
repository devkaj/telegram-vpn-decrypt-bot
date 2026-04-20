<?php
/*
VPN Decrypter APi

Developer: Abolfazl Kaj (@AbolfazlKaj)
Channel: https://t.me/IRA_Team

License: MIT
*/

header("Content-Type: application/json; charset=UTF-8");

function DecryptNetM($a)
{
    $key = '_netsyna_netmod_';
    $data = base64_decode($a);
    $decr = openssl_decrypt($data, "AES-128-ECB", $key, OPENSSL_RAW_DATA);

    if (!$decr) {
        echo json_encode([
            "status" => false,
            "message" => "Decrypt failed"
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }

    return $decr;
}

$text = $_GET['text'];

if (!$text) {
    echo json_encode([
        "status" => false,
        "message" => "Invalid config"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
}

preg_match('#^nm-([a-z]+)://#', $text, $matches);
$type = $matches[1] ?? 'unknown';

$text = preg_replace('#^nm-[a-z]+://#', '', $text);

$decrypted = DecryptNetM($text);

$profile = json_decode($decrypted, true);

if (is_array($profile)) {

    $normal_link = $type . "://" . base64_encode($decrypted);

    echo json_encode([
        "status" => true,
        "type" => $type,
        "profile" => $profile,
        "link" => $normal_link
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    $normal_link = $type . "://" . $decrypted;

    echo json_encode([
        "status" => true,
        "type" => $type,
        "profile" => $decrypted, // raw string
        "link" => $normal_link
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

/*
VPN Decrypter APi

Developer: Abolfazl Kaj (@AbolfazlKaj)
Channel: https://t.me/IRA_Team

License: MIT
*/