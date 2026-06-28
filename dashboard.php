<?php
require_once 'functions.php';
require_login();
$user = current_user();

$enrollmentStmt = $pdo->prepare(
    'SELECT e.enrolled_at, c.title, c.description, c.price FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE e.user_id = ? ORDER BY e.enrolled_at DESC'
);
$enrollmentStmt->execute([$user['id']]);
$enrollments = $enrollmentStmt->fetchAll();

$courses = $pdo->query('SELECT * FROM courses ORDER BY created_at DESC')->fetchAll();
$jobs = $pdo->query('SELECT * FROM jobs ORDER BY posted_at DESC')->fetchAll();

$applicationCount = $pdo->prepare('SELECT COUNT(*) FROM applications WHERE user_id = ?');
$applicationCount->execute([$user['id']]);
$userApplications = $applicationCount->fetchColumn();

$stats = [
    'users' => $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'courses' => $pdo->query('SELECT COUNT(*) FROM courses')->fetchColumn(),
    'enrollments' => $pdo->query('SELECT COUNT(*) FROM enrollments')->fetchColumn(),
    'applications' => $pdo->query('SELECT COUNT(*) FROM applications')->fetchColumn(),
];

$chartCourses = $pdo->query(
    'SELECT c.title, COUNT(e.id) AS total FROM courses c LEFT JOIN enrollments e ON c.id = e.course_id GROUP BY c.id ORDER BY total DESC LIMIT 6'
)->fetchAll();

render_header('Dashboard');
?>
<section class="dashboard-grid">
    <div class="card stats-card">
        <h2>Welcome back, <?php echo h($user['email']); ?></h2>
        <p>Use the dashboard to explore courses, enrollments, jobs, and analytics.</p>
    </div>
    <div class="card small-card">
        <strong><?php echo h($stats['users']); ?></strong>
        <span>Registered users</span>
    </div>
    <div class="card small-card">
        <strong><?php echo h($stats['courses']); ?></strong>
        <span>Active courses</span>
    </div>
    <div class="card small-card">
        <strong><?php echo h($stats['enrollments']); ?></strong>
        <span>Total enrollments</span>
    </div>
    <div class="card small-card">
        <strong><?php echo h($stats['applications']); ?></strong>
        <span>Job applications</span>
    </div>
</section>
<section class="chart-panel">
    <h2>Course Enrollment Analytics</h2>
    <canvas id="analyticsChart" width="800" height="320"></canvas>
</section>
<section class="dashboard-section">
    <div class="panel">
        <h3>Your Enrolled Courses</h3>
        <?php if (empty($enrollments)): ?>
            <p>You are not enrolled in any course yet. Browse courses below.</p>
        <?php else: ?>
            <ul class="item-list">
                <?php foreach ($enrollments as $item): ?>
                    <li>
                        <strong><?php echo h($item['title']); ?></strong>
                        <span><?php echo h($item['description']); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    <div class="panel">
        <h3>Available Courses</h3>
        <ul class="item-list">
            <?php foreach ($courses as $course): ?>
                <li>
                    <strong><?php echo h($course['title']); ?></strong>
                    <span><?php echo h($course['description']); ?></span>
                    <div class="item-actions">
                        <span>₹<?php echo number_format($course['price'], 2, '.', ','); ?></span>
                        <button class="button button-small enroll-button" data-course-id="<?php echo h($course['id']); ?>">Enroll</button>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
<section class="dashboard-section">
    <div class="panel">
        <h3>Open Jobs</h3>
        <?php if (empty($jobs)): ?>
            <p>No job listings are available yet.</p>
        <?php else: ?>
            <ul class="item-list job-list">
                <?php foreach ($jobs as $job): ?>
                    <li>
                        <div class="job-meta">
                            <div>
                                <strong><?php echo h($job['title']); ?></strong>
                                <p><?php echo h($job['company']); ?> &middot; <?php echo h($job['location']); ?></p>
                            </div>
                            <button class="button button-small apply-button" data-job-id="<?php echo h($job['id']); ?>">Apply</button>
                        </div>
                        <p class="job-description"><?php echo h($job['description']); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    <div class="panel">
        <h3>My Applications</h3>
        <p>You have submitted <strong><?php echo h($userApplications); ?></strong> applications so far.</p>
        <p>Track progress and return to the admin section for more opportunities.</p>
    </div>
</section>
<script>
window.analyticsData = {
    labels: <?php echo json_encode(array_column($chartCourses, 'title')); ?>,
    values: <?php echo json_encode(array_column($chartCourses, 'total')); ?>
};
</script>
<?php render_footer(); ?>