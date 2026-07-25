USE job_portal;

-- ============================================================
-- 1) عمود "الخبرة المطلوبة" بجدول jobs
--    لازم لحساب Match Score (مقارنة خبرة الباحث بالمطلوب)
-- ============================================================
ALTER TABLE jobs
    ADD COLUMN required_experience INT DEFAULT 0 AFTER education;

-- ============================================================
-- 2) جدول جدولة المقابلات (Interviews)
--    الـ employer يقترح تاريخ ووقت، والـ seeker يؤكد أو يرفض
-- ============================================================
CREATE TABLE IF NOT EXISTS interviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    employer_id INT NOT NULL,
    seeker_id INT NOT NULL,
    proposed_date DATE NOT NULL,
    proposed_time TIME NOT NULL,
    location_or_link VARCHAR(255) DEFAULT NULL,
    notes TEXT,
    status ENUM('Proposed','Confirmed','Declined','Rescheduled','Completed','Cancelled') DEFAULT 'Proposed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (employer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (seeker_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
