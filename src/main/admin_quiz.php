<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

include 'db_connection.php';

$message = "";
$error = "";

// Handle Delete Question
if (isset($_GET['delete'])) {
    $questionId = intval($_GET['delete']);
    
    if ($conn->query("DELETE FROM QUIZ_QUESTION WHERE question_id = $questionId")) {
        $conn->query("INSERT INTO ADMIN_LOG (admin_username, action_type, table_name, record_id, action_details) 
                     VALUES ('admin', 'DELETE', 'QUIZ_QUESTION', $questionId, 'Deleted quiz question ID: $questionId')");
        $message = "Question deleted successfully!";
    } else {
        $error = "Error deleting question.";
    }
}

// Handle Create/Edit Question
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $questionText = trim($_POST['question_text']);
    $optionA = trim($_POST['option_a']);
    $optionB = trim($_POST['option_b']);
    $optionC = trim($_POST['option_c']);
    $optionD = trim($_POST['option_d']);
    $correctAnswer = $_POST['correct_answer'];
    $category = trim($_POST['category']);
    
    if (isset($_POST['question_id']) && !empty($_POST['question_id'])) {
        // Update existing question
        $questionId = intval($_POST['question_id']);
        
        $stmt = $conn->prepare("UPDATE QUIZ_QUESTION SET question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_answer = ?, category = ? WHERE question_id = ?");
        $stmt->bind_param("sssssssi", $questionText, $optionA, $optionB, $optionC, $optionD, $correctAnswer, $category, $questionId);
        
        if ($stmt->execute()) {
            $conn->query("INSERT INTO ADMIN_LOG (admin_username, action_type, table_name, record_id, action_details) 
                         VALUES ('admin', 'UPDATE', 'QUIZ_QUESTION', $questionId, 'Updated quiz question')");
            $message = "Question updated successfully!";
        } else {
            $error = "Error updating question.";
        }
    } else {
        // Create new question
        $stmt = $conn->prepare("INSERT INTO QUIZ_QUESTION (question_text, option_a, option_b, option_c, option_d, correct_answer, category) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $questionText, $optionA, $optionB, $optionC, $optionD, $correctAnswer, $category);
        
        if ($stmt->execute()) {
            $newId = $conn->insert_id;
            $conn->query("INSERT INTO ADMIN_LOG (admin_username, action_type, table_name, record_id, action_details) 
                         VALUES ('admin', 'CREATE', 'QUIZ_QUESTION', $newId, 'Created new quiz question')");
            $message = "Question created successfully!";
        } else {
            $error = "Error creating question.";
        }
    }
}

// Get all questions
$questions = $conn->query("SELECT * FROM QUIZ_QUESTION ORDER BY created_at DESC");

// Get question for editing if requested
$editQuestion = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $editQuestion = $conn->query("SELECT * FROM QUIZ_QUESTION WHERE question_id = $editId")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Quiz Questions - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <?php include 'admin_sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Manage Quiz Questions</h1>
                <div class="admin-user">
                    <i class="fas fa-user-shield"></i>
                    <span>Admin</span>
                </div>
            </header>

            <div class="admin-content">
                <?php if ($message): ?>
                    <div class="alert-admin alert-success-admin">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert-admin alert-error-admin">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <!-- Add/Edit Question Form -->
                <div class="admin-form">
                    <h2><?php echo $editQuestion ? 'Edit Question' : 'Add New Quiz Question'; ?></h2>
                    <form method="POST" action="">
                        <?php if ($editQuestion): ?>
                            <input type="hidden" name="question_id" value="<?php echo $editQuestion['question_id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-group-admin">
                            <label>Question Text *</label>
                            <textarea name="question_text" required style="min-height: 80px;"><?php echo $editQuestion['question_text'] ?? ''; ?></textarea>
                        </div>
                        
                        <div class="form-grid" style="margin-top: 1rem;">
                            <div class="form-group-admin">
                                <label>Option A *</label>
                                <input type="text" name="option_a" value="<?php echo $editQuestion['option_a'] ?? ''; ?>" required>
                            </div>
                            <div class="form-group-admin">
                                <label>Option B *</label>
                                <input type="text" name="option_b" value="<?php echo $editQuestion['option_b'] ?? ''; ?>" required>
                            </div>
                            <div class="form-group-admin">
                                <label>Option C *</label>
                                <input type="text" name="option_c" value="<?php echo $editQuestion['option_c'] ?? ''; ?>" required>
                            </div>
                            <div class="form-group-admin">
                                <label>Option D *</label>
                                <input type="text" name="option_d" value="<?php echo $editQuestion['option_d'] ?? ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-grid" style="margin-top: 1rem;">
                            <div class="form-group-admin">
                                <label>Correct Answer *</label>
                                <select name="correct_answer" required>
                                    <option value="A" <?php echo ($editQuestion && $editQuestion['correct_answer'] == 'A') ? 'selected' : ''; ?>>A</option>
                                    <option value="B" <?php echo ($editQuestion && $editQuestion['correct_answer'] == 'B') ? 'selected' : ''; ?>>B</option>
                                    <option value="C" <?php echo ($editQuestion && $editQuestion['correct_answer'] == 'C') ? 'selected' : ''; ?>>C</option>
                                    <option value="D" <?php echo ($editQuestion && $editQuestion['correct_answer'] == 'D') ? 'selected' : ''; ?>>D</option>
                                </select>
                            </div>
                            <div class="form-group-admin">
                                <label>Category</label>
                                <select name="category">
                                    <option value="general" <?php echo ($editQuestion && $editQuestion['category'] == 'general') ? 'selected' : ''; ?>>General</option>
                                    <option value="recycling" <?php echo ($editQuestion && $editQuestion['category'] == 'recycling') ? 'selected' : ''; ?>>Recycling</option>
                                    <option value="energy" <?php echo ($editQuestion && $editQuestion['category'] == 'energy') ? 'selected' : ''; ?>>Energy</option>
                                    <option value="water" <?php echo ($editQuestion && $editQuestion['category'] == 'water') ? 'selected' : ''; ?>>Water</option>
                                    <option value="climate" <?php echo ($editQuestion && $editQuestion['category'] == 'climate') ? 'selected' : ''; ?>>Climate</option>
                                </select>
                            </div>
                        </div>
                        
                        <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
                            <button type="submit" class="btn-admin btn-primary-admin">
                                <i class="fas fa-save"></i>
                                <?php echo $editQuestion ? 'Update Question' : 'Create Question'; ?>
                            </button>
                            <?php if ($editQuestion): ?>
                                <a href="admin_quiz.php" class="btn-admin btn-secondary-admin">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Questions Table -->
                <div class="admin-table-container" style="margin-top: 2rem;">
                    <div class="admin-table-header">
                        <h2><i class="fas fa-question-circle"></i> All Quiz Questions (<?php echo $questions->num_rows; ?>)</h2>
                    </div>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Question</th>
                                <th>Options</th>
                                <th>Correct</th>
                                <th>Category</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($question = $questions->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $question['question_id']; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars(substr($question['question_text'], 0, 60)); ?>...</strong>
                                    </td>
                                    <td style="font-size: 0.85rem;">
                                        <div>A: <?php echo htmlspecialchars(substr($question['option_a'], 0, 20)); ?>...</div>
                                        <div>B: <?php echo htmlspecialchars(substr($question['option_b'], 0, 20)); ?>...</div>
                                        <div>C: <?php echo htmlspecialchars(substr($question['option_c'], 0, 20)); ?>...</div>
                                        <div>D: <?php echo htmlspecialchars(substr($question['option_d'], 0, 20)); ?>...</div>
                                    </td>
                                    <td>
                                        <span class="action-badge" style="background: #d1fae5; color: #065f46;">
                                            <?php echo $question['correct_answer']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="action-badge" style="background: #dbeafe; color: #1e40af;">
                                            <?php echo ucfirst($question['category']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="?edit=<?php echo $question['question_id']; ?>" class="btn-admin btn-secondary-admin btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="?delete=<?php echo $question['question_id']; ?>" 
                                           onclick="return confirm('Are you sure you want to delete this question?')"
                                           class="btn-admin btn-danger-admin btn-sm">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
