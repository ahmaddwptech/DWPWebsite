<?php

function db(): PDO
{
    static $pdo = null;
    if ($pdo) return $pdo;
    $c = cfg('db');
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $c['host'], $c['port'], $c['name'], $c['charset']);
    $pdo = new PDO($dsn, $c['user'], $c['pass'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    return $pdo;
}

/** Run a query and return all rows; returns [] if the database is unavailable (pages still render). */
function db_all(string $sql, array $params = []): array
{
    try {
        $st = db()->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    } catch (Throwable $e) {
        log_line('db.log', $e->getMessage());
        return [];
    }
}

function db_one(string $sql, array $params = []): ?array
{
    $rows = db_all($sql, $params);
    return $rows[0] ?? null;
}

function db_insert(string $table, array $row): int
{
    $cols = array_keys($row);
    $sql = sprintf(
        'INSERT INTO `%s` (%s) VALUES (%s)',
        $table,
        implode(',', array_map(fn($c) => "`$c`", $cols)),
        implode(',', array_fill(0, count($cols), '?'))
    );
    db()->prepare($sql)->execute(array_values($row));
    return (int) db()->lastInsertId();
}

function db_update(string $table, int $id, array $row): void
{
    $set = implode(',', array_map(fn($c) => "`$c` = ?", array_keys($row)));
    db()->prepare("UPDATE `$table` SET $set WHERE id = ?")->execute([...array_values($row), $id]);
}
