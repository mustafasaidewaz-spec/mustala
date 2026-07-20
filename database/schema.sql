-- Mustala Portfolio Database
-- Import this file into MySQL / phpMyAdmin

CREATE DATABASE IF NOT EXISTS mustala_portfolio
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE mustala_portfolio;

-- Admin users
CREATE TABLE IF NOT EXISTS admins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default admin: username=admin  password=password  (CHANGE after first login)
INSERT INTO admins (username, email, password) VALUES
('admin', 'mustafasaidewaz@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Categories (blog, portfolio, gallery)
CREATE TABLE IF NOT EXISTS categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE,
  type ENUM('blog','portfolio','gallery') NOT NULL DEFAULT 'blog',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO categories (name, slug, type) VALUES
('Web Design', 'web-design', 'blog'),
('Development', 'development', 'blog'),
('WordPress', 'wordpress', 'blog'),
('Freelance Tips', 'freelance-tips', 'blog'),
('Frontend', 'frontend', 'portfolio'),
('WordPress', 'wordpress-projects', 'portfolio'),
('E-commerce', 'ecommerce', 'portfolio'),
('Landing Pages', 'landing-pages', 'portfolio'),
('Web Apps', 'web-apps', 'portfolio'),
('Images', 'images', 'gallery'),
('Videos', 'videos', 'gallery'),
('Projects', 'projects', 'gallery');

-- Portfolio projects
CREATE TABLE IF NOT EXISTS projects (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(220) NOT NULL UNIQUE,
  description TEXT,
  content LONGTEXT,
  category_id INT UNSIGNED NULL,
  featured_image VARCHAR(255) DEFAULT NULL,
  video_url VARCHAR(500) DEFAULT NULL,
  tech_stack VARCHAR(500) DEFAULT NULL,
  live_demo VARCHAR(500) DEFAULT NULL,
  github_url VARCHAR(500) DEFAULT NULL,
  is_featured TINYINT(1) DEFAULT 0,
  status ENUM('draft','published') DEFAULT 'published',
  views INT UNSIGNED DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Project screenshots
CREATE TABLE IF NOT EXISTS project_images (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  project_id INT UNSIGNED NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  sort_order INT DEFAULT 0,
  FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Blog posts
CREATE TABLE IF NOT EXISTS blog_posts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(220) NOT NULL UNIQUE,
  excerpt TEXT,
  content LONGTEXT,
  category_id INT UNSIGNED NULL,
  featured_image VARCHAR(255) DEFAULT NULL,
  video_url VARCHAR(500) DEFAULT NULL,
  tags VARCHAR(500) DEFAULT NULL,
  author VARCHAR(100) DEFAULT 'Mustafa Saide',
  is_featured TINYINT(1) DEFAULT 0,
  status ENUM('draft','published') DEFAULT 'published',
  views INT UNSIGNED DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Blog comments
CREATE TABLE IF NOT EXISTS blog_comments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  post_id INT UNSIGNED NOT NULL,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  comment TEXT NOT NULL,
  is_approved TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Gallery items
CREATE TABLE IF NOT EXISTS gallery (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  description TEXT,
  file_path VARCHAR(255) NOT NULL,
  media_type ENUM('image','video') NOT NULL DEFAULT 'image',
  category_id INT UNSIGNED NULL,
  thumbnail VARCHAR(255) DEFAULT NULL,
  sort_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Testimonials
CREATE TABLE IF NOT EXISTS testimonials (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  client_name VARCHAR(100) NOT NULL,
  company VARCHAR(150) DEFAULT NULL,
  role VARCHAR(100) DEFAULT NULL,
  content TEXT NOT NULL,
  rating TINYINT UNSIGNED DEFAULT 5,
  client_image VARCHAR(255) DEFAULT NULL,
  company_logo VARCHAR(255) DEFAULT NULL,
  is_featured TINYINT(1) DEFAULT 1,
  status ENUM('draft','published') DEFAULT 'published',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Contact messages
CREATE TABLE IF NOT EXISTS contact_messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  subject VARCHAR(200) NOT NULL,
  message TEXT NOT NULL,
  is_read TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Site settings
CREATE TABLE IF NOT EXISTS settings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(100) NOT NULL UNIQUE,
  setting_value TEXT
) ENGINE=InnoDB;

INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'Mustala'),
('site_tagline', 'Freelance Web Developer'),
('owner_name', 'Mustafa Saide'),
('email', 'mustafasaidewaz@gmail.com'),
('whatsapp', '258846551778'),
('github', 'https://github.com/'),
('linkedin', 'https://www.linkedin.com/in/mustafa-saide-a88090290'),
('facebook', 'https://www.facebook.com/profile.php?id=61554800685289'),
('location', 'Palma, Mozambique'),
('map_embed', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126746.0!2d40.3!3d-10.77!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x18fa6b0!2sPalma%2C%20Mozambique!5e0!3m2!1sen!2s!4v1'),
('cv_path', 'assets/uploads/Mustafa_Saide_CV.pdf'),
('theme_crimson', '#dc143c'),
('theme_crimson_deep', '#b01030'),
('theme_dark', '#111111'),
('theme_card_dark', '#222222');

-- Seed portfolio projects
INSERT INTO projects (title, slug, description, content, category_id, featured_image, tech_stack, live_demo, github_url, is_featured, status) VALUES
('Modern Business Website', 'modern-business-website',
 'Responsive business website with clean UI and contact integration.',
 '<p>A fully responsive business website built for a local client. Features include service pages, contact form, SEO optimization, and mobile-first design.</p>',
 5, NULL, 'HTML, CSS, JavaScript, PHP', '#', '#', 1, 'published'),
('WooCommerce Online Store', 'woocommerce-online-store',
 'Complete e-commerce store with product management and payments.',
 '<p>Custom WooCommerce store with product catalog, cart, checkout, and payment gateway integration.</p>',
 7, NULL, 'WordPress, WooCommerce, PHP, MySQL', '#', '#', 1, 'published'),
('Landing Page for Startup', 'landing-page-startup',
 'High-converting landing page designed for lead generation.',
 '<p>Single-page marketing site with hero, features, testimonials, and strong CTAs optimized for conversions.</p>',
 8, NULL, 'HTML, CSS, JavaScript', '#', '#', 1, 'published'),
('WordPress Corporate Site', 'wordpress-corporate-site',
 'Custom WordPress theme for a corporate client.',
 '<p>Custom theme development with editable sections, blog, and multi-language readiness.</p>',
 6, NULL, 'WordPress, PHP, CSS, JS', '#', '#', 0, 'published');

-- Seed blog posts
INSERT INTO blog_posts (title, slug, excerpt, content, category_id, tags, is_featured, status) VALUES
('How to Build a Fast Responsive Website', 'build-fast-responsive-website',
 'Practical tips for creating websites that load quickly and look great on every device.',
 '<p>Performance and responsiveness are essential for modern websites. Start with semantic HTML, minimize CSS, defer JavaScript, and use optimized images.</p><p>Use a mobile-first approach, compress assets, and leverage lazy loading for media.</p>',
 2, 'performance,responsive,html,css', 1, 'published'),
('Why WordPress Is Great for Small Business', 'wordpress-for-small-business',
 'Explore why WordPress remains one of the best platforms for small business websites.',
 '<p>WordPress powers a huge share of the web because it is flexible, SEO-friendly, and easy to manage. With themes and plugins you can launch quickly without sacrificing quality.</p>',
 3, 'wordpress,business,cms', 1, 'published'),
('Freelance Web Development Tips for 2026', 'freelance-web-dev-tips-2026',
 'Advice for freelancers looking to grow their web development career this year.',
 '<p>Build a strong portfolio, communicate clearly with clients, price your packages transparently, and keep learning modern tools and best practices.</p>',
 4, 'freelance,career,tips', 1, 'published');

-- Seed testimonials
INSERT INTO testimonials (client_name, company, role, content, rating, is_featured, status) VALUES
('Ana Silva', 'Silva Traders', 'Owner',
 'Mustafa delivered a beautiful, fast website for my business. Communication was clear and the result exceeded my expectations.',
 5, 1, 'published'),
('João Mabunda', 'Coastal Retail', 'Manager',
 'Our WooCommerce store was set up professionally with products, payments, and a mobile-friendly design. Highly recommended.',
 5, 1, 'published'),
('Maria Santos', 'Startup Hub', 'Founder',
 'The landing page we received converts well and looks modern. Great work and fast turnaround.',
 4, 1, 'published');
