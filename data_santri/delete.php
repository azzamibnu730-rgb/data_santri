<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Metode tidak diizinkan.');
}

require __DIR__ . '/csrf.php';

$token = isset($_POST['csrf_token']) && is_string($_POST['csrf_token'])
    ? $_POST['csrf_token']
    : '';
if ($token === '' || !hash_equals(csrf_token(), $token)) {
    http_response_code(403);
    exit('Permintaan tidak valid. Muat ulang halaman lalu coba lagi.');
}

$entities = [
    'santri' => ['table' => 'tb_santri', 'key' => 'no_induk', 'type' => 's', 'page' => 'data_santri.php'],
    'ustadz' => ['table' => 'tb_ustadz', 'key' => 'nip', 'type' => 's', 'page' => 'data_ustadz.php'],
    'buku' => ['table' => 'tb_buku', 'key' => 'isbn', 'type' => 'i', 'page' => 'data_buku.php'],
    'mapel' => ['table' => 'tb_mapel', 'key' => 'no_mapel', 'type' => 'i', 'page' => 'data_pelajaran.php'],
];

$entityName = isset($_POST['entity']) && is_string($_POST['entity'])
    ? $_POST['entity']
    : '';
$id = isset($_POST['id']) && is_string($_POST['id'])
    ? trim($_POST['id'])
    : '';
if (!isset($entities[$entityName]) || $id === '') {
    http_response_code(422);
    exit('Data yang akan dihapus tidak valid.');
}

$entity = $entities[$entityName];
if ($entity['type'] === 'i') {
    $integerId = filter_var($id, FILTER_VALIDATE_INT);
    if ($integerId === false || $integerId < 1) {
        http_response_code(422);
        exit('ID yang akan dihapus tidak valid.');
    }
    $id = $integerId;
}

require __DIR__ . '/connection.php';

try {
    $statement = $conn->prepare(
        'DELETE FROM ' . $entity['table'] . ' WHERE ' . $entity['key'] . ' = ?'
    );
    $statement->bind_param($entity['type'], $id);
    $statement->execute();
} catch (mysqli_sql_exception $exception) {
    error_log('Gagal menghapus data ' . $entityName . ': ' . $exception->getMessage());
    http_response_code(500);
    exit('Data gagal dihapus. Silakan coba lagi.');
}

$status = $statement->affected_rows > 0 ? 'deleted' : 'not_found';
header('Location: ' . $entity['page'] . '?delete=' . $status);
exit;
