<?php
require_once 'functions.php';

$stmt = $pdo->query('SELECT * FROM courses ORDER BY id DESC LIMIT 8');
$courses = $stmt->fetchAll();
render_header('Home');
?>
<section class="hero">
    <span class="hero-badge">New • Career-ready learning paths</span>
    <h1>Learn new skills, enroll in expert-led courses.</h1>
    <p>Search available learning paths and join a community-ready capstone portal with a smoother experience.</p>
    <div class="hero-actions">
        <a class="button" href="login.php">Login with Email OTP</a>
        <a class="button button-secondary" href="dashboard.php">View Dashboard</a>
    </div>
</section>
<section class="search-panel">
    <div class="section-heading">
        <h2>Explore courses</h2>
        <p>Search a curated range of learning options and jump straight into your next milestone.</p>
    </div>
    <div class="search-box">
        <input id="course-search" type="search" placeholder="Search courses by title or keyword..." aria-label="Search courses">
    </div>
</section>
<section class="course-grid" id="course-results">
    <?php foreach ($courses as $course): ?>
        <article class="course-card">
            <h2><?php echo h($course['title']); ?></h2>
            <p><?php echo h($course['description']); ?></p>
            <div class="course-meta">
                <span>Price: ₹<?php echo number_format($course['price'], 2, '.', ','); ?></span>
            </div>
            <button class="enroll-button" data-course-id="<?php echo h($course['id']); ?>">Enroll Now</button>
        </article>
    <?php endforeach; ?>
</section>
<?php render_footer(); ?>