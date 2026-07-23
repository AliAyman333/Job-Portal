<?php
// =============================================
// تأكد من تضمين ملف الاتصال الذي يحتوي على $conc
// =============================================
require_once 'conn.php'; // عدّل المسار حسب مشروعك

// للتأكد من استخدام قاعدة البيانات الصحيحة
mysqli_select_db($conc, 'job_portal');

// ============================================
// 1. إدخال المستخدمين مع تشفير كلمة المرور
// ============================================
$users = [
    ['Admin User', 'admin@jobportal.com', 'Admin@123', 'admin', 1],
    ['Admin User2', 'admin2@jobportal.com', 'Admin2@123', 'admin', 1],
    ['Ayman', 'ayman@jobportal.com', 'Ayman@123', 'admin', 1],
    ['Bassam', 'bassam@jobportal.com', 'Bassam@123', 'employer', 1],
    ['Mohammed', 'mohammed@jobportal.com', 'Mohammed@123', 'employer', 1],
    ['ahmad', 'ahmad@jobportal.com', 'Ahmad@123', 'employer', 1],
    ['Tech Corp', 'tech@techcorp.com', 'TechCorp#2024', 'employer', 1],
    ['StartUp Solutions', 'hr@startups.com', 'StartUp@123', 'employer', 1],
    ['Global Tech Pvt Ltd', 'jobs@globaltech.com', 'GlobalTech#123', 'employer', 1],
    ['Digital Marketing Inc', 'career@digi-market.com', 'DigiMkt@2024', 'employer', 1],
    ['Rajesh Kumar', 'rajesh.kumar@email.com', 'Rajesh@2024', 'seeker', 1],
    ['sham', 'sham@email.com', 'Sham@2024', 'seeker', 1],
   
    ['Fatima Noor', 'fatima@email.com', 'Fatima#2024', 'seeker', 1],
    ['Sara Ali', 'sara.ali@email.com', 'Sara@2024', 'seeker', 1],
    ['Priya Sharma', 'priya.sharma@email.com', 'Priya#2024', 'seeker', 1],
    ['Arjun Singh', 'arjun.singh@email.com', 'Arjun@2024', 'seeker', 1],
    ['Neha Patel', 'neha.patel@email.com', 'Neha#Design', 'seeker', 1],
    ['ali', 'ali@email.com', 'Ali@2024', 'seeker', 1],
    ['Amit Gupta', 'amit.gupta@email.com', 'Amit@2024', 'seeker', 1],
    ['Zara Khan', 'zara.khan@email.com', 'Zara#DataSci', 'seeker', 1]
];

$stmt_user = mysqli_prepare($conc, 
    "INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)"
);

foreach ($users as $user) {
    $name     = $user[0];
    $email    = $user[1];
    $password = password_hash($user[2], PASSWORD_DEFAULT); // تشفير كلمة المرور
    $role     = $user[3];
    $status   = $user[4];

    mysqli_stmt_bind_param($stmt_user, "ssssi", $name, $email, $password, $role, $status);
    mysqli_stmt_execute($stmt_user);
}
mysqli_stmt_close($stmt_user);
echo "✓ تم إدخال جميع المستخدمين بكلمات مرور مشفرة.<br>";

// ============================================
// 2. باقي الإدخالات (لا تحتاج تشفيراً)
//    سننفذها مباشرة باستخدام mysqli_multi_query
// ============================================

$sql_rest = "
INSERT INTO search_history (seeker_id, search_term) VALUES
(9, 'PHP Developer'),
(10, 'React Developer'),
(11, 'Python Developer'),
(12, 'UI/UX Designer'),
(13, 'Full Stack Developer'),
(15, 'Data Scientist'),
(9, 'Laravel Jobs'),
(10, 'Frontend Developer'),
(11, 'Django Jobs'),
(12, 'UX Design Jobs'),
(13, 'Node.js Developer'),
(15, 'Machine Learning Jobs');

INSERT INTO categories (category_name) VALUES
('Software Development'),
('Web Development'),
('Mobile App Development'),
('Data Science'),
('DevOps & Cloud'),
('UI/UX Design'),
('Digital Marketing'),
('Business Analysis'),
('Project Management'),
('QA & Testing');



