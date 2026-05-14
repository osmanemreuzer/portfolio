# Osman Emre Üzer — Portfolio

A dynamic full-stack web portfolio built with HTML5, CSS3, JavaScript, PHP, and MySQL.

**Live Demo:** [osmanuzer.infinityfreeapp.com](http://osmanuzer.infinityfreeapp.com)

---

## Features

- Responsive design with light/dark mode
- Typing effect & animated skill bars
- Projects loaded dynamically from MySQL database
- Project filter by technology (C#, ASP.NET, HTML/CSS, JavaScript)
- Contact form with validation and AJAX submission
- Admin dashboard: add/edit/delete projects, manage messages
- Secure login with bcrypt password hashing and session management

## Tech Stack

| Layer | Technology |
|---|---|
| Frontend | HTML5, CSS3, JavaScript (ES6+) |
| Backend | PHP 8 |
| Database | MySQL (MySQLi) |
| Hosting | InfinityFree |

## File Structure

```
portfolio/
├── index.html
├── css/
│   ├── style.css
│   └── admin.css
├── js/
│   ├── main.js
│   └── admin.js
├── api/
│   ├── get_projects.php
│   └── contact.php
├── admin/
│   ├── login.php
│   ├── dashboard.php
│   ├── projects.php
│   ├── add_project.php
│   ├── edit_project.php
│   ├── messages.php
│   └── logout.php
├── db/
│   └── connection.php
├── images/
│   └── img1.jpeg
└── portfolio.sql
```

## Setup

1. Import `portfolio.sql` into your MySQL database
2. Update `db/connection.php` with your database credentials
3. Run `setup.php` once to create the admin account
4. Delete `setup.php` after setup is complete

## Author

**Osman Emre Üzer**
[GitHub](https://github.com/osmanemreuzer) · [LinkedIn](https://linkedin.com/in/osman-emre-üzer-29a25635b)
