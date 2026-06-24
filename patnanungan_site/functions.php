<?php
// ════════════════════════════════════════════════════════════════════
// PATNANUNGAN TOURISM PORTAL - CRUD OPERATIONS LIBRARY
// Functions for managing all content types
// ════════════════════════════════════════════════════════════════════

require_once 'config.php';

// ────────────────────────────────────────────────────────────────────
// ATTRACTIONS CRUD
// ────────────────────────────────────────────────────────────────────

/**
 * Get all attractions (with optional filtering)
 */
function get_attractions($pdo, $filters = []) {
    $query = "SELECT * FROM attractions WHERE 1=1";
    $params = [];
    
    if (isset($filters['published'])) {
        $query .= " AND is_published = ?";
        $params[] = (bool)$filters['published'];
    }
    
    if (isset($filters['barangay'])) {
        $query .= " AND barangay = ?";
        $params[] = $filters['barangay'];
    }
    
    if (isset($filters['search'])) {
        $query .= " AND (name LIKE ? OR description LIKE ?)";
        $search = '%' . $filters['search'] . '%';
        $params[] = $search;
        $params[] = $search;
    }
    
    $query .= " ORDER BY created_at DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Get single attraction by ID
 */
function get_attraction($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM attractions WHERE attraction_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Create new attraction
 */
function create_attraction($pdo, $data) {
    $stmt = $pdo->prepare("
        INSERT INTO attractions 
        (name, description, location, barangay, latitude, longitude, entry_fee, operating_hours, contact_info, featured_image_url, is_published, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $result = $stmt->execute([
        $data['name'],
        $data['description'],
        $data['location'] ?? null,
        $data['barangay'] ?? null,
        $data['latitude'] ?? null,
        $data['longitude'] ?? null,
        $data['entry_fee'] ?? 0,
        $data['operating_hours'] ?? null,
        $data['contact_info'] ?? null,
        $data['featured_image_url'] ?? null,
        $data['is_published'] ?? true,
        $_SESSION['user_id']
    ]);
    
    return $result ? $pdo->lastInsertId() : false;
}

/**
 * Update attraction
 */
function update_attraction($pdo, $id, $data) {
    $stmt = $pdo->prepare("
        UPDATE attractions SET
        name = ?, description = ?, location = ?, barangay = ?, 
        latitude = ?, longitude = ?, entry_fee = ?, operating_hours = ?, 
        contact_info = ?, featured_image_url = ?, is_published = ?
        WHERE attraction_id = ?
    ");
    
    return $stmt->execute([
        $data['name'],
        $data['description'],
        $data['location'] ?? null,
        $data['barangay'] ?? null,
        $data['latitude'] ?? null,
        $data['longitude'] ?? null,
        $data['entry_fee'] ?? 0,
        $data['operating_hours'] ?? null,
        $data['contact_info'] ?? null,
        $data['featured_image_url'] ?? null,
        $data['is_published'] ?? true,
        $id
    ]);
}

/**
 * Delete attraction
 */
function delete_attraction($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM attractions WHERE attraction_id = ?");
    return $stmt->execute([$id]);
}

// ────────────────────────────────────────────────────────────────────
// ACCOMMODATIONS CRUD
// ────────────────────────────────────────────────────────────────────

/**
 * Get all accommodations
 */
function get_accommodations($pdo, $filters = []) {
    $query = "SELECT * FROM accommodations WHERE 1=1";
    $params = [];
    
    if (isset($filters['published'])) {
        $query .= " AND is_published = ?";
        $params[] = (bool)$filters['published'];
    }
    
    if (isset($filters['type'])) {
        $query .= " AND type = ?";
        $params[] = $filters['type'];
    }
    
    if (isset($filters['search'])) {
        $query .= " AND (name LIKE ? OR description LIKE ?)";
        $search = '%' . $filters['search'] . '%';
        $params[] = $search;
        $params[] = $search;
    }
    
    $query .= " ORDER BY created_at DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Get single accommodation
 */
function get_accommodation($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM accommodations WHERE accommodation_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Create accommodation
 */
function create_accommodation($pdo, $data) {
    $stmt = $pdo->prepare("
        INSERT INTO accommodations 
        (name, description, type, address, barangay, phone, email, price_per_night, number_of_rooms, 
         amenities, check_in_time, check_out_time, featured_image_url, rating, is_published, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    return $stmt->execute([
        $data['name'],
        $data['description'],
        $data['type'],
        $data['address'] ?? null,
        $data['barangay'] ?? null,
        $data['phone'] ?? null,
        $data['email'] ?? null,
        $data['price_per_night'] ?? 0,
        $data['number_of_rooms'] ?? null,
        $data['amenities'] ?? null,
        $data['check_in_time'] ?? '14:00',
        $data['check_out_time'] ?? '12:00',
        $data['featured_image_url'] ?? null,
        $data['rating'] ?? null,
        $data['is_published'] ?? true,
        $_SESSION['user_id']
    ]) ? $pdo->lastInsertId() : false;
}

/**
 * Update accommodation
 */
function update_accommodation($pdo, $id, $data) {
    $stmt = $pdo->prepare("
        UPDATE accommodations SET
        name = ?, description = ?, type = ?, address = ?, barangay = ?, 
        phone = ?, email = ?, price_per_night = ?, number_of_rooms = ?, 
        amenities = ?, check_in_time = ?, check_out_time = ?, 
        featured_image_url = ?, rating = ?, is_published = ?
        WHERE accommodation_id = ?
    ");
    
    return $stmt->execute([
        $data['name'],
        $data['description'],
        $data['type'],
        $data['address'] ?? null,
        $data['barangay'] ?? null,
        $data['phone'] ?? null,
        $data['email'] ?? null,
        $data['price_per_night'] ?? 0,
        $data['number_of_rooms'] ?? null,
        $data['amenities'] ?? null,
        $data['check_in_time'] ?? '14:00',
        $data['check_out_time'] ?? '12:00',
        $data['featured_image_url'] ?? null,
        $data['rating'] ?? null,
        $data['is_published'] ?? true,
        $id
    ]);
}

/**
 * Delete accommodation
 */
function delete_accommodation($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM accommodations WHERE accommodation_id = ?");
    return $stmt->execute([$id]);
}

// ────────────────────────────────────────────────────────────────────
// ACTIVITIES CRUD
// ────────────────────────────────────────────────────────────────────

/**
 * Get all activities
 */
function get_activities($pdo, $filters = []) {
    $query = "SELECT * FROM activities WHERE 1=1";
    $params = [];
    
    if (isset($filters['published'])) {
        $query .= " AND is_published = ?";
        $params[] = (bool)$filters['published'];
    }
    
    if (isset($filters['category'])) {
        $query .= " AND category = ?";
        $params[] = $filters['category'];
    }
    
    if (isset($filters['search'])) {
        $query .= " AND (name LIKE ? OR description LIKE ?)";
        $search = '%' . $filters['search'] . '%';
        $params[] = $search;
        $params[] = $search;
    }
    
    $query .= " ORDER BY created_at DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Get single activity
 */
function get_activity($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM activities WHERE activity_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Create activity
 */
function create_activity($pdo, $data) {
    $stmt = $pdo->prepare("
        INSERT INTO activities 
        (name, description, category, difficulty_level, duration_hours, price_per_person, 
         max_participants, best_season, equipment_provided, guide_required, featured_image_url, is_published, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    return $stmt->execute([
        $data['name'],
        $data['description'],
        $data['category'] ?? null,
        $data['difficulty_level'] ?? 'moderate',
        $data['duration_hours'] ?? null,
        $data['price_per_person'] ?? 0,
        $data['max_participants'] ?? null,
        $data['best_season'] ?? null,
        $data['equipment_provided'] ?? null,
        $data['guide_required'] ?? false,
        $data['featured_image_url'] ?? null,
        $data['is_published'] ?? true,
        $_SESSION['user_id']
    ]) ? $pdo->lastInsertId() : false;
}

/**
 * Update activity
 */
function update_activity($pdo, $id, $data) {
    $stmt = $pdo->prepare("
        UPDATE activities SET
        name = ?, description = ?, category = ?, difficulty_level = ?, 
        duration_hours = ?, price_per_person = ?, max_participants = ?, 
        best_season = ?, equipment_provided = ?, guide_required = ?, 
        featured_image_url = ?, is_published = ?
        WHERE activity_id = ?
    ");
    
    return $stmt->execute([
        $data['name'],
        $data['description'],
        $data['category'] ?? null,
        $data['difficulty_level'] ?? 'moderate',
        $data['duration_hours'] ?? null,
        $data['price_per_person'] ?? 0,
        $data['max_participants'] ?? null,
        $data['best_season'] ?? null,
        $data['equipment_provided'] ?? null,
        $data['guide_required'] ?? false,
        $data['featured_image_url'] ?? null,
        $data['is_published'] ?? true,
        $id
    ]);
}

/**
 * Delete activity
 */
function delete_activity($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM activities WHERE activity_id = ?");
    return $stmt->execute([$id]);
}

// ────────────────────────────────────────────────────────────────────
// LOCAL FOOD CRUD
// ────────────────────────────────────────────────────────────────────

/**
 * Get all food items
 */
function get_food_items($pdo, $filters = []) {
    $query = "SELECT * FROM local_food WHERE 1=1";
    $params = [];
    
    if (isset($filters['published'])) {
        $query .= " AND is_published = ?";
        $params[] = (bool)$filters['published'];
    }
    
    if (isset($filters['search'])) {
        $query .= " AND (name LIKE ? OR description LIKE ?)";
        $search = '%' . $filters['search'] . '%';
        $params[] = $search;
        $params[] = $search;
    }
    
    $query .= " ORDER BY created_at DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Create food item
 */
function create_food_item($pdo, $data) {
    $stmt = $pdo->prepare("
        INSERT INTO local_food 
        (name, description, food_type, ingredients, preparation_method, is_specialty, where_to_find, price_range, featured_image_url, is_published, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    return $stmt->execute([
        $data['name'],
        $data['description'],
        $data['food_type'] ?? null,
        $data['ingredients'] ?? null,
        $data['preparation_method'] ?? null,
        $data['is_specialty'] ?? false,
        $data['where_to_find'] ?? null,
        $data['price_range'] ?? null,
        $data['featured_image_url'] ?? null,
        $data['is_published'] ?? true,
        $_SESSION['user_id']
    ]) ? $pdo->lastInsertId() : false;
}

// ────────────────────────────────────────────────────────────────────
// INQUIRIES MANAGEMENT
// ────────────────────────────────────────────────────────────────────

/**
 * Get all inquiries with optional filtering
 */
function get_inquiries($pdo, $filters = []) {
    $query = "SELECT * FROM inquiries WHERE 1=1";
    $params = [];
    
    if (isset($filters['status'])) {
        $query .= " AND status = ?";
        $params[] = $filters['status'];
    }
    
    if (isset($filters['search'])) {
        $query .= " AND (full_name LIKE ? OR email_address LIKE ?)";
        $search = '%' . $filters['search'] . '%';
        $params[] = $search;
        $params[] = $search;
    }
    
    $query .= " ORDER BY submitted_at DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Get single inquiry
 */
function get_inquiry($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM inquiries WHERE inquiry_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Update inquiry status
 */
function update_inquiry_status($pdo, $id, $status) {
    $stmt = $pdo->prepare("UPDATE inquiries SET status = ? WHERE inquiry_id = ?");
    return $stmt->execute([$status, $id]);
}

/**
 * Add response to inquiry
 */
function respond_to_inquiry($pdo, $id, $response_message) {
    $stmt = $pdo->prepare("
        UPDATE inquiries 
        SET response_message = ?, response_date = CURRENT_TIMESTAMP, status = 'responded'
        WHERE inquiry_id = ?
    ");
    return $stmt->execute([$response_message, $id]);
}

// ────────────────────────────────────────────────────────────────────
// GALLERY MANAGEMENT
// ────────────────────────────────────────────────────────────────────

/**
 * Get gallery images
 */
function get_gallery_images($pdo, $filters = []) {
    $query = "SELECT * FROM gallery WHERE 1=1";
    $params = [];
    
    if (isset($filters['category'])) {
        $query .= " AND category = ?";
        $params[] = $filters['category'];
    }
    
    if (isset($filters['published'])) {
        $query .= " AND is_published = ?";
        $params[] = (bool)$filters['published'];
    }
    
    $query .= " ORDER BY display_order ASC, upload_date DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Upload gallery image
 */
function upload_gallery_image($pdo, $data) {
    $stmt = $pdo->prepare("
        INSERT INTO gallery (title, description, image_url, category, uploaded_by, is_published, display_order)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    return $stmt->execute([
        $data['title'],
        $data['description'] ?? null,
        $data['image_url'],
        $data['category'] ?? 'other',
        $_SESSION['user_id'],
        $data['is_published'] ?? true,
        $data['display_order'] ?? 0
    ]) ? $pdo->lastInsertId() : false;
}

?>
