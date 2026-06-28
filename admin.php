<?php
require_once 'functions.php';
require_admin();

$editCourse = null;
$editJob = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_course') {
        $stmt = $pdo->prepare('INSERT INTO courses (title, description, price, created_at) VALUES (?, ?, ?, NOW())');
        $stmt->execute([trim($_POST['course_title']), trim($_POST['course_description']), floatval($_POST['course_price'])]);
        flash('success', 'Course created successfully.');
        header('Location: admin.php');
        exit;
    }

    if ($action === 'update_course' && !empty($_POST['course_id'])) {
        $stmt = $pdo->prepare('UPDATE courses SET title = ?, description = ?, price = ? WHERE id = ?');
        $stmt->execute([trim($_POST['course_title']), trim($_POST['course_description']), floatval($_POST['course_price']), intval($_POST['course_id'])]);
        flash('success', 'Course updated successfully.');
        header('Location: admin.php');
        exit;
    }

    if ($action === 'delete_course' && !empty($_POST['course_id'])) {
        $stmt = $pdo->prepare('DELETE FROM courses WHERE id = ?');
        $stmt->execute([intval($_POST['course_id'])]);
        flash('success', 'Course deleted successfully.');
        header('Location: admin.php');
        exit;
    }

    if ($action === 'create_job') {
        $stmt = $pdo->prepare('INSERT INTO jobs (title, company, location, description, posted_at) VALUES (?, ?, ?, ?, NOW())');
        $stmt->execute([trim($_POST['job_title']), trim($_POST['job_company']), trim($_POST['job_location']), trim($_POST['job_description'])]);
        flash('success', 'Job posted successfully.');
        header('Location: admin.php');
        exit;
    }

    if ($action === 'update_job' && !empty($_POST['job_id'])) {
        $stmt = $pdo->prepare('UPDATE jobs SET title = ?, company = ?, location = ?, description = ? WHERE id = ?');
        $stmt->execute([trim($_POST['job_title']), trim($_POST['job_company']), trim($_POST['job_location']), trim($_POST['job_description']), intval($_POST['job_id'])]);
        flash('success', 'Job updated successfully.');
        header('Location: admin.php');
        exit;
    }

    if ($action === 'delete_job' && !empty($_POST['job_id'])) {
        $stmt = $pdo->prepare('DELETE FROM jobs WHERE id = ?');
        $stmt->execute([intval($_POST['job_id'])]);
        flash('success', 'Job deleted successfully.');
        header('Location: admin.php');
        exit;
    }
}

if (!empty($_GET['edit_course_id'])) {
    $stmt = $pdo->prepare('SELECT * FROM courses WHERE id = ?');
    $stmt->execute([intval($_GET['edit_course_id'])]);
    $editCourse = $stmt->fetch();
}

if (!empty($_GET['edit_job_id'])) {
    $stmt = $pdo->prepare('SELECT * FROM jobs WHERE id = ?');
    $stmt->execute([intval($_GET['edit_job_id'])]);
    $editJob = $stmt->fetch();
}

$courses = $pdo->query('SELECT * FROM courses ORDER BY created_at DESC')->fetchAll();
$jobs = $pdo->query('SELECT * FROM jobs ORDER BY posted_at DESC')->fetchAll();
$recentEnrollments = $pdo->query(
    'SELECT u.email, c.title, e.enrolled_at FROM enrollments e JOIN users u ON e.user_id = u.id JOIN courses c ON e.course_id = c.id ORDER BY e.enrolled_at DESC LIMIT 10'
)->fetchAll();
$recentApplications = $pdo->query(
    'SELECT u.email, j.title, a.applied_at FROM applications a JOIN users u ON a.user_id = u.id JOIN jobs j ON a.job_id = j.id ORDER BY a.applied_at DESC LIMIT 10'
)->fetchAll();

