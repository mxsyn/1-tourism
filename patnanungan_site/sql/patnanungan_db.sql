-- ════════════════════════════════════════════════════════════════════
-- DISCOVER PATNANUNGAN - TOURISM PORTAL DATABASE
-- Complete CMS with Admin, Content Management, and Inquiry System
-- ════════════════════════════════════════════════════════════════════

-- Create Database
CREATE DATABASE IF NOT EXISTS patnanungan_tourism;
USE patnanungan_tourism;

-- ════════════════════════════════════════════════════════════════════
-- 1. ADMIN USERS TABLE
-- ════════════════════════════════════════════════════════════════════
CREATE TABLE admin_users (
  user_id INT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  full_name VARCHAR(100),
  role ENUM('admin', 'moderator', 'editor') DEFAULT 'editor',
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  last_login TIMESTAMP NULL,
  INDEX idx_username (username),
  INDEX idx_email (email)
);

-- ════════════════════════════════════════════════════════════════════
-- 2. ATTRACTIONS TABLE
-- ════════════════════════════════════════════════════════════════════
CREATE TABLE attractions (
  attraction_id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(150) NOT NULL,
  description LONGTEXT NOT NULL,
  location VARCHAR(200),
  barangay VARCHAR(100),
  latitude DECIMAL(10, 8),
  longitude DECIMAL(11, 8),
  entry_fee DECIMAL(10, 2) DEFAULT 0,
  operating_hours VARCHAR(100),
  contact_info VARCHAR(100),
  featured_image_url VARCHAR(255),
  is_published BOOLEAN DEFAULT TRUE,
  view_count INT DEFAULT 0,
  created_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES admin_users(user_id) ON DELETE SET NULL,
  INDEX idx_name (name),
  INDEX idx_barangay (barangay),
  INDEX idx_published (is_published)
);

-- ════════════════════════════════════════════════════════════════════
-- 3. ATTRACTION TAGS (for categorization)
-- ════════════════════════════════════════════════════════════════════
CREATE TABLE attraction_tags (
  tag_id INT PRIMARY KEY AUTO_INCREMENT,
  attraction_id INT NOT NULL,
  tag_name VARCHAR(50) NOT NULL,
  FOREIGN KEY (attraction_id) REFERENCES attractions(attraction_id) ON DELETE CASCADE,
  INDEX idx_attraction (attraction_id),
  INDEX idx_tag (tag_name)
);

-- ════════════════════════════════════════════════════════════════════
-- 4. ACCOMMODATIONS TABLE
-- ════════════════════════════════════════════════════════════════════
CREATE TABLE accommodations (
  accommodation_id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(150) NOT NULL,
  description LONGTEXT NOT NULL,
  type ENUM('resort', 'hotel', 'homestay', 'guesthouse', 'beach_hut') DEFAULT 'resort',
  address VARCHAR(255),
  barangay VARCHAR(100),
  phone VARCHAR(20),
  email VARCHAR(100),
  price_per_night DECIMAL(10, 2),
  number_of_rooms INT,
  amenities TEXT,
  check_in_time VARCHAR(20) DEFAULT '14:00',
  check_out_time VARCHAR(20) DEFAULT '12:00',
  featured_image_url VARCHAR(255),
  rating DECIMAL(3, 2),
  is_published BOOLEAN DEFAULT TRUE,
  created_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES admin_users(user_id) ON DELETE SET NULL,
  INDEX idx_name (name),
  INDEX idx_type (type),
  INDEX idx_barangay (barangay),
  INDEX idx_published (is_published)
);

-- ════════════════════════════════════════════════════════════════════
-- 5. ACTIVITIES TABLE
-- ════════════════════════════════════════════════════════════════════
CREATE TABLE activities (
  activity_id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(150) NOT NULL,
  description LONGTEXT NOT NULL,
  category VARCHAR(100),
  difficulty_level ENUM('easy', 'moderate', 'challenging') DEFAULT 'moderate',
  duration_hours INT,
  price_per_person DECIMAL(10, 2),
  max_participants INT,
  best_season VARCHAR(100),
  equipment_provided TEXT,
  guide_required BOOLEAN DEFAULT FALSE,
  featured_image_url VARCHAR(255),
  is_published BOOLEAN DEFAULT TRUE,
  created_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES admin_users(user_id) ON DELETE SET NULL,
  INDEX idx_name (name),
  INDEX idx_category (category),
  INDEX idx_difficulty (difficulty_level),
  INDEX idx_published (is_published)
);

-- ════════════════════════════════════════════════════════════════════
-- 6. LOCAL FOOD/CUISINE TABLE
-- ════════════════════════════════════════════════════════════════════
CREATE TABLE local_food (
  food_id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(150) NOT NULL,
  description LONGTEXT NOT NULL,
  food_type VARCHAR(100),
  ingredients TEXT,
  preparation_method TEXT,
  is_specialty BOOLEAN DEFAULT FALSE,
  where_to_find VARCHAR(200),
  price_range VARCHAR(50),
  featured_image_url VARCHAR(255),
  is_published BOOLEAN DEFAULT TRUE,
  created_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES admin_users(user_id) ON DELETE SET NULL,
  INDEX idx_name (name),
  INDEX idx_type (food_type),
  INDEX idx_specialty (is_specialty),
  INDEX idx_published (is_published)
);

