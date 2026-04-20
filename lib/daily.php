<?php
/*
Free VPN Decrypter Bot

Developer: Abolfazl Kaj (@AbolfazlKaj)
Channel: https://t.me/IRA_Team

License: MIT
*/

//======================// Start //======================//
include '../config.php';
include '../lib/jdf.php';

//=======================// Variable //=======================//
$send = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM `sendall` LIMIT 1"));

date_default_timezone_set('Asia/Tehran');
$timestamp = time();
$time = date('H:i', $timestamp);
$date = gregorian_to_jalali(date('Y'), date('m'), date('d'), '/');
//=======================// Function //=======================//
function bot($method, $datas = [])
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot' . API_KEY . '/' . $method);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
  return json_decode(curl_exec($ch));
}

function estimateTime($remaining)
{
  global $sendall_min;
  return ($remaining <= $sendall_min) ? "1" : ceil($remaining / $sendall_min) + 1;
}

function updateProgress($connect, $send, $alluser)
{

  $send = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM `sendall` LIMIT 1"));
  $sent_count = $send['sended'];
  $remaining = $alluser - $sent_count;
  $estimated_time = estimateTime($remaining);

  bot('editMessageReplyMarkup', [
    'chat_id' => $send['admin'],
    'message_id' => $send['messageid'],
    'reply_markup' => json_encode([
      'inline_keyboard' => [
        [['text' => "🔹 تعداد ارسال شده: $sent_count", 'callback_data' => "none"]],
        [['text' => "🚀 زمان باقی‌مانده: $estimated_time دقیقه", 'callback_data' => "none"]],
      ]
    ])
  ]);

  if ($sent_count >= $alluser) {
    bot('sendMessage', [
      'chat_id' => $send['admin'],
      'text' => "✅ عملیات همگانی به پایان رسید!",
      'parse_mode' => "HTML",
    ]);
    bot('editMessageReplyMarkup', [
      'chat_id' => $send['admin'],
      'message_id' => $send['messageid'],
      'reply_markup' => json_encode([
        'inline_keyboard' => [
          [['text' => "✅ همگانی پایان یافت.", 'callback_data' => "none"]],
        ]
      ])
    ]);
    $connect->query("UPDATE `sendall` SET `step` = 'none', `admin` = null, `messageid` = null, `text` = '', `sended` = '0', `chat` = '' LIMIT 1");
    exit;
  }
}

function sendToTargets($connect, $send, $table, $type)
{
  global $sendall_min;

  $all_targets = mysqli_num_rows(mysqli_query($connect, "SELECT `id` FROM `$table`"));
  $targets = mysqli_query($connect, "SELECT `id` FROM `$table` LIMIT $sendall_min OFFSET {$send['sended']}");

  while ($row = mysqli_fetch_assoc($targets)) {
    if ($type == 'send') {
      bot('sendmessage', [
        'chat_id' => $row['id'],
        'text' => $send['text'],
      ]);
    } elseif ($type == 'forward') {
      bot('ForwardMessage', [
        'chat_id' => $row['id'],
        'from_chat_id' => $send['chat'],
        'message_id' => $send['text'],
      ]);
    }

    $connect->query("UPDATE `sendall` SET `sended` = `sended` + 1 LIMIT 1");
    $send['sended']++;
  }

  updateProgress($connect, $send, $all_targets);
}

//======================// Process Sending //======================//
if ($send['step'] == 'send') {
  sendToTargets($connect, $send, 'user', 'send');
} elseif ($send['step'] == 'forward') {
  sendToTargets($connect, $send, 'user', 'forward');
}

/*
Free VPN Decrypter Bot

Developer: Abolfazl Kaj (@AbolfazlKaj)
Channel: https://t.me/IRA_Team

License: MIT
*/