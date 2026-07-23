<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - Job Portal</title>
    <link rel="icon" type="image/svg+xml" href="css/favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        :root {
            --primary-color: #0052CC;
            --success-color: #12A336;
            --info-color: #0891B2;
            --gradient-1: linear-gradient(135deg, #0052CC 0%, #0891B2 100%);
        }

        body {
            background-color: #f8fafc;
        }

        /* Hero Section */
        .hero-about {
            background: var(--gradient-1);
            color: white;
            padding: 80px 0;
            text-align: center;
        }

        .hero-about h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .hero-about p {
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
            opacity: 0.95;
        }

        /* About Content Section */
        .about-content {
            padding: 80px 0;
        }

        .about-text {
            color: #1e293b;
            line-height: 1.8;
        }

        .about-text h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 25px;
            color: var(--primary-color);
        }

        .about-text p {
            font-size: 1.05rem;
            margin-bottom: 15px;
            color: #475569;
        }

        .features-list {
            list-style: none;
            padding: 0;
            margin: 30px 0;
        }

        .features-list li {
            padding: 12px 0;
            font-size: 1.05rem;
            color: #1e293b;
        }

        .features-list li:before {
            content: "✓ ";
            color: var(--success-color);
            font-weight: 700;
            margin-right: 10px;
            font-size: 1.3rem;
        }

        /* About Image */
        .about-image {
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            height: 400px;
            object-fit: cover;
        }

        /* Stats Section */
        .stats-section {
            background: white;
            padding: 60px 0;
            border-top: 1px solid #e2e8f0;
        }

        .stat-box {
            text-align: center;
            padding: 30px;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 1.1rem;
            color: #64748b;
            font-weight: 500;
        }

        /* Team Section */
        .team-section {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 80px 0;
        }

        .team-section h2 {
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 60px;
            color: var(--primary-color);
        }

        .team-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid #e2e8f0;
        }

        .team-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
        }

        .team-card img {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 3px solid var(--primary-color);
        }

        .team-card h4 {
            font-size: 1.3rem;
            color: var(--primary-color);
            margin-bottom: 5px;
            font-weight: 700;
        }

        .team-card p {
            color: #64748b;
            margin-bottom: 15px;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s;
        }

        .social-links a:hover {
            background: var(--success-color);
            transform: scale(1.2);
        }

        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 60px;
        }

        .feature-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-top: 3px solid var(--primary-color);
            transition: all 0.3s;
        }

        .feature-card:hover {
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .feature-card h4 {
            color: var(--primary-color);
            margin-bottom: 10px;
            font-weight: 700;
        }

        .feature-card p {
            color: #64748b;
            line-height: 1.6;
        }

        /* CTA Section */
        .cta-section {
            background: var(--gradient-1);
            color: white;
            padding: 60px 0;
            text-align: center;
            margin-top: 80px;
        }

        .cta-section h2 {
            font-size: 2.2rem;
            margin-bottom: 20px;
        }

        .cta-section p {
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        .btn-cta {
            background: white;
            color: var(--primary-color);
            padding: 12px 40px;
            border-radius: 30px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-cta:hover {
            background: #f0f0f0;
            transform: scale(1.05);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-about h1 {
                font-size: 2rem;
            }

            .about-text h2 {
                font-size: 1.8rem;
            }

            .about-image {
                height: 300px;
                margin-top: 30px;
            }

            .team-section h2 {
                font-size: 1.8rem;
            }

            .stat-number {
                font-size: 2rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="css/logo.svg" alt="JobPortal" class="brand-logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                    <?php if(!isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="auth/login.php">Login</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="auth/logout.php">Logout</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-about">
        <div class="container">
            <h1>About Job Portal</h1>
            <p>Your Gateway to Career Opportunities - Connecting Talent with Employers</p>
        </div>
    </section>

    <!-- About Content -->
    <section class="about-content">
        <div class="container">
            <div class="row align-items-center gap-4">
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=500&h=400&fit=crop" alt="Job Portal" class="about-image w-100">
                </div>
                <div class="col-lg-5 about-text">
                    <h2>What is Job Portal?</h2>
                    <p>
                        Job Portal is a modern, web-based recruitment platform designed to simplify the hiring process 
                        for employers and help job seekers find their ideal career opportunities. Built with cutting-edge 
                        technology, it brings employers and talent together in one secure, user-friendly ecosystem.
                    </p>
                    <p>
                        Whether you're a growing startup, an established company, or a talented professional looking for 
                        your next opportunity, Job Portal provides the tools you need to succeed.
                    </p>
                    <ul class="features-list">
                        <li>Easy job posting and application management</li>
                        <li>Advanced candidate filtering and screening</li>
                        <li>Secure resume management system</li>
                        <li>Real-time notification and tracking</li>
                        <li>Comprehensive analytics and reporting</li>
                        <li>Mobile-responsive design for on-the-go access</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-box">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Active Jobs</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-box">
                        <div class="stat-number">2000+</div>
                        <div class="stat-label">Job Seekers</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-box">
                        <div class="stat-number">150+</div>
                        <div class="stat-label">Employers</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-box">
                        <div class="stat-number">95%</div>
                        <div class="stat-label">Satisfaction Rate</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <h2 style="text-align: center; font-size: 2.5rem; font-weight: 700; color: var(--primary-color); margin-bottom: 60px;">
                Why Choose Job Portal?
            </h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-lightning"></i></div>
                    <h4>Quick & Easy</h4>
                    <p>Post jobs and apply in minutes with our intuitive interface designed for simplicity.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-lock"></i></div>
                    <h4>Secure & Safe</h4>
                    <p>Your data is protected with enterprise-grade security and encryption protocols.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                    <h4>Smart Analytics</h4>
                    <p>Get detailed insights into your recruitment process with comprehensive reporting tools.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-mobile-alt"></i></div>
                    <h4>Mobile Ready</h4>
                    <p>Access Job Portal anytime, anywhere on any device with responsive design.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-users"></i></div>
                    <h4>Community</h4>
                    <p>Join thousands of employers and job seekers in our growing professional community.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-headset"></i></div>
                    <h4>24/7 Support</h4>
                    <p>Get help whenever you need it from our dedicated customer support team.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <div class="container">
            <h2>Our Developers</h2>
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="team-card">
                        <img src="#" alt="">
                        <h4>Ali & Ali</h4>
                        <p>Web / App Developer</p>
                        <p style="font-size: 0.9rem; color: #94a3b8;">Web & mobile app development specialist</p>
                        <div class="social-links">
                            <a href="https://www.instagram.com/_chetan_pawar__" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://github.com/Chetan-Pawar18706" target="_blank" title="GitHub"><i class="fab fa-github"></i></a>
                            <a href="mailto:chetanpawar8125@gmail.com" title="Email"><i class="fas fa-envelope"></i></a>
                            <a href="https://www.linkedin.com/in/chetan-pawarr/" target="_blank" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>Ready to Get Started?</h2>
            <p>Join thousands of employers and job seekers who are already using Job Portal</p>
            <a href="auth/login.php" class="btn-cta">Get Started Today</a>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