INSERT INTO companies (user_id, company_name, description, location) VALUES
(2, 'Tech Corp India', 'Leading IT solutions provider with 500+ employees. Specialized in enterprise software development and cloud services.', 'Damascus'),
(3, 'StartUp Solutions', 'Fast-growing startup focused on AI and machine learning solutions for businesses. Founded in 2020.', 'Remote'),
(4, 'Global Tech Pvt Ltd', 'International tech company with offices in 15 countries. Expertise in web and mobile development.', 'Latakia'),
(5, 'Digital Marketing Inc', 'Full-service digital marketing agency. Specializes in social media, SEO, and content marketing.', 'Homs');

INSERT INTO jobs (employer_id, title, category_id, location, salary, job_type, expiry_date, description, status, gender, education, skills) VALUES
(2, 'Senior PHP Developer', 1, 'Damascus', '$12,000 - $16,000', 'Full-Time', '2026-03-01', 'We are looking for an experienced PHP developer with 5+ years of experience in Laravel and MySQL. Must have experience with REST APIs and modern web technologies.', 'Open','Not important', 'Master\'s Degree','8'),
(2, 'React.js Developer', 2, 'Damascus', '$10,000 - $14,000', 'Full-Time', '2026-03-15', 'Seeking a talented React developer to join our frontend team. Experience with Redux, Hooks, and responsive design required.', 'Open','Not important', 'Master\'s Degree','8'),
(2, 'Junior Python Developer', 1, 'Damascus', '$6,000 - $8,000', 'Full-Time', '2026-02-28', 'Perfect for fresh graduates interested in Python development. We provide training and mentorship.', 'Open','Not important', 'Master\'s Degree','6'),
(3, 'Full Stack Web Developer', 2, 'Remote', '$9,000 - $12,000', 'Full-Time', '2026-03-10', 'Join our startup! Build scalable web applications using Node.js and React. Work in agile environment.', 'Open','Not important', 'Master\'s Degree','2'),
(3, 'Mobile App Developer (Flutter)', 3, 'Remote', '$8,000 - $11,000', 'Full-Time', '2026-03-05', 'We need an experienced Flutter developer to build cross-platform mobile applications for iOS and Android.', 'Open','Not important', 'Master\'s Degree','4'),
(4, 'DevOps Engineer', 5, 'Latakia', '$11,000 - $15,000', 'Full-Time', '2026-03-20', 'Manage and optimize our cloud infrastructure on AWS. Experience with Docker, Kubernetes, and CI/CD pipelines required.', 'Open','Not important', 'Master\'s Degree','1'),
(4, 'Data Science Intern', 4, 'Latakia', '$3,000 - $4,000', 'Internship', '2026-02-15', 'Great opportunity for Data Science enthusiasts! Work on real-world ML projects. Python, pandas, scikit-learn required.', 'Open','Female', 'Master\'s Degree','3'),
(5, 'Digital Marketing Executive', 7, 'Homs', '$5,000 - $7,000', 'Full-Time', '2026-03-12', 'Develop and execute digital marketing strategies. Experience with Google Ads, Facebook Ads, and content creation required.', 'Open','Not important', 'Master\'s Degree','7'),
(5, 'Content Writer', 7, 'Remote', '$4,500 - $6,000', 'Full-Time', '2026-03-01', 'Create engaging content for blogs, social media, and marketing campaigns. SEO knowledge is a plus.', 'Open','Male', 'Master\'s Degree','3'),
(2, 'QA Engineer', 10, 'Damascus', '$7,000 - $9,500', 'Full-Time', '2026-02-20', 'Manual and automated testing expert needed. Experience with Selenium, TestNG, and JIRA required.', 'Open','Not important', 'Master\'s Degree','9');

