document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.querySelector('#course-search');
    const resultsContainer = document.querySelector('#course-results');

    if (searchInput && resultsContainer) {
        let timeout;
        searchInput.addEventListener('input', function () {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                fetch(`ajax_search.php?q=${encodeURIComponent(this.value)}`)
                    .then(response => response.json())
                    .then(data => {
                        resultsContainer.innerHTML = data.map(course => `
                            <article class="course-card">
                                <h2>${course.title}</h2>
                                <p>${course.description}</p>
                                <div class="course-meta"><span>Price: ₹${new Intl.NumberFormat('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(course.price)}</span></div>
                                <button class="enroll-button" data-course-id="${course.id}">Enroll Now</button>
                            </article>
                        `).join('');
                    })
                    .catch(() => {
                        resultsContainer.innerHTML = '<p>Search failed. Try again later.</p>';
                    });
            }, 300);
        });
    }

    document.body.addEventListener('click', function (event) {
        if (event.target.matches('.enroll-button')) {
            const courseId = event.target.dataset.courseId;
            fetch('enroll.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `course_id=${encodeURIComponent(courseId)}`
            })
                .then(response => response.json())
                .then(result => {
                    alert(result.message);
                })
                .catch(() => alert('Could not complete enrollment.'));
        }

        if (event.target.matches('.apply-button')) {
            const jobId = event.target.dataset.jobId;
            fetch('apply.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `job_id=${encodeURIComponent(jobId)}`
            })
                .then(response => response.json())
                .then(result => {
                    alert(result.message);
                })
                .catch(() => alert('Could not submit application.'));
        }
    });

    if (window.analyticsData) {
        const ctx = document.getElementById('analyticsChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: window.analyticsData.labels,
                    datasets: [{
                        label: 'Enrollments',
                        data: window.analyticsData.values,
                        backgroundColor: '#2563eb'
                    }]
                },
                options: {
                    responsive: true,
                    scales: { y: { beginAtZero: true } }
                }
            });
        }
    }
});
