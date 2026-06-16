<?php
/**
 * Talemwa — Server Diagnostic Page
 * Visit: roberttalemwa.online/diagnostic.php
 * DELETE THIS FILE after debugging is complete.
 */

// Test asset file existence on disk
$docRoot   = $_SERVER['DOCUMENT_ROOT'] ?? 'unknown';
$scriptDir = dirname(__FILE__);

$checks = [
    'assets/css/app.css'    => $scriptDir . '/assets/css/app.css',
    'assets/js/site.js'     => $scriptDir . '/assets/js/site.js',
    'partials/head.php'     => $scriptDir . '/partials/head.php',
    'partials/nav.php'      => $scriptDir . '/partials/nav.php',
    'partials/footer.php'   => $scriptDir . '/partials/footer.php',
    'pages/home.php'        => $scriptDir . '/pages/home.php',
    '.htaccess'             => $scriptDir . '/.htaccess',
];

// Try to read app.css to check size
$cssPath = $scriptDir . '/assets/css/app.css';
$cssSize = file_exists($cssPath) ? filesize($cssPath) : 0;
$cssReadable = is_readable($cssPath);

// PHP info
$phpVersion    = PHP_VERSION;
$phpSapi       = PHP_SAPI;
$modRewrite    = in_array('mod_rewrite', apache_get_modules() ?? []) ? 'Enabled' : 'Not detected (may still work via FastCGI)';
$extensions    = get_loaded_extensions();
$pdo           = in_array('pdo_sqlite', $extensions);
$opcache       = in_array('Zend OPcache', $extensions) || function_exists('opcache_get_status');

// Server vars
$serverSoft  = $_SERVER['SERVER_SOFTWARE'] ?? 'unknown';
$requestUri  = $_SERVER['REQUEST_URI'] ?? '';
$serverName  = $_SERVER['SERVER_NAME'] ?? '';

