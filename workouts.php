<?php
// workouts.php
// List all workout plans for the logged-in user + allow delete

require_once 'config/db.php';
require_once 'includes/auth.php';
require_login();

$user_id = current_user_id();

// ----- Handle delete request -----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = (int) $_POST['delete_id'];

    if ($delete_id > 0) {
        $stmt = $mysqli->prepare("
            DELETE FROM workout_plans
            WHERE workout_id = ? AND user_id = ?
        ");

        if ($stmt) {
            $stmt->bind_param('ii', $delete_id, $user_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Prevent form resubmission on refresh
    header('Location: workouts.php');
    exit;
}

// ----- Fetch workout plans for this user -----
$stmt = $mysqli->prepare("
    SELECT workout_id, name, difficulty, target_muscle, created_at
    FROM workout_plans
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

include 'includes/header.php';
?>

<div class="page-card">
    <h2>My Workout Plans</h2>

    <!-- Button to go to Create Workout page -->
    <p style="margin-bottom: 15px;">
        <a href="workouts_new.php" class="btn-primary">
             Create New Workout Plan
        </a>
    </p>

    <?php if ($result->num_rows === 0): ?>
        <p>You don't have any workout plans yet.</p>
        <p>
            <a href="workouts_new.php" class="btn-secondary">
                Create your first workout plan
            </a>
        </p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Plan Name</th>
                    <th>Difficulty</th>
                    <th>Target Muscle</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                while ($row = $result->fetch_assoc()):
                ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['difficulty']); ?></td>
                        <td><?php echo htmlspecialchars($row['target_muscle']); ?></td>
                        <td><?php echo date('M j, Y g:i A', strtotime($row['created_at'])); ?></td>
                        <td>
                            <form method="post" style="display:inline;"
                                  onsubmit="return confirm('Delete this workout plan?');">
                                <input type="hidden" name="delete_id"
                                       value="<?php echo (int)$row['workout_id']; ?>">
                                <button type="submit" class="btn-danger">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php
$stmt->close();
include 'includes/footer.php';
?>
