<?php
/*
VPN Decrypter APi

Developer: Abolfazl Kaj (@AbolfazlKaj)
Channel: https://t.me/IRA_Team

License: MIT
*/

header('Content-Type: application/json; charset=utf-8');

function parseSlipnet($input)
{
    $lines = preg_split('/\r?\n/', trim($input));

    $profiles = [];
    $errors = [];
    $i = 0;

    foreach ($lines as $line) {

        $i++;
        $line = trim($line);

        if ($line == "") {
            continue;
        }

        if (stripos($line, "slipnet-enc://") === 0) {
            $b64 = substr($line, 14);
            try {
                try {
                    $plain = decryptData($b64);
                    $profiles[] = $plain;
                } catch (Exception $e) {
                    $errors[] = "Line $i: Failed to decrypt, skipping";
                }
            } catch (Exception $e) {
                $errors[] = "Line $i: Failed to decode, skipping";
            }
        } else {
            $errors[] = "Line $i: Invalid format, skipping";
        }
    }

    if (empty($profiles)) {
        if (!empty($errors)) {
            return [
                "success" => false,
                "error" => "No valid profiles found:\n" . implode("\n", $errors)
            ];
        }

        return [
            "success" => false,
            "error" => "No valid profiles found"
        ];
    }

    return [
        "success" => true,
        "profiles" => $profiles,
        "warnings" => $errors
    ];
}

function decryptData($base64)
{
    $data = base64_decode($base64);
    $key = hex2bin('214f052025b2f949605a5429ec3d5fa80c2022c168ad946e68852d447214dbd3');
    $iv = substr($data, 1, 12);
    $cipherTag = substr($data, 13);
    $tag = substr($cipherTag, -16);
    $ciphertext = substr($cipherTag, 0, -16);
    $plain =  openssl_decrypt($ciphertext, "aes-256-gcm", $key, OPENSSL_RAW_DATA, $iv, $tag);
    return parseSlipnetProfile($plain);
}

function parseSlipnetProfile($profile)
{
    $parts = explode("|", $profile);

    $linkParts = $parts;
    $linkParts[31] = "0";
    $linkParts[32] = "";
    $cleanProfile = implode("|", $linkParts);

    return [
        "version" => $parts[0] ?? null,
        "tunnel_type" => $parts[1] ?? null,
        "name" => $parts[2] ?? null,
        "domain" => $parts[3] ?? null,
        "resolvers" => $parts[4] ?? null,
        "authoritative_mode" => $parts[5] ?? null,
        "keepalive" => $parts[6] ?? null,
        "congestion" => $parts[7] ?? null,
        "tcp_port" => $parts[8] ?? null,
        "tcp_host" => $parts[9] ?? null,
        "gso" => $parts[10] ?? null,
        "dnstt_key" => $parts[11] ?? null,
        "socks_user" => $parts[12] ?? null,
        "socks_pass" => $parts[13] ?? null,
        "ssh_enabled" => $parts[14] ?? null,
        "ssh_user" => $parts[15] ?? null,
        "ssh_pass" => $parts[16] ?? null,
        "ssh_port" => $parts[17] ?? null,
        "ssh_host" => $parts[19] ?? null,
        "dns_transport" => $parts[22] ?? null,
        "ssh_auth_type" => $parts[23] ?? null,
        "naive_port" => $parts[28] ?? null,
        "naive_user" => $parts[29] ?? null,
        "locked" => $parts[31] ?? null,
        "lock_hash" => $parts[32] ?? null,
        "slipnet_link" => "slipnet://" . base64_encode($cleanProfile)
    ];
}

$text = $_GET['text'];

$profile = parseSlipnet($text);

echo json_encode($profile, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

/*
VPN Decrypter APi

Developer: Abolfazl Kaj (@AbolfazlKaj)
Channel: https://t.me/IRA_Team

License: MIT
*/