<?php
require __DIR__ . '/csrf.php';
require __DIR__ . '/record_schema.php';

$entityName = isset($_POST['entity']) && is_string($_POST['entity'])
    ? $_POST['entity']
    : (isset($_GET['entity']) && is_string($_GET['entity']) ? $_GET['entity'] : '');

if (!isset($entities[$entityName])) {
    http_response_code(422);
    exit('Jenis data yang akan ditambahkan tidak valid.');
}

$entity = $entities[$entityName];
$errors = [];
$values = [];
$id = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = isset($_POST['csrf_token']) && is_string($_POST['csrf_token'])
        ? $_POST['csrf_token']
        : '';
    if ($token === '' || !hash_equals(csrf_token(), $token)) {
        http_response_code(403);
        exit('Permintaan tidak valid. Muat ulang halaman lalu coba lagi.');
    }

    if (!$entity['key_auto']) {
        $rawId = $_POST['id'] ?? '';
        if (!is_string($rawId)) {
            $errors[] = $entity['key_label'] . ' tidak valid.';
        } else {
            $id = trim($rawId);
            if ($id === '') {
                $errors[] = $entity['key_label'] . ' wajib diisi.';
            } elseif (strlen($id) > 20) {
                $errors[] = $entity['key_label'] . ' maksimal 20 karakter.';
            }
        }
    }

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
            $values[$name] = $number;
        }
    }

    if ($errors === []) {
        require __DIR__ . '/connection.php';

        $columns = [];
        $placeholders = [];
        $types = '';
        $parameters = [];
        if (!$entity['key_auto']) {
            array_unshift($columns, $entity['key']);
            array_unshift($placeholders, '?');
            $types .= $entity['key_type'];
            $parameters[] = $id;
        }
        foreach ($values as $name => $value) {
            $columns[] = $name;
            $placeholders[] = '?';
            $types .= $entity['fields'][$name]['type'] === 'number' ? 'i' : 's';
            $parameters[] = $value;
        }

        try {
            $statement = $conn->prepare(
                'INSERT INTO ' . $entity['table'] . ' (' . implode(', ', $columns) . ') VALUES ('
                . implode(', ', $placeholders) . ')'
            );
            $bindParameters = [$types];
            foreach ($parameters as &$parameter) {
                $bindParameters[] = &$parameter;
            }
            unset($parameter);
            call_user_func_array([$statement, 'bind_param'], $bindParameters);
            $statement->execute();

            header('Location: ' . $entity['page'] . '?add=saved');
            exit;
        } catch (mysqli_sql_exception $exception) {
            error_log('Gagal menambahkan data ' . $entityName . ': ' . $exception->getMessage());
            if ($exception->getCode() === 1062) {
                $errors[] = $entity['key_label'] . ' sudah digunakan. Gunakan nilai yang berbeda.';
            } else {
                http_response_code(500);
                exit('Data gagal ditambahkan. Silakan coba lagi.');
            }
        }
    }
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
    <title>Tambah <?= $escape($entity['title']) ?> - Al Mahir</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="edit-page">
        <section class="content-section">
            <div class="card-header-teal">
                <h1>Tambah <?= $escape($entity['title']) ?></h1>
            </div>
            <?php foreach ($errors as $error): ?>
                <p class="edit-error"><?= $escape($error) ?></p>
            <?php endforeach; ?>

            <form action="add.php" method="post" class="edit-form">
                <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
                <input type="hidden" name="entity" value="<?= $escape($entityName) ?>">
                <?php if (!$entity['key_auto']): ?>
                    <label class="edit-field">
                        <span><?= $escape($entity['key_label']) ?></span>
                        <input type="text" name="id" value="<?= $escape($id) ?>" maxlength="20" required>
                    </label>
                <?php endif; ?>
                <?php foreach ($entity['fields'] as $name => $field): ?>
                    <label class="edit-field">
                        <span><?= $escape($field['label']) ?></span>
                        <?php if ($field['type'] === 'select'): ?>
                            <select name="<?= $escape($name) ?>" required>
                                <?php foreach ($field['options'] as $option => $label): ?>
                                    <option value="<?= $escape($option) ?>" <?= (string) ($values[$name] ?? '') === (string) $option ? 'selected' : '' ?>>
                                        <?= $escape($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php elseif ($field['type'] === 'textarea'): ?>
                            <textarea name="<?= $escape($name) ?>" <?= $field['required'] ? 'required' : '' ?><?= isset($field['max']) ? ' maxlength="' . (int) $field['max'] . '"' : '' ?>><?= $escape($values[$name] ?? '') ?></textarea>
                        <?php else: ?>
                            <input
                                type="<?= $escape($field['type']) ?>"
                                name="<?= $escape($name) ?>"
                                value="<?= $escape($values[$name] ?? '') ?>"
                                <?= $field['required'] ? 'required' : '' ?>
                                <?= isset($field['max']) ? 'maxlength="' . (int) $field['max'] . '"' : '' ?>
                                <?= isset($field['min']) ? 'min="' . (int) $field['min'] . '"' : '' ?>
                            >
                        <?php endif; ?>
                    </label>
                <?php endforeach; ?>
                <div class="edit-actions">
                    <button type="submit" class="btn-add-santri">Simpan Data</button>
                    <a href="<?= $escape($entity['page']) ?>" class="btn-cancel">Batal</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
