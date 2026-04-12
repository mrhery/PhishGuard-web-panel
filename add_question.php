<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$success_message = null;
$error_message = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    try {
        $question = $_POST['question'];
        $options = $_POST['options'];
        $correct = $_POST['correct'];

        // insert question
        $stmt = $pdo->prepare("INSERT INTO questions (question) VALUES (?)");
        $stmt->execute([$question]);

        $qid = $pdo->lastInsertId();

        // insert options
        foreach ($options as $i => $opt) {
            $is_correct = ($i == $correct) ? 1 : 0;

            $stmt = $pdo->prepare("
                INSERT INTO options (question_id, option_text, is_correct)
                VALUES (?, ?, ?)
            ");

            $stmt->execute([$qid, $opt, $is_correct]);
        }

        $success_message = "Question added successfully!";

    } catch (Exception $e) {
        $error_message = "Something went wrong!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Question</title>

<link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.0/dist/sweetalert2.min.css">

<style>
/* ===== SAME CSS AS ADMIN PANEL ===== */
:root {
    --primary: #1d4ed8;
    --sidebar: #0f172a;
    --bg: #f1f5f9;
    --white: #ffffff;
    --border: #e5e7eb;
    --text: #111827;
    --text-light: #6b7280;
    --success: #16a34a;
    --danger: #dc2626;
}

body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: var(--bg);
    display: flex;
}

.nav-bar {
    width: 220px;
    height: 100vh;
    background: var(--sidebar);
    display: flex;
    flex-direction: column;
    padding: 20px 10px;
    position: fixed;
}

.nav-bar button {
    background: none;
    border: none;
    color: #cbd5f5;
    text-align: left;
    padding: 12px 15px;
    margin: 5px 0;
    border-radius: 8px;
    cursor: pointer;
}

.nav-bar button:hover {
    background: #1e293b;
    color: white;
}

.nav-bar .active {
    background: var(--primary);
    color: white;
}

.nav-bar button i {
    margin-right: 10px;
}

.container {
    margin-left: 240px;
    padding: 30px;
    width: 100%;
}

.container h2 {
    margin-bottom: 5px;
}

.container h3 {
    margin-top: 25px;
    color: var(--text-light);
}

form {
    background: var(--white);
    padding: 20px;
    border-radius: 10px;
    border: 1px solid var(--border);
    margin-top: 20px;
}

form label {
    font-weight: 500;
    margin-top: 12px;
    display: block;
}

form input,
form select {
    width: 100%;
    padding: 10px;
    margin-top: 6px;
    border-radius: 6px;
    border: 1px solid var(--border);
}

form input:focus,
form select:focus {
    border-color: var(--primary);
    outline: none;
}

form button {
    margin-top: 20px;
    padding: 12px;
    background: var(--success);
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
}

form button:hover {
    opacity: 0.9;
}

.success, .error {
    margin-top: 15px;
    padding: 10px;
    border-radius: 6px;
}

.success {
    background: #ecfdf5;
    color: #065f46;
}

.error {
    background: #fef2f2;
    color: #7f1d1d;
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="nav-bar">
    <button onclick="window.location.href='admin_panel.php'">
        <i class="fas fa-user-cog"></i> Admin Panel
    </button>

    <button onclick="window.location.href='dashboard.php'">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </button>

    <button class="active">
        <i class="fas fa-question-circle"></i> Add Question
    </button>

    <button onclick="window.location.href='manage_questions.php'">
        <i class="fas fa-list"></i> Manage Questions
    </button>

    <button onclick="confirmLogout()">
        <i class="fas fa-sign-out-alt"></i> Logout
    </button>
</div>

<!-- CONTENT -->
<div class="container">

    <h2><i class="fas fa-plus"></i> Add Question</h2>

    <h3><i class="fas fa-edit"></i> Create New Question</h3>

    <form method="POST">

        <label>Question:</label>
        <input type="text" name="question" required placeholder="Enter question">

        <label>Option 1:</label>
        <input type="text" name="options[]" required placeholder="Option 1">

        <label>Option 2:</label>
        <input type="text" name="options[]" required placeholder="Option 2">

        <label>Option 3:</label>
        <input type="text" name="options[]" required placeholder="Option 3">

        <label>Option 4:</label>
        <input type="text" name="options[]" required placeholder="Option 4">

        <label>Correct Answer:</label>
        <select name="correct">
            <option value="0">Option 1</option>
            <option value="1">Option 2</option>
            <option value="2">Option 3</option>
            <option value="3">Option 4</option>
        </select>

        <button type="submit">
            <i class="fas fa-save"></i> Save Question
        </button>

    </form>

    <?php if ($success_message): ?>
        <p class="success"><i class="fas fa-check-circle"></i> <?= $success_message ?></p>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <p class="error"><i class="fas fa-times-circle"></i> <?= $error_message ?></p>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.0/dist/sweetalert2.all.min.js"></script>

<script>
function confirmLogout() {
    Swal.fire({
        title: 'Logout?',
        text: 'Anda pasti nak logout?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'logout.php';
        }
    });
}
</script>

</body>
</html>