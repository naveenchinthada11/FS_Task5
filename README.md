# FS_Task5
# Capstone Portal

A professional PHP capstone project built for deployment on free shared hosting.

## Features

- Email OTP authentication workflow
- Course catalog with search and enrollment
- Jobs board and application tracking
- Admin panel for course/job creation
- Analytics dashboard using Chart.js
- AJAX-powered real-time search and enrollment

## Deployment

1. Create MySQL database `capstone`.
2. Run `database.sql` in phpMyAdmin or MySQL console.
3. Place project files in the hosting root.
4. Configure `config.php` with host/database credentials.
5. Use `admin@capstone.local` for admin creation by inserting role `admin`.

## Pages

- `index.php` - Home page & search
- `login.php` - OTP login request
- `verify_otp.php` - OTP verification
- `dashboard.php` - User dashboard
- `admin.php` - Admin course/job management

 ## Notes

- Email OTP uses `mail()`; local hosting may use debug OTP display.
- Use a seeded admin user in MySQL to access admin controls.


- Email OTP uses `mail()`; local hosting may use debug OTP display.
- Use a seeded admin user in MySQL to access admin controls.
