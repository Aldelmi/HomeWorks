<?php
$pdo = new PDO("sqlite::memory:");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec("CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT, password TEXT)");
$pdo->exec("INSERT INTO users (username, password) VALUES ('admin','admin123'),('ahmed','pass1'),('sara','pass2')");

$u = $_GET['username'] ?? '';
$m = $_GET['mode'] ?? 'safe';
$rows = [];
$q = '';
$err = '';

if ($u !== '') {
    if ($m === 'unsafe') {
        $q = "SELECT * FROM users WHERE username = '$u'";
        try { $rows = $pdo->query($q)->fetchAll(PDO::FETCH_ASSOC); }
        catch (PDOException $e) { $err = $e->getMessage(); }
    } else {
        $q = "SELECT * FROM users WHERE username = :u";
        try {
            $s = $pdo->prepare($q);
            $s->execute([':u' => $u]);
            $rows = $s->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { $err = $e->getMessage(); }
    }
}

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>sqli_lab</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Consolas, Monaco, monospace; background: #0a0e14; color: #b3b1ad; font-size: 13px; }
.topbar { background: #131721; padding: 10px 20px; border-bottom: 1px solid #1f2430; }
.topbar h1 { font-size: 14px; color: #f66565; font-weight: normal; letter-spacing: 1px; }
.wrap { padding: 20px; max-width: 960px; }
.box { background: #0d1117; border: 1px solid #1f2430; margin-bottom: 14px; }
.head { padding: 8px 12px; background: #131721; border-bottom: 1px solid #1f2430; color: #e6b450; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; }
.body { padding: 14px; }
.row { display: flex; gap: 10px; margin-bottom: 12px; align-items: center; }
.row input { flex: 1; background: #0a0e14; color: #b3b1ad; border: 1px solid #1f2430; padding: 8px 10px; font-family: inherit; font-size: 13px; outline: none; }
.row input:focus { border-color: #1f6feb; }
.row select { background: #131721; color: #ff7733; border: 1px solid #1f2430; padding: 8px 10px; font-family: inherit; font-size: 13px; outline: none; }
.row button { background: #1f6feb; color: #fff; border: none; padding: 8px 20px; font-family: inherit; cursor: pointer; font-size: 13px; }
.payloads { display: flex; flex-wrap: wrap; gap: 6px; }
.payloads button { background: #1f2430; color: #b3b1ad; border: 1px solid #2a3140; padding: 5px 12px; font-family: inherit; font-size: 11px; cursor: pointer; }
.payloads button:hover { border-color: #e6b450; color: #e6b450; }
pre { background: #0a0e14; padding: 10px; font-size: 12px; color: #7dbb6f; overflow-x: auto; border: 1px solid #1f2430; }
pre.bad { color: #f66565; }
table { width: 100%; border-collapse: collapse; font-size: 12px; }
th { background: #131721; color: #626a73; text-align: left; padding: 8px 10px; font-weight: normal; border-bottom: 1px solid #1f2430; text-transform: uppercase; font-size: 10px; letter-spacing: 1px; }
td { padding: 8px 10px; border-bottom: 1px solid #1f2430; }
.state { padding: 3px 10px; font-size: 11px; }
.state.s { background: rgba(125,187,111,.15); color: #7dbb6f; }
.state.u { background: rgba(246,101,101,.15); color: #f66565; }
.none { color: #626a73; font-style: italic; }
</style>
</head>
<body>

<div class="topbar"><h1>sqli_lab</h1></div>

<div class="wrap">

<div class="box">
  <div class="head">input</div>
  <div class="body">
    <form method="GET">
      <div class="row">
        <input type="text" name="username" value="<?= h($u) ?>" placeholder="username" autofocus>
        <select name="mode">
          <option value="safe" <?= $m === 'safe' ? 'selected' : '' ?>>SAFE</option>
          <option value="unsafe" <?= $m === 'unsafe' ? 'selected' : '' ?>>UNSAFE</option>
        </select>
        <button type="submit">RUN</button>
      </div>
    </form>
    <div class="payloads">
      <button data-p="admin">admin</button>
      <button data-p="' OR '1'='1">' OR '1'='1</button>
      <button data-p="' OR 1=1 --">' OR 1=1 --</button>
      <button data-p="' UNION SELECT 1,username,password FROM users --">' UNION SELECT ...</button>
      <button data-p="'; DROP TABLE users --">'; DROP TABLE</button>
    </div>
  </div>
</div>

<?php if ($u !== ''): ?>
<div class="box">
  <div class="head">
    query
    <span class="state <?= $m === 'safe' ? 's' : 'u' ?>"><?= $m === 'safe' ? 'prepared' : 'concatenated' ?></span>
  </div>
  <div class="body">
    <pre class="<?= $m === 'unsafe' ? 'bad' : '' ?>"><?= h($q) ?></pre>
    <?php if ($err): ?>
      <pre class="bad" style="margin-top:10px">ERROR: <?= h($err) ?></pre>
    <?php endif; ?>
  </div>
</div>

<div class="box">
  <div class="head">result · <?= count($rows) ?> row<?= count($rows) === 1 ? '' : 's' ?></div>
  <div class="body">
    <?php if (count($rows) === 0): ?>
      <p class="none">no rows returned</p>
    <?php else: ?>
      <table>
        <tr><th>id</th><th>username</th><th>password</th></tr>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= h($r['id'] ?? '?') ?></td>
            <td><?= h($r['username'] ?? '?') ?></td>
            <td><?= h($r['password'] ?? '?') ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>

</div>

<script>
document.querySelectorAll('.payloads button').forEach(function(b) {
  b.addEventListener('click', function() {
    document.querySelector('input[name=username]').value = b.dataset.p;
  });
});
</script>

</body>
</html>