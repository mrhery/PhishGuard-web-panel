<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

session_start();

/* ===== DATABASE CONFIG ===== */
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'phishing_simulator');

/* ===== SMTP CONFIG ===== */
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'unitteknologimaklumatpsmza@gmail.com');
define('SMTP_PASS', 'gxpjodfsbbuutbcv');
define('SMTP_PORT', 587);
define('SMTP_FROM', 'admin@phisingsimulator.com');
define('SMTP_FROM_NAME', 'Phishing Simulator');


/* ===== PDO CONNECTION (WAJIB ATAS) ===== */
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
    die("PDO connection failed: " . $e->getMessage());
}


/* ===== MYSQLI ===== */
$mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error) {
    $mysqli = null;
}


/* ===== AUTO CREATE TABLE ===== */
try {

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS questions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            question TEXT NOT NULL
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS options (
            id INT AUTO_INCREMENT PRIMARY KEY,
            question_id INT,
            option_text TEXT,
            is_correct TINYINT(1),
            FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
        )
    ");

} catch (PDOException $e) {
    die("Table error: " . $e->getMessage());
}


/* ===== BARU AMBIL DATA QUIZ ===== */
$questions = [];

$stmt = $pdo->query("SELECT * FROM questions");

while ($row = $stmt->fetch()) {

    $q = [
        'id' => $row['id'],
        'question' => $row['question'],
        'options' => [],
        'answer' => null
    ];

    $opt = $pdo->prepare("SELECT * FROM options WHERE question_id=?");
    $opt->execute([$row['id']]);

    while ($o = $opt->fetch()) {
        $q['options'][] = $o['option_text'];

        if ($o['is_correct'] == 1) {
            $q['answer'] = count($q['options']) - 1;
        }
    }

    $questions[] = $q;
}
?>