-- ════════════════════════════════════════════════════════════════════
-- 7. GALLERY IMAGES TABLE
-- ════════════════════════════════════════════════════════════════════
CREATE TABLE gallery (
  image_id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(200) NOT NULL,
  description TEXT,
  image_url VARCHAR(255) NOT NULL,
  category ENUM('beaches', 'activities', 'culture', 'food', 'community', 'other') DEFAULT 'other',
  upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  uploaded_by INT,
  is_featured BOOLEAN DEFAULT FALSE,
  is_published BOOLEAN DEFAULT TRUE,
  display_order INT DEFAULT 0,
  FOREIGN KEY (uploaded_by) REFERENCES admin_users(user_id) ON DELETE SET NULL,
  INDEX idx_category (category),
  INDEX idx_featured (is_featured),
  INDEX idx_published (is_published),
  INDEX idx_order (display_order)
);

-- ════════════════════════════════════════════════════════════════════
-- 8. INQUIRIES/CONTACT SUBMISSIONS TABLE
-- ════════════════════════════════════════════════════════════════════
CREATE TABLE inquiries (
  inquiry_id INT PRIMARY KEY AUTO_INCREMENT,
  full_name VARCHAR(100) NOT NULL,
  email_address VARCHAR(100) NOT NULL,
  inquiry_type VARCHAR(100),
  planned_visit_date DATE,
  message LONGTEXT NOT NULL,
  status ENUM('new', 'read', 'responded', 'archived') DEFAULT 'new',
  assigned_to INT,
  response_message LONGTEXT,
  response_date TIMESTAMP NULL,
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (assigned_to) REFERENCES admin_users(user_id) ON DELETE SET NULL,
  INDEX idx_email (email_address),
  INDEX idx_status (status),
  INDEX idx_date (submitted_at)
);

-- ════════════════════════════════════════════════════════════════════
-- 9. BOOKINGS TABLE (Optional - for future reservation system)
-- ════════════════════════════════════════════════════════════════════
CREATE TABLE bookings (
  booking_id INT PRIMARY KEY AUTO_INCREMENT,
  booking_reference VARCHAR(50) UNIQUE NOT NULL,
  guest_name VARCHAR(100) NOT NULL,
  guest_email VARCHAR(100) NOT NULL,
  guest_phone VARCHAR(20),
  accommodation_id INT,
  activity_id INT,
  check_in_date DATE,
  check_out_date DATE,
  activity_date DATE,
  number_of_guests INT,
  total_price DECIMAL(12, 2),
  booking_status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
  payment_status ENUM('unpaid', 'paid', 'partially_paid', 'refunded') DEFAULT 'unpaid',
  special_requests TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (accommodation_id) REFERENCES accommodations(accommodation_id) ON DELETE SET NULL,
  FOREIGN KEY (activity_id) REFERENCES activities(activity_id) ON DELETE SET NULL,
  INDEX idx_reference (booking_reference),
  INDEX idx_email (guest_email),
  INDEX idx_status (booking_status),
  INDEX idx_created (created_at)
);

-- ════════════════════════════════════════════════════════════════════
-- 10. ACTIVITY LOG TABLE (for admin audit trail)
-- ════════════════════════════════════════════════════════════════════
CREATE TABLE activity_log (
  log_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  action VARCHAR(100) NOT NULL,
  table_name VARCHAR(50),
  record_id INT,
  description TEXT,
  ip_address VARCHAR(45),
  timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES admin_users(user_id) ON DELETE SET NULL,
  INDEX idx_user (user_id),
  INDEX idx_timestamp (timestamp)
);

-- ════════════════════════════════════════════════════════════════════
-- SAMPLE DATA FOR TESTING
-- ════════════════════════════════════════════════════════════════════

-- Sample Admin User (password: admin123 hashed with bcrypt)
INSERT INTO admin_users (username, password_hash, email, full_name, role, is_active) 
VALUES ('admin', '$2y$10$R9h7cIPz0gi.URNNX3kh2OPST9/PgBkqquzi.Oo8KQUgO2t0jKMUm', 'admin@patnanungan.ph', 'Administrator', 'admin', TRUE);

-- Sample Accommodation
INSERT INTO accommodations (name, description, type, address, barangay, phone, email, price_per_night, number_of_rooms, amenities, featured_image_url, is_published, created_by)
VALUES (
  'Villa Karrine Beach Resort',
  'The hidden paradise of Patnanungan with pristine white sand beaches and crystal clear waters.',
  'resort',
  'Brgy. Poblacion, Patnanungan Island, Quezon Province, Philippines',
  'Poblacion',
  '+63 XXX XXXX XXX',
  'villakarrine@example.com',
  2500.00,
  15,
  'WiFi, Restaurant, Beach Access, Air Conditioning, Hot Water',
  'assets/vkacc.png',
  TRUE,
  1
);

-- Sample Attraction
INSERT INTO attractions (name, description, location, barangay, entry_fee, featured_image_url, is_published, created_by)
VALUES (
  'Villa Karrine Beach',
  'Patnanungan\'s most celebrated stretch of shoreline with fine white sand and calm azure waters.',
  'Brgy. Poblacion, Patnanungan Island, Quezon Province',
  'Poblacion',
  0.00,
  'assets/villakarrine.png',
  TRUE,
  1
);

-- Sample Activity
INSERT INTO activities (name, description, category, difficulty_level, duration_hours, price_per_person, featured_image_url, is_published, created_by)
VALUES (
  'Island Hopping',
  'Hire a local bangka and explore the cluster of smaller islands and sandbars surrounding Patnanungan.',
  'Water Activities',
  'moderate',
  3,
  1500.00,
  'assets/activity.png',
  TRUE,
  1
);

-- Sample Food
INSERT INTO local_food (name, description, food_type, where_to_find, is_published, created_by)
VALUES (
  'Fresh Lobsters',
  'Lobsters are a delicacy in Patnanungan, known for their sweet and succulent meat.',
  'Seafood',
  'Local Markets and Restaurants',
  TRUE,
  1
);

-- ════════════════════════════════════════════════════════════════════
-- END OF DATABASE SCHEMA
-- ════════════════════════════════════════════════════════════════════