function badge(bool $ok, string $yes = 'OK', string $no = 'FAIL'): string {
    $color = $ok ? '#16a34a' : '#dc2626';
    $label = $ok ? $yes : $no;
    return "<span style='background:{$color};color:#fff;padding:2px 10px;border-radius:999px;font-size:12px;font-weight:600'>{$label}</span>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Talemwa — Server Diagnostic</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: system-ui, sans-serif; background: #f1f5f9; color: #1e293b; padding: 32px 16px; }
  .wrap { max-width: 800px; margin: 0 auto; }
  h1 { font-size: 22px; font-weight: 700; margin-bottom: 4px; color: #0f172a; }
  .subtitle { font-size: 13px; color: #64748b; margin-bottom: 24px; }
  .card { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 20px 24px; margin-bottom: 16px; }
  .card h2 { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #64748b; margin-bottom: 14px; }
  table { width: 100%; border-collapse: collapse; font-size: 14px; }
  td { padding: 8px 6px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
  td:first-child { color: #475569; width: 55%; word-break: break-all; font-family: monospace; font-size: 13px; }
  td:last-child { text-align: right; }
  tr:last-child td { border-bottom: none; }
  .ok   { color: #16a34a; font-weight: 600; }
  .fail { color: #dc2626; font-weight: 600; }
  .warn { color: #d97706; font-weight: 600; }
  .code { font-family: monospace; font-size: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px 14px; white-space: pre-wrap; word-break: break-all; color: #334155; margin-top: 8px; }
  .section-note { font-size: 12px; color: #94a3b8; margin-top: 10px; }
  #fetch-results td:first-child { font-family: monospace; }
  .tag-red { background:#fef2f2;color:#dc2626;border:1px solid #fecaca;padding:2px 8px;border-radius:4px;font-size:12px;font-weight:600; }
  .tag-grn { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;padding:2px 8px;border-radius:4px;font-size:12px;font-weight:600; }
</style>
</head>
<body>
<div class="wrap">

  <h1>🔍 Talemwa Server Diagnostic</h1>
  <p class="subtitle">Visit this page on the VPS to understand what's broken. <strong>Delete this file after debugging.</strong></p>

  <!-- PHP & Server -->
  <div class="card">
    <h2>PHP &amp; Server</h2>
    <table>
      <tr><td>PHP Version</td><td><?= htmlspecialchars($phpVersion) ?></td></tr>
      <tr><td>SAPI</td><td><?= htmlspecialchars($phpSapi) ?></td></tr>
      <tr><td>Server Software</td><td><?= htmlspecialchars($serverSoft) ?></td></tr>
      <tr><td>Server Name</td><td><?= htmlspecialchars($serverName) ?></td></tr>
      <tr><td>Document Root</td><td><?= htmlspecialchars($docRoot) ?></td></tr>
      <tr><td>__FILE__ (this script)</td><td><?= htmlspecialchars(__FILE__) ?></td></tr>
      <tr><td>Request URI</td><td><?= htmlspecialchars($requestUri) ?></td></tr>
      <tr><td>PDO SQLite extension</td><td><?= badge($pdo) ?></td></tr>
      <tr><td>OPcache</td><td><?= badge($opcache, 'Enabled', 'Disabled') ?></td></tr>
      <tr><td>mod_rewrite / Apache modules</td><td class="warn"><?= htmlspecialchars($modRewrite) ?></td></tr>
    </table>
  </div>

  <!-- File existence on disk -->
  <div class="card">
    <h2>Files — Disk Existence Check</h2>
    <p style="font-size:13px;color:#64748b;margin-bottom:12px">Checks whether each file physically exists from PHP's perspective. Base dir: <code><?= htmlspecialchars($scriptDir) ?></code></p>
    <table>
      <?php foreach ($checks as $label => $path): ?>
      <tr>
        <td><?= htmlspecialchars($label) ?></td>
        <td><?= badge(file_exists($path)) ?></td>
      </tr>
      <?php endforeach; ?>
      <tr>
        <td>assets/css/app.css — size</td>
        <td><?= $cssSize > 0 ? number_format($cssSize) . ' bytes' : '<span class="fail">0 / not found</span>' ?></td>
      </tr>
      <tr>
        <td>assets/css/app.css — readable</td>
        <td><?= badge($cssReadable) ?></td>
      </tr>
    </table>
  </div>

  <!-- HTTP fetch test (client-side JS) -->
  <div class="card">
    <h2>HTTP Fetch Test — Can Browser Load These URLs?</h2>
    <p style="font-size:13px;color:#64748b;margin-bottom:12px">JavaScript fetches each URL and reports the HTTP status code. A 200 means the file is being served correctly.</p>
    <table id="fetch-results">
      <tr><td>/assets/css/app.css</td><td id="r-css"><em>testing…</em></td></tr>
      <tr><td>/assets/js/site.js</td><td id="r-js"><em>testing…</em></td></tr>
      <tr><td>https://cdn.tailwindcss.com</td><td id="r-tw"><em>testing…</em></td></tr>
      <tr><td>https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js</td><td id="r-alpine"><em>testing…</em></td></tr>
      <tr><td>https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css</td><td id="r-tabler"><em>testing…</em></td></tr>
      <tr><td>https://fonts.googleapis.com/css2?family=Inter</td><td id="r-fonts"><em>testing…</em></td></tr>
    </table>
  </div>

  <!-- .htaccess content -->
  <div class="card">
    <h2>.htaccess Content</h2>
    <?php
    $htFile = $scriptDir . '/.htaccess';
    if (file_exists($htFile)):
      echo '<div class="code">' . htmlspecialchars(file_get_contents($htFile)) . '</div>';
    else:
      echo '<p class="fail">.htaccess not found at ' . htmlspecialchars($htFile) . '</p>';
    endif;
    ?>
  </div>

  <!-- PHP include test -->
  <div class="card">
    <h2>PHP Include Test — head.php</h2>
    <?php
    $headFile = $scriptDir . '/partials/head.php';
    if (!file_exists($headFile)):
      echo '<p class="fail">partials/head.php does not exist on disk.</p>';
    else:
      $headContent = file_get_contents($headFile);
      // Check for key strings
      $hasTailwindCdn = str_contains($headContent, 'cdn.tailwindcss.com');
      $hasLocalCss    = str_contains($headContent, '/assets/css/app.css');
      $hasAlpine      = str_contains($headContent, 'alpinejs');
      $hasTabler      = str_contains($headContent, 'tabler-icons');
    ?>
    <table>
      <tr><td>head.php exists</td><td><?= badge(true) ?></td></tr>
      <tr><td>References cdn.tailwindcss.com</td><td><?= badge($hasTailwindCdn, 'Yes ✓', 'No — still using local CSS!') ?></td></tr>
      <tr><td>References /assets/css/app.css (old)</td><td><?= badge(!$hasLocalCss, 'Not present ✓', 'Still present — remove this!') ?></td></tr>
      <tr><td>References Alpine.js</td><td><?= badge($hasAlpine) ?></td></tr>
      <tr><td>References Tabler Icons CDN</td><td><?= badge($hasTabler) ?></td></tr>
    </table>
    <p class="section-note">If "Still using local CSS" shows FAIL, the old code is deployed — you need to upload the updated head.php from the repo.</p>
    <?php endif; ?>
  </div>

  <!-- Permissions -->
  <div class="card">
    <h2>File Permissions</h2>
    <table>
      <?php
      $permChecks = [
        $scriptDir,
        $scriptDir . '/assets',
        $scriptDir . '/assets/css',
        $scriptDir . '/assets/css/app.css',
        $scriptDir . '/partials',
        $scriptDir . '/pages',
      ];
      foreach ($permChecks as $p):
        if (!file_exists($p)) { echo "<tr><td>" . htmlspecialchars(str_replace($scriptDir.'/','',$p)) . "</td><td class='fail'>Missing</td></tr>"; continue; }
        $perms = substr(sprintf('%o', fileperms($p)), -4);
        $readable = is_readable($p);
      ?>
      <tr>
        <td><?= htmlspecialchars(str_replace($scriptDir . '/', '', $p) ?: basename($p)) ?></td>
        <td><?= $perms ?> &nbsp; <?= badge($readable, 'Readable', 'NOT readable') ?></td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>

  <!-- Loaded extensions -->
  <div class="card">
    <h2>Loaded PHP Extensions (key ones)</h2>
    <table>
      <?php
      $key = ['pdo','pdo_sqlite','sqlite3','curl','json','openssl','mbstring','fileinfo','zip'];
      foreach ($key as $ext):
        $loaded = extension_loaded($ext);
      ?>
      <tr><td><?= $ext ?></td><td><?= badge($loaded, 'Loaded', 'Missing') ?></td></tr>
      <?php endforeach; ?>
    </table>
  </div>

  <p style="font-size:12px;color:#94a3b8;margin-top:24px;text-align:center">
    Talemwa Diagnostic · Built by Thirdsan Enterprises Ltd · <strong>Delete this file in production</strong>
  </p>
</div>

<script>
async function probe(url, id) {
  const el = document.getElementById(id);
  try {
    const r = await fetch(url, { method: 'HEAD', cache: 'no-store' });
    const ok = r.status >= 200 && r.status < 400;
    el.innerHTML = ok
      ? '<span class="tag-grn">HTTP ' + r.status + ' OK</span>'
      : '<span class="tag-red">HTTP ' + r.status + ' FAIL</span>';
  } catch(e) {
    el.innerHTML = '<span class="tag-red">Network error / CORS blocked</span>';
  }
}

probe('/assets/css/app.css', 'r-css');
probe('/assets/js/site.js', 'r-js');
probe('https://cdn.tailwindcss.com', 'r-tw');
probe('https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js', 'r-alpine');
probe('https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css', 'r-tabler');
probe('https://fonts.googleapis.com/css2?family=Inter:wght@400&display=swap', 'r-fonts');
</script>
</body>
</html>
