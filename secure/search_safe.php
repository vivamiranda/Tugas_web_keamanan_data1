<?php
// web_keamanan_data/search_safe.php (SAFE version)
include 'koneksi.php'; // Menggunakan koneksi mysqli Anda

$q = trim((string)($_GET['q'] ?? ''));
$results = [];
$error = null;

if ($q !== '') {
    try {
        // SAFE: prepared statement (mysqli)
        $sql = "SELECT c.id, u.username, u.fullname, c.comment_text, c.created_at
                FROM comments c
                LEFT JOIN sqli_users_safe u ON c.user_id = u.id
                WHERE LOWER(c.comment_text) LIKE ? 
                   OR LOWER(u.username) LIKE ? 
                   OR LOWER(u.fullname) LIKE ?
                ORDER BY c.created_at DESC
                LIMIT 200";
        
        $stmt = $conn->prepare($sql);
        $like = '%' . mb_strtolower($q, 'UTF-8') . '%';
        // Bind 3 parameter
        $stmt->bind_param("sss", $like, $like, $like);
        $stmt->execute();
        $result = $stmt->get_result();
        $results = $result->fetch_all(MYSQLI_ASSOC);

    } catch (Exception $e) {
        $error = 'Terjadi kesalahan saat mencari. Coba lagi.';
    }
}

// helper: safely escape and optionally highlight the query
function safe_highlight(string $text, string $query): string {
    $escaped = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    if ($query === '') return nl2br($escaped);
    
    $safe_q = htmlspecialchars($query, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $pattern = '/' . preg_quote($safe_q, '/') . '/iu'; // case-insensitive, unicode
    $highlighted = preg_replace($pattern, '<mark>$0</mark>', $escaped);
    return nl2br($highlighted);
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Search Comments — SAFE</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Styling ini diambil dari xss-main agar cepat, tidak menggunakan tailwind */
    body { background: linear-gradient(120deg,#f8fafc 0%, #eef6ff 100%); min-height:100vh; }
    .search-card { max-width:980px; margin:42px auto; border-radius:12px; box-shadow:0 10px 30px rgba(15,23,42,0.06); }
    .brand { width:56px; height:56px; border-radius:10px; display:inline-flex; align-items:center; justify-content:center; background:#fff; box-shadow:0 4px 12px rgba(16,24,40,0.06); font-weight:700; color:#0d6efd; }
    .comment { padding:12px; border-radius:8px; background:#fff; box-shadow:0 6px 18px rgba(15,23,42,0.03); margin-bottom:12px; }
    .meta { color:#6c757d; font-size:.9rem; }
    .note { font-size:.85rem; color:#6c757d; }
    .safe-badge { font-size:.75rem; background:#e6f7ff; color:#055160; padding:4px 8px; border-radius:999px; }
    mark { background:#ffe58f; padding:0 .15rem; border-radius:.15rem; }
    .count-badge { font-weight:600; color:#495057; }
  </style>
</head>
<body>
  <div class="card search-card">
    <div class="card-body p-4">
      <div class="d-flex align-items-center mb-3">
        <div class="brand me-3">SAFE</div>
        <div>
          <h4 class="mb-0">Search Komentar (SAFE)</h4>
          <div class="note">Versi aman: prepared statements + escaping.</div>
        </div>
        <div class="ms-auto">
          <span class="safe-badge">SAFE</span>
            <a class="btn btn-outline-warning btn-sm" href="index.php">Kembali</a>
        </div>
      </div>

      <form class="row g-2 align-items-center" method="get" action="">
        <div class="col-md-9">
           <input name="q" class="form-control" placeholder="Cari komentar atau username..." value="<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>" autofocus>
        </div>
        <div class="col-md-3 d-grid">
          <button class="btn btn-success" type="submit">Search</button>
        </div>
      </form>

      <?php if ($q !== ''): ?>
        <hr class="my-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="mb-0">Hasil untuk: 
                <small class="text-muted"><?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?></small>
            </h5>
            <div class="note">Menampilkan komentar atau username (case-insensitive).</div>
          </div>
          <div class="text-end">
            <span class="count-badge"><?php echo count($results); ?> hasil</span>
          </div>
        </div>

        <?php if ($error): ?>
          <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (empty($results)): ?>
          <div class="alert alert-info">Tidak ada hasil untuk pencarian ini.</div>
        <?php else: ?>
          <div>
            <?php foreach ($results as $r): ?>
              <div class="comment">
                <div class="d-flex justify-content-between">
                  <div>
                    <strong><?php echo htmlspecialchars($r['username'] ?? ($r['fullname'] ?? 'Guest')); ?></strong>
                    <div class="meta"><?php echo htmlspecialchars($r['created_at'] ?? ''); ?></div>
                  </div>
                  <div>
                    <a href="<?php echo BASE_URL; ?>Artikel/Artikel1.php?id=<?php echo $r['id'] ?? 1; ?>&mode=safe" class="btn btn-sm btn-outline-secondary">View Post</a>
                  </div>
                </div>

                <div class="mt-2 text-break">
                  <?php echo safe_highlight((string)$r['comment_text'], $q); ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      <?php endif; ?>

    </div>

    <div class="card-footer text-muted small">
      Catatan: file ini **aman** — menggunakan prepared statements dan escaping output.
    </div>
  </div>
</body>
</html>