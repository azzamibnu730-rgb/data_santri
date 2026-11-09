<?php
require __DIR__ . '/csrf.php';
require __DIR__ . '/record_schema.php';

$entityName = isset($_POST['entity']) && is_string($_POST['entity'])
    ? $_POST['entity']
    : (isset($_GET['entity']) && is_string($_GET['entity']) ? $_GET['entity'] : '');
$id = isset($_POST['id']) && is_string($_POST['id'])
    ? trim($_POST['id'])
    : (isset($_GET['id']) && is_string($_GET['id']) ? trim($_GET['id']) : '');

if (!isset($entities[$entityName]) || $id === '') {
    http_response_code(422);
    exit('Data yang akan diedit tidak valid.');
}

$entity = $entities[$entityName];
if (in_array($entityName, ['buku', 'mapel'], true)) {
    $integerId = filter_var($id, FILTER_VALIDATE_INT);
    if ($integerId === false || $integerId < 1) {
        http_response_code(422);
        exit('ID yang akan diedit tidak valid.');
    }
    $id = (string) $integerId;
}

require __DIR__ . '/connection.php';

try {
    $select = $conn->prepare(
        'SELECT * FROM ' . $entity['table'] . ' WHERE ' . $entity['key'] . ' = ?'
    );
    $selectType = $entity['key_type'];
    $selectId = $selectType === 'i' ? (int) $id : $id;
    $select->bind_param($selectType, $selectId);
    $select->execute();
    $result = $select->get_result();
    $record = $result->fetch_assoc();

    if (!$record) {
        http_response_code(404);
        exit('Data tidak ditemukan atau sudah dihapus.');
    }

    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = isset($_POST['csrf_token']) && is_string($_POST['csrf_token'])
            ? $_POST['csrf_token']
            : '';
        if ($token === '' || !hash_equals(csrf_token(), $token)) {
            http_response_code(403);
            exit('Permintaan tidak valid. Muat ulang halaman lalu coba lagi.');
        }

        $values = [];
        foreach ($entity['fields'] as $name => $field) {
            $rawValue = $_POST[$name] ?? '';
            if (!is_string($rawValue)) {
                $errors[] = $field['label'] . ' tidak valid.';
                continue;
            }

            $value = trim($rawValue);
            $values[$name] = $value;
            if ($value === '' && !$field['required']) {
                $values[$name] = null;
                continue;
            }
            if ($value === '') {
                $errors[] = $field['label'] . ' wajib diisi.';
                continue;
            }
            if (isset($field['max']) && (function_exists('mb_strlen') ? mb_strlen($value) : strlen($value)) > $field['max']) {
                $errors[] = $field['label'] . ' maksimal ' . $field['max'] . ' karakter.';
                continue;
            }
            if ($field['type'] === 'date') {
                $date = DateTime::createFromFormat('!Y-m-d', $value);
                if (!$date || $date->format('Y-m-d') !== $value) {
                    $errors[] = $field['label'] . ' tidak valid.';
                    continue;
                }
            }
            if ($field['type'] === 'select' && !array_key_exists($value, $field['options'])) {
                $errors[] = $field['label'] . ' tidak valid.';
                continue;
            }
            if ($field['type'] === 'number') {
                $number = filter_var($value, FILTER_VALIDATE_INT);
                if ($number === false || $number < $field['min']) {
                    $errors[] = $field['label'] . ' harus berupa angka minimal ' . $field['min'] . '.';
                    continue;
                }
                $value = $number;
            }

            $values[$name] = $value;
        }

        if ($errors === []) {
            $setClauses = [];
            $types = '';
            $parameters = [];
            foreach ($values as $name => $value) {
                $setClauses[] = $name . ' = ?';
                $types .= $entity['fields'][$name]['type'] === 'number' ? 'i' : 's';
                $parameters[] = $value;
            }
            $types .= $selectType;
            $parameters[] = $selectId;
            $statement = $conn->prepare(
                'UPDATE ' . $entity['table'] . ' SET ' . implode(', ', $setClauses)
                . ' WHERE ' . $entity['key'] . ' = ?'
            );
            $bindParameters = [$types];
            foreach ($parameters as $index => &$parameter) {
                $bindParameters[] = &$parameter;
            }
            unset($parameter);
            call_user_func_array([$statement, 'bind_param'], $bindParameters);
            $statement->execute();

            header('Location: ' . $entity['page'] . '?edit=saved');
            exit;
        }

        foreach ($values as $name => $value) {
            $record[$name] = $value;
        }
    }
} catch (mysqli_sql_exception $exception) {
    error_log('Gagal memuat atau mengedit data ' . $entityName . ': ' . $exception->getMessage());
    http_response_code(500);
    exit('Data gagal diproses. Silakan coba lagi.');
}

$csrfToken = csrf_token();
$escape = static function ($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit <?= $escape($entity['title']) ?> - Al Mahir</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="edit-page">
        <section class="content-section">
            <div class="card-header-teal">
                <h1>Edit <?= $escape($entity['title']) ?></h1>
            </div>
            <?php foreach ($errors as $error): ?>
                <p class="edit-error"><?= $escape($error) ?></p>
            <?php endforeach; ?>

            <form action="edit.php" method="post" class="edit-form">
                <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
                <input type="hidden" name="entity" value="<?= $escape($entityName) ?>">
                <input type="hidden" name="id" value="<?= $escape($id) ?>">
                <?php foreach ($entity['fields'] as $name => $field): ?>
                    <label class="edit-field">
                        <span><?= $escape($field['label']) ?></span>
                        <?php if ($field['type'] === 'select'): ?>
                            <select name="<?= $escape($name) ?>" required>
                                <?php foreach ($field['options'] as $option => $label): ?>
                                    <option value="<?= $escape($option) ?>" <?= (string) $record[$name] === (string) $option ? 'selected' : '' ?>>
                                        <?= $escape($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php elseif ($field['type'] === 'textarea'): ?>
                            <textarea name="<?= $escape($name) ?>" <?= $field['required'] ? 'required' : '' ?><?= isset($field['max']) ? ' maxlength="' . (int) $field['max'] . '"' : '' ?>><?= $escape($record[$name]) ?></textarea>
                        <?php else: ?>
                            <input
                                type="<?= $escape($field['type']) ?>"
                                name="<?= $escape($name) ?>"
                                value="<?= $escape($record[$name]) ?>"
                                <?= $field['required'] ? 'required' : '' ?>
                                <?= isset($field['max']) ? 'maxlength="' . (int) $field['max'] . '"' : '' ?>
                                <?= isset($field['min']) ? 'min="' . (int) $field['min'] . '"' : '' ?>
                            >
                        <?php endif; ?>
                    </label>
                <?php endforeach; ?>
                <div class="edit-actions">
                    <button type="submit" class="btn-add-santri">Simpan Perubahan</button>
                    <a href="<?= $escape($entity['page']) ?>" class="btn-cancel">Batal</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
