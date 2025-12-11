<?php
require_once 'config/db.php';
require_once 'includes/auth.php';
require_login();

$user_id = current_user_id();

// ----- Range filter: all-time or last 7 days -----
$range = $_GET['range'] ?? 'all';   // 'all' or '7'
$range_clause = '';

if ($range === '7') {
    // last 7 days including today
    $range_clause = " AND log_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
}

// ---------- Overall stats ----------
$stats_sql = "
    SELECT 
        COUNT(*) AS total_logs,
        MIN(log_date) AS first_log,
        MAX(log_date) AS last_log,
        AVG(weight_lb) AS avg_weight,
        AVG(rpe) AS avg_rpe,
        SUM(strength_volume) AS total_strength,
        SUM(cardio_minutes) AS total_cardio
    FROM progress_logs
    WHERE user_id = ? $range_clause
";

$stats_stmt = $mysqli->prepare($stats_sql);
$stats_stmt->bind_param("i", $user_id);
$stats_stmt->execute();
$stats_result = $stats_stmt->get_result();
$stats = $stats_result->fetch_assoc();

// ---------- Weight trend (first vs last in range) ----------
$trend_sql = "
    SELECT log_date, weight_lb
    FROM progress_logs
    WHERE user_id = ? $range_clause
    ORDER BY log_date ASC, created_at ASC
";

$trend_stmt = $mysqli->prepare($trend_sql);
$trend_stmt->bind_param("i", $user_id);
$trend_stmt->execute();
$trend_result = $trend_stmt->get_result();

$first_weight = null;
$last_weight  = null;
$first_date   = null;
$last_date    = null;
$log_count    = $trend_result->num_rows;

if ($log_count > 0) {
    $first_row   = $trend_result->fetch_assoc();
    $first_weight = (float)$first_row['weight_lb'];
    $first_date   = $first_row['log_date'];

    if ($log_count === 1) {
        $last_weight = $first_weight;
        $last_date   = $first_date;
    } else {
        // move pointer to last row
        $trend_result->data_seek($log_count - 1);
        $last_row   = $trend_result->fetch_assoc();
        $last_weight = (float)$last_row['weight_lb'];
        $last_date   = $last_row['log_date'];
    }
}

// build human-friendly trend text
$weight_trend_text = "Not enough data yet.";
if ($log_count >= 2 && $first_weight !== null && $last_weight !== null) {
    $change = $last_weight - $first_weight;
    $change_abs = abs($change);
    if ($change_abs < 0.01) {
        $weight_trend_text = "Stable (no significant change)";
    } elseif ($change > 0) {
        $weight_trend_text = "Gained " . number_format($change_abs, 2) . " lbs";
    } else {
        $weight_trend_text = "Lost " . number_format($change_abs, 2) . " lbs";
    }
    $weight_trend_text .= " from " . htmlspecialchars($first_date) . " to " . htmlspecialchars($last_date);
}

// ---------- Per-focus breakdown (with cardio minutes) ----------
$focus_sql = "
    SELECT 
        focus,
        AVG(rpe) AS avg_rpe,
        AVG(weight_lb) AS avg_weight,
        SUM(cardio_minutes) AS total_cardio_focus
    FROM progress_logs
    WHERE user_id = ? $range_clause
    GROUP BY focus
    ORDER BY total_cardio_focus DESC
";

$focus_stmt = $mysqli->prepare($focus_sql);
$focus_stmt->bind_param("i", $user_id);
$focus_stmt->execute();
$focus_result = $focus_stmt->get_result();

include 'includes/header.php';
?>

<div class="page-card wide">
    <h2>Analytics Dashboard</h2>

    <!-- Range selector -->
    <form method="GET" action="analytics.php" style="margin-bottom: 12px;">
        <label for="range">Show data for: </label>
        <select name="range" id="range">
            <option value="all" <?php if ($range === 'all') echo 'selected'; ?>>All time</option>
            <option value="7"   <?php if ($range === '7')   echo 'selected'; ?>>Last 7 days</option>
        </select>
        <button type="submit">Apply</button>
    </form>

    <?php if (empty($stats['total_logs'])): ?>
        <p>You don’t have any logs in this time range yet. Add a few workouts on the <strong>Log Progress</strong> page to see analytics here.</p>
    <?php else: ?>
        <!-- Overall summary -->
        <h3>Overall Summary</h3>
        <table class="data-table">
            <tr>
                <th>Total Logs</th>
                <td><?php echo (int)$stats['total_logs']; ?></td>
            </tr>
            <tr>
                <th>First Log Date</th>
                <td><?php echo htmlspecialchars($stats['first_log']); ?></td>
            </tr>
            <tr>
                <th>Most Recent Log</th>
                <td><?php echo htmlspecialchars($stats['last_log']); ?></td>
            </tr>
            <tr>
                <th>Average Body Weight (lbs)</th>
                <td><?php echo $stats['avg_weight'] !== null ? number_format($stats['avg_weight'], 2) : 'N/A'; ?></td>
            </tr>
            <tr>
                <th>Average RPE</th>
                <td><?php echo $stats['avg_rpe'] !== null ? number_format($stats['avg_rpe'], 1) : 'N/A'; ?></td>
            </tr>
            <tr>
                <th>Total Strength Volume</th>
                <td><?php echo $stats['total_strength'] !== null ? (int)$stats['total_strength'] : 0; ?></td>
            </tr>
            <tr>
                <th>Total Cardio Minutes</th>
                <td><?php echo $stats['total_cardio'] !== null ? (int)$stats['total_cardio'] : 0; ?></td>
            </tr>
            <tr>
                <th>Weight Trend</th>
                <td><?php echo $weight_trend_text; ?></td>
            </tr>
        </table>

        <!-- Focus breakdown -->
        <h3 style="margin-top: 25px;">Breakdown by Focus</h3>

        <?php if ($focus_result->num_rows === 0): ?>
            <p>No focus data in this time range.</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Focus</th>
                        <th>Total Cardio Minutes</th>
                        <th>Average RPE</th>
                        <th>Average Weight (lbs)</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $focus_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['focus']); ?></td>
                        <td><?php echo $row['total_cardio_focus'] !== null ? (int)$row['total_cardio_focus'] : 0; ?></td>
                        <td><?php echo $row['avg_rpe'] !== null ? number_format($row['avg_rpe'], 1) : 'N/A'; ?></td>
                        <td><?php echo $row['avg_weight'] !== null ? number_format($row['avg_weight'], 2) : 'N/A'; ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <p class="card-note">
            Time range: <strong><?php echo ($range === '7') ? 'Last 7 days' : 'All time'; ?></strong>.
            These stats are calculated from your logged workouts in FitQuest.
        </p>
    <?php endif; ?>
</div>

<?php
$stats_stmt->close();
$trend_stmt->close();
$focus_stmt->close();
include 'includes/footer.php';
?>