INSERT INTO seekers (user_id, skills, education, years_of_experience, resume, gender, category_id) VALUES
(6, '4, 1, 6, 20', 'Master\'s Degree', '5 years as Backend Developer', 'resumes/rajesh_resume.pdf', 'Male', 1),
(7, '5, 4, 6, 21', 'Master\'s Degree', '2 years as Frontend Developer', 'resumes/sham_resume.pdf', 'Male', 2),
(8, '7, 1, 6, 22', 'Master\'s Degree', '3 years as Backend Developer', 'resumes/arjun_resume.pdf', 'Male', 1),
(9, '8, 5, 6, 23', 'Master\'s Degree', '4 years as UX Designer', 'resumes/neha_resume.pdf', 'Female', 8),
(7, '9, 8, 6, 24', 'Master\'s Degree', '3 years as Frontend Developer', 'resumes/priya_resume.pdf', 'Male', 7),
(8, '10, 18, 6, 25', 'Master\'s Degree', '2 years as Python Developer', 'resumes/arjun_resume.pdf', 'Female', 5),
(9, '11, 7, 6, 19', 'Master\'s Degree', '4 years as UX Designer', 'resumes/neha_resume.pdf', 'Female', 6),
(10, '12, 1, 6, 18', 'Master\'s Degree', '3 years as Full Stack Developer', 'resumes/amit_resume.pdf', 'Female', 4),
(11, '13, 2, 6, 17', 'Master\'s Degree', 'Fresher - Recent Graduate', 'resumes/zara_resume.pdf', 'Female', 3);

INSERT INTO applications (job_id, seeker_id, resume, status, applied_at) VALUES
(1, 6, 'resumes/rajesh_resume.pdf', 'Shortlisted', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(1, 8, 'resumes/arjun_resume.pdf', 'Applied', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(2, 7, 'resumes/priya_resume.pdf', 'Shortlisted', DATE_SUB(NOW(), INTERVAL 4 DAY)),
(3, 8, 'resumes/arjun_resume.pdf', 'Applied', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(4, 10, 'resumes/amit_resume.pdf', 'Applied', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(5, 8, 'resumes/arjun_resume.pdf', 'Rejected', DATE_SUB(NOW(), INTERVAL 6 DAY)),
(6, 10, 'resumes/amit_resume.pdf', 'Applied', DATE_SUB(NOW(), INTERVAL 7 DAY)),
(7, 11, 'resumes/zara_resume.pdf', 'Applied', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(8, 9, 'resumes/neha_resume.pdf', 'Applied', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(9, 7, 'resumes/priya_resume.pdf', 'Applied', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(10, 6, 'resumes/rajesh_resume.pdf', 'Applied', DATE_SUB(NOW(), INTERVAL 1 DAY));

INSERT INTO activity_log (user_id, action, created_at) VALUES
(6, 'User registered as Job Seeker', DATE_SUB(NOW(), INTERVAL 20 DAY)),
(6, 'Applied for Senior PHP Developer', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(7, 'User registered as Job Seeker', DATE_SUB(NOW(), INTERVAL 18 DAY)),
(7, 'Applied for React.js Developer', DATE_SUB(NOW(), INTERVAL 4 DAY)),
(2, 'Posted job - Senior PHP Developer', DATE_SUB(NOW(), INTERVAL 10 DAY)),
(2, 'Posted job - React.js Developer', DATE_SUB(NOW(), INTERVAL 9 DAY)),
(3, 'Posted job - Full Stack Web Developer', DATE_SUB(NOW(), INTERVAL 8 DAY)),
(6, 'Updated profile', DATE_SUB(NOW(), INTERVAL 15 DAY)),
(8, 'User registered as Job Seeker', DATE_SUB(NOW(), INTERVAL 12 DAY)),
(8, 'Applied for Python Developer', DATE_SUB(NOW(), INTERVAL 2 DAY));

INSERT INTO skills (skill_name) VALUES
('PHP'),
('Laravel'),
('JavaScript'),
('React'),
('Node.js'),
('Python'),
('Django'),
('FastAPI'),
('PostgreSQL'),
('SQL'),
('Docker'),
('Git'),
('Data Science'),
('Machine Learning'),
('TensorFlow'),
('Pandas'),
('UI/UX Design'),
('Adobe XD'),
('Figma'),
('Prototyping'),
('Research'),
('Digital Marketing'),
('SEO'),
('Content Creation'),
('Social Media Marketing'),
('Project Management'),
('Agile Methodologies'),
('Scrum'),
('Kanban')
;
";

// تنفيذ جميع الاستعلامات دفعة واحدة
if (mysqli_multi_query($conc, $sql_rest)) {
    // استهلاك جميع النتائج حتى لا يحدث تداخل
    while (mysqli_more_results($conc) && mysqli_next_result($conc)) {;}
    echo "✓ تم إدخال جميع البيانات الأخرى بنجاح.";
} else {
    echo "✗ خطأ أثناء إدخال البيانات: " . mysqli_error($conc);
}
?>