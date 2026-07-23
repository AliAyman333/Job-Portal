DROP DATABASE IF EXISTS job_portal;
CREATE DATABASE IF NOT EXISTS job_portal;

USE job_portal;



-- 1️⃣ Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','employer','seeker') NOT NULL,
    status TINYINT(1) DEFAULT 1
);

-- 2️⃣ Categories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL
);

-- 3️⃣ Locations


-- 4️⃣ Companies
CREATE TABLE companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    company_name VARCHAR(150),
    description TEXT,
    location VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 5️⃣ Jobs
CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employer_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    category_id INT,
    location VARCHAR(100),
    salary VARCHAR(50),
    job_type ENUM('Full-Time','Part-Time','Internship','Contract') DEFAULT 'Full-Time',
    expiry_date DATE,
    description TEXT,
    status ENUM('Pending','Open','Closed') DEFAULT 'Pending',
    FOREIGN KEY (employer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- 6️⃣ Job Seeker Profile
CREATE TABLE seekers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    skills VARCHAR(255),
    education VARCHAR(255),
    experience VARCHAR(255),
    resume VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 7️⃣ Applications
CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    seeker_id INT NOT NULL,
    resume VARCHAR(255),
    status ENUM('Applied','Shortlisted','Accepted','Rejected') DEFAULT 'Applied',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (seeker_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 8️⃣ Activity Log
CREATE TABLE activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 9️⃣ Search History Table
CREATE TABLE search_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seeker_id INT NOT NULL,
    search_term VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seeker_id) REFERENCES users(id) ON DELETE CASCADE
);



-- إنشاء جدول المهارات
CREATE TABLE IF NOT EXISTS skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    skill_name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- تعديل جدول seekers: إضافة الأعمدة الجديدة وحذف القديم إن لزم
ALTER TABLE seekers
   
    ADD COLUMN gender ENUM('Male','Female') DEFAULT NULL,
    ADD COLUMN years_of_experience INT DEFAULT 0,
    DROP COLUMN experience;

  

-- إضافة حقل الجندر 
ALTER TABLE jobs
    ADD COLUMN gender ENUM('Male','Female','Not important') DEFAULT NULL;

ALTER TABLE jobs
     ADD COLUMN education TEXT DEFAULT NULL;


   
-- ============================================
-- SAMPLE DATA FOR JOB PORTAL DATABASE
-- ============================================
CREATE TABLE IF NOT EXISTS skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    skill_name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE jobs
    ADD COLUMN skills TEXT DEFAULT NULL;


CREATE TABLE seeker_skills (
    seeker_id INT NOT NULL,
    skill_id INT NOT NULL,
    PRIMARY KEY (seeker_id, skill_id),
    FOREIGN KEY (seeker_id) REFERENCES seekers(user_id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE
);

CREATE TABLE job_skills (
    job_id INT NOT NULL,
    skill_id INT NOT NULL,
    PRIMARY KEY (job_id, skill_id),
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE
);

ALTER TABLE seekers
   
    ADD COLUMN IF NOT EXISTS category_id INT DEFAULT NULL,
    ADD FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL;

