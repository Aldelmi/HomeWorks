<?php
$upload_dir = __DIR__ . "/uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$msg = "";
$kind = "";
$saved = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_FILES['uploaded_file']) || $_FILES['uploaded_file']['error'] !== UPLOAD_ERR_OK) {
        $msg = "no file received or upload error";
        $kind = "err";
    } else {
        $file = $_FILES['uploaded_file'];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        $mime_map = [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'pdf'  => 'application/pdf',
        ];

        if ($file['size'] > 2 * 1024 * 1024) {
            $msg = "file exceeds 2MB limit";
            $kind = "err";
        } elseif (!isset($mime_map[$ext])) {
            $msg = "extension .$ext rejected";
            $kind = "err";
        } else {
            $real_mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file['tmp_name']);

            if ($real_mime !== $mime_map[$ext]) {
                $msg = "mime mismatch. detected $real_mime, expected " . $mime_map[$ext];
                $kind = "err";
            } else {
                $new_name = bin2hex(random_bytes(16)) . "." . $ext;
                if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_name)) {
                    $msg = "upload accepted";
                    $kind = "ok";
                    $saved = $new_name;
                } else {
                    $msg = "could not write to disk. check uploads/ permissions";
                    $kind = "err";
                }
            }
        }
    }
}

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>file_gate</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Consolas, Monaco, monospace; background: #0a0e14; color: #b3b1ad; font-size: 13px; }
.topbar { background: #131721; padding: 10px 20px; border-bottom: 1px solid #1f2430; }
.topbar h1 { font-size: 14px; color: #39bae6; font-weight: normal; letter-spacing: 1px; }
.wrap { padding: 20px; max-width: 620px; }
.box { background: #0d1117; border: 1px solid #1f2430; margin-bottom: 14px; }
.head { padding: 8px 12px; background: #131721; border-bottom: 1px solid #1f2430; color: #e6b450; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; }
.body { padding: 16px; }
.specs { font-size: 11px; color: #626a73; line-height: 1.8; margin-bottom: 14px; }
.specs b { color: #b3b1ad; font-weight: normal; }
.drop { border: 1px dashed #2a3140; padding: 20px; text-align: center; margin-bottom: 12px; }
.drop input[type=file] { color: #b3b1ad; font-family: inherit; font-size: 12px; }
button { background: #1f6feb; color: #fff; border: none; padding: 9px 24px; font-family: inherit; cursor: pointer; font-size: 13px; letter-spacing: 1px; }
button:active { background: #1858c4; }
.alert { padding: 12px 14px; font-size: 12px; margin-bottom: 12px; border-left: 3px solid; }
.alert.ok  { background: rgba(125,187,111,.08); border-color: #7dbb6f; color: #7dbb6f; }
.alert.err { background: rgba(246,101,101,.08); border-color: #f66565; color: #f66565; }
.saved { background: #0a0e14; border: 1px solid #1f2430; padding: 10px; font-size: 12px; color: #7dbb6f; word-break: break-all; }
.saved span { color: #626a73; }
</style>
</head>
<body>

<div class="topbar"><h1>file_gate</h1></div>

<div class="wrap">

<div class="box">
  <div class="head">upload</div>
  <div class="body">
    <div class="specs">
      <b>accepts:</b> jpg · jpeg · png · pdf<br>
      <b>max size:</b> 2 MB<br>
      <b>checks:</b> size → extension → real mime → random rename
    </div>

    <?php if ($msg): ?>
      <div class="alert <?= h($kind) ?>"><?= h($msg) ?></div>
    <?php endif; ?>

    <?php if ($saved): ?>
      <div class="saved"><span>stored as:</span> <?= h($saved) ?></div>
      <br>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <div class="drop">
        <input type="file" name="uploaded_file" required>
      </div>
      <button type="submit">UPLOAD</button>
    </form>
  </div>
</div>

</div>

</body>
</html>