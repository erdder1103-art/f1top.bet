<?php
// Simple protected A/B switch admin page
// Change this password before upload if needed.
$password = 'Wk2026@Switch9981';
$configFile = __DIR__ . '/config.json';

function read_mode($configFile) {
    if (!is_file($configFile)) {
        return 'A';
    }
    $json = file_get_contents($configFile);
    $config = json_decode($json, true);
    if (is_array($config) && isset($config['mode']) && strtoupper($config['mode']) === 'B') {
        return 'B';
    }
    return 'A';
}

if (!isset($_GET['pass']) || !hash_equals($password, (string)$_GET['pass'])) {
    http_response_code(403);
    echo 'No permission';
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mode'])) {
    $mode = strtoupper((string)$_POST['mode']) === 'B' ? 'B' : 'A';
    $data = [
        'mode' => $mode,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    $ok = file_put_contents($configFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
    if ($ok === false) {
        $message = '切換失敗：config.json 無法寫入，請檢查主機檔案權限。';
    } else {
        header('Location: switch-9283.php?pass=' . urlencode($password) . '&saved=1');
        exit;
    }
}

$currentMode = read_mode($configFile);
if (isset($_GET['saved'])) {
    $message = '已成功切換，目前顯示 ' . $currentMode . ' 網站。';
}
?>
<!doctype html>
<html lang="zh-Hant">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>網站顯示開關</title>
  <style>
    *{box-sizing:border-box}
    body{margin:0;min-height:100vh;background:#101114;color:#fff;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,"Noto Sans TC",sans-serif;display:flex;align-items:center;justify-content:center;padding:24px}
    .box{width:100%;max-width:460px;background:#1b1d22;border:1px solid #343842;border-radius:18px;padding:28px;box-shadow:0 18px 50px rgba(0,0,0,.35)}
    h1{font-size:26px;margin:0 0 10px}.sub{color:#b8beca;margin:0 0 24px;line-height:1.6}.status{background:#111318;border:1px solid #343842;border-radius:14px;padding:16px;margin-bottom:18px}.mode{font-size:34px;font-weight:800;letter-spacing:.04em}.msg{background:#17351f;border:1px solid #2f8f48;color:#d8ffe1;border-radius:12px;padding:12px 14px;margin-bottom:16px}.btn{width:100%;border:0;border-radius:14px;padding:17px 18px;margin-top:12px;font-size:18px;font-weight:800;cursor:pointer;color:#fff}.btn-a{background:#2f80ed}.btn-b{background:#f2994a}.hint{font-size:13px;color:#9aa3b2;line-height:1.6;margin-top:20px}.link{color:#8ab4ff}
  </style>
</head>
<body>
  <main class="box">
    <h1>網站顯示開關</h1>
    <p class="sub">這個開關會讓所有訪客看到同一個版本：A 網站或 B 網站。</p>

    <?php if ($message !== ''): ?>
      <div class="msg"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <div class="status">
      <div>目前顯示</div>
      <div class="mode"><?php echo htmlspecialchars($currentMode, ENT_QUOTES, 'UTF-8'); ?> 網站</div>
    </div>

    <form method="post">
      <input type="hidden" name="mode" value="A">
      <button class="btn btn-a" type="submit">切換成 A 網站</button>
    </form>

    <form method="post">
      <input type="hidden" name="mode" value="B">
      <button class="btn btn-b" type="submit">切換成 B 網站</button>
    </form>

    <div class="hint">
      前台首頁：<span class="link">/index.php</span><br>
      A 網站資料夾：<span class="link">/site-a/</span><br>
      B 網站資料夾：<span class="link">/site-b/</span>
    </div>
  </main>
</body>
</html>
