-- =============================================
-- Portfolio Database Schema & Sample Data
-- =============================================
-- Run this file in phpMyAdmin or MySQL CLI:
--   source portfolio.sql;
-- =============================================

CREATE DATABASE IF NOT EXISTS portfolio_db
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE portfolio_db;

-- =============================================
-- TABLE: admin_users
-- =============================================
DROP TABLE IF EXISTS admin_users;
CREATE TABLE admin_users (
  id             INT          NOT NULL AUTO_INCREMENT,
  username       VARCHAR(50)  NOT NULL UNIQUE,
  password_hash  VARCHAR(255) NOT NULL,
  remember_token VARCHAR(100) DEFAULT NULL,
  created_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default credentials: username=admin  password=emre123
-- Hash generated with: password_hash('emre123', PASSWORD_BCRYPT)
-- Run setup.php once to insert the admin account securely.

-- =============================================
-- TABLE: projects
-- =============================================
DROP TABLE IF EXISTS projects;
CREATE TABLE projects (
  id           INT           NOT NULL AUTO_INCREMENT,
  title        VARCHAR(100)  NOT NULL,
  description  TEXT          NOT NULL,
  tech_stack   VARCHAR(255)  DEFAULT NULL,
  image_url    VARCHAR(255)  DEFAULT NULL,
  github_url   VARCHAR(255)  DEFAULT NULL,
  live_url     VARCHAR(255)  DEFAULT NULL,
  created_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- TABLE: contacts
-- =============================================
DROP TABLE IF EXISTS contacts;
CREATE TABLE contacts (
  id         INT          NOT NULL AUTO_INCREMENT,
  name       VARCHAR(100) NOT NULL,
  email      VARCHAR(150) NOT NULL,
  subject    VARCHAR(200) NOT NULL DEFAULT '',
  message    TEXT         NOT NULL,
  is_read    TINYINT(1)   NOT NULL DEFAULT 0,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- PROJECTS: github.com/osmanemreuzer
-- =============================================
INSERT INTO projects (title, description, tech_stack, github_url, live_url) VALUES
(
  'FitGain',
  'A fitness tracking web application for logging workouts, monitoring progress, and managing exercise routines. Built with ASP.NET and features a dynamic frontend.',
  'C#, ASP.NET, HTML, CSS, JavaScript',
  'https://github.com/osmanemreuzer/FitGain',
  NULL
),
(
  'IdentityApp',
  'A secure user authentication system built with ASP.NET Core Identity. Includes registration, login, role management, and profile features.',
  'C#, ASP.NET Core, Identity, HTML, CSS',
  'https://github.com/osmanemreuzer/IdentityApp',
  NULL
),
(
  'StoreApp',
  'A full-featured e-commerce store application with product listing, category filtering, and cart functionality. Clean UI built with SCSS.',
  'C#, ASP.NET, SCSS, HTML',
  'https://github.com/osmanemreuzer/StoreApp',
  NULL
),
(
  'SchoolSystem',
  'A school management system for tracking students, courses, teachers, and grades. Features a full CRUD interface with a dynamic web frontend.',
  'C#, ASP.NET, HTML, CSS, JavaScript',
  'https://github.com/osmanemreuzer/SchoolSystem',
  NULL
),
(
  'FormsApp',
  'A Windows Forms desktop application demonstrating various UI components, event handling, and form validation techniques in C#.',
  'C#, HTML, CSS, JavaScript',
  'https://github.com/osmanemreuzer/FormsApp',
  NULL
);
