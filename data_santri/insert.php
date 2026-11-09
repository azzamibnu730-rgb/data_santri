<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Metode tidak diizinkan.');
}

require __DIR__ . '/connection.php';

$data = [
    'no_induk' => trim((string) ($_POST['no_induk'] ?? '')),
    'nama_santri' => trim((string) ($_POST['nama_santri'] ?? '')),
    'kelamin' => trim((string) ($_POST['kelamin'] ?? 'L')),
    'tempat_lahir' => trim((string) ($_POST['tempat_lahir'] ?? $_POST['tmpt_lahir'] ?? '')),
    'tgl_lahir' => trim((string) ($_POST['tgl_lahir'] ?? '')),
    'alamat' => trim((string) ($_POST['alamat'] ?? '')),
    'no_telp' => trim((string) ($_POST['no_telp'] ?? $_POST['telp_santri'] ?? '')),
    'nama_wali' => trim((string) ($_POST['nama_wali'] ?? '')),
    'telp_wali' => trim((string) ($_POST['telp_wali'] ?? '')),
];

$requiredFields = [
    'no_induk',
    'nama_santri',
    'tempat_lahir',
    'tgl_lahir',
    'alamat',
    'nama_wali',
];

foreach ($requiredFields as $field) {
    if ($data[$field] === '') {
        http_response_code(422);
        exit('Data wajib belum lengkap. <a href="index.html">Kembali ke formulir</a>');
    }
}

$data['kelamin'] = strtoupper($data['kelamin']);
if (!in_array($data['kelamin'], ['L', 'P'], true)) {
    http_response_code(422);
    exit('Jenis kelamin tidak valid. <a href="index.html">Kembali ke formulir</a>');
}

$birthDate = DateTime::createFromFormat('Y-m-d', $data['tgl_lahir']);
if (!$birthDate || $birthDate->format('Y-m-d') !== $data['tgl_lahir']) {
    http_response_code(422);
    exit('Tanggal lahir tidak valid. <a href="index.html">Kembali ke formulir</a>');
}

$sql = 'INSERT INTO tb_santri
    (no_induk, nama_santri, kelamin, tempat_lahir, tgl_lahir, alamat, no_telp, nama_wali, telp_wali)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';

try {
    $statement = $conn->prepare($sql);
    $noTelp = $data['no_telp'] !== '' ? $data['no_telp'] : null;
    $telpWali = $data['telp_wali'] !== '' ? $data['telp_wali'] : null;
    $statement->bind_param(
        'sssssssss',
        $data['no_induk'],
        $data['nama_santri'],
        $data['kelamin'],
        $data['tempat_lahir'],
        $data['tgl_lahir'],
        $data['alamat'],
        $noTelp,
        $data['nama_wali'],
        $telpWali
    );
    $statement->execute();
} catch (mysqli_sql_exception $exception) {
    error_log($exception->getMessage());
    http_response_code(500);
    exit('Data gagal disimpan. Pastikan database dan tabel sudah dibuat, serta nomor induk belum digunakan.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Santri Tersimpan</title>
</head>
<body>
  <h1>Data santri berhasil disimpan.</h1>
  <p><a href="index.html">Kembali ke formulir</a></p>
</body>
</html>