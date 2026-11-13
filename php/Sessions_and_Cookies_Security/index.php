<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => false,
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();

$action = $_GET['action'] ?? '';

if ($action === 'login') {
    $_SESSION['user'] = 'ahmed';
    $_SESSION['logged_at'] = time();
    session_regenerate_id(true);
    header("Location: index.php");
    exit;
}

if ($action === 'logout') {
    session_destroy();
    header("Location: index.php");
    exit;
}

setcookie("insecure_demo", "i_am_visible_to_js", [
    'expires'  => time() + 3600,
    'path'     => '/',
    'httponly' => false,
    'samesite' => 'Lax'
]);

$headers = headers_list();
$logged  = isset($_SESSION['user']);
$sid     = session_id();

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>session_audit</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Consolas, Monaco, monospace; background: #0a0e14; color: #b3b1ad; font-size: 13px; }
.topbar { background: #131721; padding: 10px 20px; border-bottom: 1px solid #1f2430; display: flex; justify-content: space-between; align-items: center; }
.topbar h1 { font-size: 14px; color: #39bae6; font-weight: normal; letter-spacing: 1px; }
.auth { display: flex; align-items: center; gap: 14px; font-size: 12px; }
.auth a { color: #1f6feb; text-decoration: none; }
.auth a:hover { text-decoration: underline; }
.auth .on  { color: #7dbb6f; }
.auth .off { color: #f66565; }
.wrap { padding: 20px; max-width: 960px; }
.box { background: #0d1117; border: 1px solid #1f2430; margin-bottom: 14px; }
.head { padding: 8px 12px; background: #131721; border-bottom: 1px solid #1f2430; color: #e6b450; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; display: flex; justify-content: space-between; }
.body { padding: 14px; }
.sid { background: #0a0e14; padding: 10px 12px; border: 1px solid #1f2430; color: #7dbb6f; word-break: break-all; font-size: 12px; }
table { width: 100%; border-collapse: collapse; font-size: 12px; }
th { background: #131721; color: #626a73; text-align: left; padding: 8px 10px; font-weight: normal; border-bottom: 1px solid #1f2430; text-transform: uppercase; font-size: 10px; letter-spacing: 1px; }
td { padding: 10px; border-bottom: 1px solid #1f2430; }
.good { color: #7dbb6f; }
.bad  { color: #f66565; }
pre { background: #0a0e14; padding: 12px; font-size: 12px; color: #b3b1ad; white-space: pre-wrap; word-break: break-all; border: 1px solid #1f2430; font-family: inherit; }
pre.ok  { color: #7dbb6f; }
pre.err { color: #f66565; }
.note { font-size: 11px; color: #626a73; margin-top: 8px; line-height: 1.7; }
.note code { background: #131721; padding: 1px 5px; color: #e6b450; }
</style>
</head>
<body>

<div class="topbar">
  <h1>session_audit</h1>
  <div class="auth">
    <?php if ($logged): ?>
      <span class="on">● logged in as <?= h($_SESSION['user']) ?></span>
      <a href="?action=logout">logout</a>
    <?php else: ?>
      <span class="off">● anonymous</span>
      <a href="?action=login">login</a>
    <?php endif; ?>
  </div>
</div>

<div class="wrap">

<div class="box">
  <div class="head">session</div>
  <div class="body">
    <div class="sid"><?= h($sid) ?></div>
  </div>
</div>

<div class="box">
  <div class="head">cookies in browser</div>
  <div class="body">
    <table>
      <tr><th>name</th><th>value</th><th>protection</th></tr>
      <tr>
        <td>PHPSESSID</td>
        <td><?= h($sid) ?></td>
        <td class="good">HttpOnly · SameSite=Strict</td>
      </tr>
      <tr>
        <td>insecure_demo</td>
        <td>i_am_visible_to_js</td>
        <td class="bad">no HttpOnly · readable by JS</td>
      </tr>
    </table>
  </div>
</div>

<div class="box">
  <div class="head">javascript test</div>
  <div class="body">
    <pre id="jsout">running</pre>
    <div class="note">
      if <code>HttpOnly</code> works, <code>document.cookie</code> must not contain <code>PHPSESSID</code>.
    </div>
  </div>
</div>

<div class="box">
  <div class="head">raw set-cookie headers</div>
  <div class="body">
    <pre><?php
      foreach ($headers as $line) {
        if (stripos($line, 'Set-Cookie') !== false) echo h($line) . "\n";
      }
    ?></pre>
    <div class="note">
      verify <code>HttpOnly</code> and <code>SameSite=Strict</code> appear on the <code>PHPSESSID</code> cookie.
    </div>
  </div>
</div>

</div>

<script>
const c = document.cookie;
const o = document.getElementById('jsout');
if (c.indexOf('PHPSESSID') !== -1) {
  o.className = 'err';
  o.textContent = 'FAIL · PHPSESSID is readable by javascript\n\n' + c;
} else {
  o.className = 'ok';
  o.textContent = 'PASS · PHPSESSID not exposed to javascript\n\nvisible cookies: ' + (c || '(none)');
}
</script>

</body>
</html>