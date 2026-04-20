<?php
session_start();

function base_url()
{
  $p = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https://' : 'http://';
  return $p . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['REQUEST_URI']), '/\\');
}

if (isset($_GET['step'])) {
  $step = intval($_GET['step']);
  $data = $_SESSION['install'];

  if ($step == 1) {
    foreach (["config.php", "lib/table.php", "bot.php"] as $f) {
      if (!file_exists($f)) die("ERROR: Missing $f");
    }
    echo "OK";
  }

  if ($step == 2) {
    $db = @new mysqli("localhost", $data['dbuser'], $data['dbpass'], $data['dbname']);
    if ($db->connect_error) die("ERROR: DB failed");
    echo "OK";
  }

  if ($step == 3) {
    $web = base_url();
    $cfg = file_get_contents("config.php");
    $cfg = str_replace(
      ["[*Token*]", "[*Admin*]", "[*Channel*]", "[*DbName*]", "[*DbUser*]", "[*DbPass*]", "[*Web*]"],
      [$data['token'], $data['admin'], $data['channel'], $data['dbname'], $data['dbuser'], $data['dbpass'], $web],
      $cfg
    );
    file_put_contents("config.php", $cfg);
    echo "OK";
  }

  if ($step == 4) {
    $web = base_url();
    @file_get_contents($web . "/lib/table.php");
    echo "OK";
  }

  if ($step == 5) {
    $web = base_url();
    $res = @file_get_contents("https://api.telegram.org/bot" . $data['token'] . "/setWebhook?url=" . $web . "/bot.php");
    if (!$res) die("ERROR: Webhook failed");
    echo "OK";
  }

  exit;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $_SESSION['install'] = $_POST;
  echo "OK";
  exit;
}
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Kaj Installer</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * {
      box-sizing: border-box
    }

    body {
      margin: 0;
      background: #0f172a;
      font-family: Segoe UI;
      color: #fff
    }

    .wrap {
      max-width: 520px;
      margin: 40px auto;
      padding: 25px;
      background: #111827;
      border-radius: 18px;
      box-shadow: 0 25px 60px rgba(0, 0, 0, .6)
    }

    .title {
      text-align: center;
      font-size: 30px;
      font-weight: 800
    }

    .title span {
      color: #38bdf8
    }

    .input {
      margin: 12px 0;
      position: relative
    }

    .input i {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #38bdf8;
      font-size: 14px;
    }

    input {
      width: 100%;
      padding: 12px 12px 12px 38px;
      border: none;
      border-radius: 10px;
      background: #1e293b;
      color: #fff;
    }

    button {
      width: 100%;
      padding: 14px;
      border: none;
      border-radius: 12px;
      background: #38bdf8;
      font-weight: bold;
      cursor: pointer;
      transition: .3s;
    }

    button:hover {
      transform: scale(1.05)
    }

    .steps {
      margin-top: 20px;
      display: none
    }

    .step {
      opacity: .3;
      padding: 12px;
      margin: 8px 0;
      background: #1e293b;
      border-radius: 10px;
      transition: .4s
    }

    .step.active {
      opacity: 1;
      transform: scale(1.03)
    }

    .step.done {
      background: #16a34a
    }

    .progress {
      height: 8px;
      background: #1e293b;
      border-radius: 10px;
      margin-top: 10px;
      display: none
    }

    .bar {
      height: 100%;
      width: 0;
      background: #38bdf8;
      border-radius: 10px;
      transition: .5s
    }

    .success {
      display: none;
      text-align: center;
      padding: 30px
    }

    .check {
      font-size: 70px;
      color: #4ade80;
      animation: pop .5s
    }

    @keyframes pop {
      0% {
        transform: scale(.5)
      }

      100% {
        transform: scale(1)
      }
    }

    .links {
      margin-top: 20px;
      display: flex;
      flex-direction: column;
      gap: 10px
    }

    .links a {
      padding: 12px;
      background: #1e293b;
      border-radius: 10px;
      color: #38bdf8;
      text-align: center;
      text-decoration: none;
      transition: .3s
    }

    .links a:hover {
      background: #38bdf8;
      color: #000
    }

    .footer {
      text-align: center;
      margin-top: 20px;
      color: #888
    }
  </style>

  <script>
    let current = 0;

    function startInstall() {
      let form = document.getElementById("form");
      let data = new FormData(form);

      fetch("", {
        method: "POST",
        body: data
      }).then(() => {
        form.style.display = "none";
        document.getElementById("steps").style.display = "block";
        document.getElementById("progress").style.display = "block";
        runStep();
      });
    }

    function runStep() {
      current++;
      let stepEl = document.querySelectorAll(".step")[current - 1];
      stepEl.classList.add("active");

      fetch("?step=" + current)
        .then(r => r.text())
        .then(res => {
          if (res.startsWith("ERROR")) {
            stepEl.innerHTML = res;
            return;
          }

          stepEl.classList.remove("active");
          stepEl.classList.add("done");

          document.getElementById("bar").style.width = (current * 20) + "%";

          if (current < 5) {
            setTimeout(runStep, 700);
          } else {
            document.getElementById("steps").style.display = "none";
            document.getElementById("progress").style.display = "none";
            document.getElementById("success").style.display = "block";
          }
        });
    }
  </script>

</head>

<body>

  <div class="wrap">
    <div class="title">Kaj <span>Installer</span></div>

    <form id="form">
      <div class="input"><i class="fa fa-key"></i><input name="token" placeholder="Bot Token" required></div>
      <div class="input"><i class="fa fa-user"></i><input name="admin" placeholder="Admin ID" required></div>
      <div class="input"><i class="fa fa-bullhorn"></i><input name="channel" placeholder="Channel Username" required></div>
      <div class="input"><i class="fa fa-database"></i><input name="dbname" placeholder="Database Name" required></div>
      <div class="input"><i class="fa fa-user-cog"></i><input name="dbuser" placeholder="Database User" required></div>
      <div class="input"><i class="fa fa-lock"></i><input name="dbpass" placeholder="Database Password" required></div>
      <button type="button" onclick="startInstall()"><i class="fa fa-rocket"></i> Install</button>
    </form>

    <div id="steps" class="steps">
      <div class="step">Checking files...</div>
      <div class="step">Connecting database...</div>
      <div class="step">Writing config...</div>
      <div class="step">Creating tables...</div>
      <div class="step">Setting webhook...</div>
    </div>

    <div id="progress" class="progress">
      <div id="bar" class="bar"></div>
    </div>

    <div id="success" class="success">
      <div class="check"><i class="fa fa-circle-check"></i></div>
      <h2>Installed Successfully</h2>
      <p>Delete install.php</p>
    </div>

    <div class="links">
      <a href="https://t.me/IRA_Team"><i class="fab fa-telegram"></i> @IRA_Team [Channel]</a>
      <a href="https://t.me/AbolfazlKaj"><i class="fab fa-telegram"></i> @AbolfazlKaj [Developer]</a>
    </div>

    <div class="footer">IRATeam © <?php echo date("Y"); ?></div>

  </div>

</body>

</html>