render_header('Admin Panel');
?>
<section class="admin-grid">
    <div class="admin-panel">
        <h2><?php echo $editCourse ? 'Edit Course' : 'Create Course'; ?></h2>
        <form method="post" class="admin-form">
            <input type="hidden" name="action" value="<?php echo $editCourse ? 'update_course' : 'create_course'; ?>">
            <?php if ($editCourse): ?>
                <input type="hidden" name="course_id" value="<?php echo h($editCourse['id']); ?>">
            <?php endif; ?>
            <label>Title<input name="course_title" required value="<?php echo h($editCourse['title'] ?? ''); ?>"></label>
            <label>Description<textarea name="course_description" rows="4" required><?php echo h($editCourse['description'] ?? ''); ?></textarea></label>
            <label>Price<input name="course_price" type="number" step="0.01" required value="<?php echo h($editCourse['price'] ?? ''); ?>"></label>
            <button type="submit"><?php echo $editCourse ? 'Update Course' : 'Save Course'; ?></button>
            <?php if ($editCourse): ?>
                <a class="button button-secondary" href="admin.php">Cancel</a>
            <?php endif; ?>
        </form>
    </div>
    <div class="admin-panel">
        <h2><?php echo $editJob ? 'Edit Job' : 'Post Job'; ?></h2>
        <form method="post" class="admin-form">
            <input type="hidden" name="action" value="<?php echo $editJob ? 'update_job' : 'create_job'; ?>">
            <?php if ($editJob): ?>
                <input type="hidden" name="job_id" value="<?php echo h($editJob['id']); ?>">
            <?php endif; ?>
            <label>Job Title<input name="job_title" required value="<?php echo h($editJob['title'] ?? ''); ?>"></label>
            <label>Company<input name="job_company" required value="<?php echo h($editJob['company'] ?? ''); ?>"></label>
            <label>Location<input name="job_location" required value="<?php echo h($editJob['location'] ?? ''); ?>"></label>
            <label>Description<textarea name="job_description" rows="4" required><?php echo h($editJob['description'] ?? ''); ?></textarea></label>
            <button type="submit"><?php echo $editJob ? 'Update Job' : 'Post Job'; ?></button>
            <?php if ($editJob): ?>
                <a class="button button-secondary" href="admin.php">Cancel</a>
            <?php endif; ?>
        </form>
    </div>
</section>
<section class="dashboard-section">
    <div class="panel">
        <h3>Courses</h3>
        <ul class="item-list">
            <?php foreach ($courses as $course): ?>
                <li>
                    <div class="admin-item-row">
                        <div>
                            <strong><?php echo h($course['title']); ?></strong>
                            <p>₹<?php echo number_format($course['price'], 2, '.', ','); ?></p>
                        </div>
                        <div class="admin-item-actions">
                            <a class="button button-small button-secondary" href="admin.php?edit_course_id=<?php echo h($course['id']); ?>">Edit</a>
                            <form method="post" class="inline-form" onsubmit="return confirm('Delete this course?');">
                                <input type="hidden" name="action" value="delete_course">
                                <input type="hidden" name="course_id" value="<?php echo h($course['id']); ?>">
                                <button class="button button-small button-secondary" type="submit">Delete</button>
                            </form>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="panel">
        <h3>Job Listings</h3>
        <ul class="item-list">
            <?php foreach ($jobs as $job): ?>
                <li>
                    <div class="admin-item-row">
                        <div>
                            <strong><?php echo h($job['title']); ?></strong>
                            <p><?php echo h($job['company']); ?> &middot; <?php echo h($job['location']); ?></p>
                        </div>
                        <div class="admin-item-actions">
                            <a class="button button-small button-secondary" href="admin.php?edit_job_id=<?php echo h($job['id']); ?>">Edit</a>
                            <form method="post" class="inline-form" onsubmit="return confirm('Delete this job?');">
                                <input type="hidden" name="action" value="delete_job">
                                <input type="hidden" name="job_id" value="<?php echo h($job['id']); ?>">
                                <button class="button button-small button-secondary" type="submit">Delete</button>
                            </form>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
<section class="dashboard-section">
    <div class="panel">
        <h3>Recent Enrollments</h3>
        <ul class="item-list">
            <?php foreach ($recentEnrollments as $item): ?>
                <li><?php echo h($item['email']); ?> enrolled in <?php echo h($item['title']); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="panel">
        <h3>Recent Applications</h3>
        <ul class="item-list">
            <?php foreach ($recentApplications as $item): ?>
                <li><?php echo h($item['email']); ?> applied for <?php echo h($item['title']); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
<?php render_footer(); ?>