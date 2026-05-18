<?php
declare(strict_types=1);

mysqli_report(MYSQLI_REPORT_OFF);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dbHost = getenv('DMAN_DB_HOST') ?: 'localhost';
$dbUser = getenv('DMAN_DB_USER') ?: 'root';
$dbPass = getenv('DMAN_DB_PASS') ?: '';
$dbName = getenv('DMAN_DB_NAME') ?: 'dman_logistica';

$conn = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);
$dbReady = $conn instanceof mysqli && !$conn->connect_error;

if ($dbReady) {
    $conn->set_charset('utf8mb4');

    if (isset($_SESSION['id_usuario'])) {
        $conn->query('SET @current_user_id = ' . (int) $_SESSION['id_usuario']);
    }
}

function dman_db_ready(): bool
{
    global $dbReady;
    return (bool) $dbReady;
}

function dman_fetch_all(string $sql, string $types = '', array $params = []): array
{
    global $conn;

    if (!($conn instanceof mysqli)) {
        return [];
    }

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return [];
    }

    if ($types !== '') {
        $bind = [$types];
        foreach ($params as &$value) {
            $bind[] = &$value;
        }
        call_user_func_array([$stmt, 'bind_param'], $bind);
    }

    if (!$stmt->execute()) {
        $stmt->close();
        return [];
    }

    $result = $stmt->get_result();
    $rows = [];
    if ($result instanceof mysqli_result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }

    $stmt->close();
    return $rows;
}

function dman_fetch_one(string $sql, string $types = '', array $params = []): ?array
{
    $rows = dman_fetch_all($sql, $types, $params);
    return $rows[0] ?? null;
}
