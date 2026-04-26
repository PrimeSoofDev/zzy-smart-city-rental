ALTER TABLE rental_requests MODIFY COLUMN status ENUM('pending','processing','paid','cancelled','completed','disputed') DEFAULT 'pending';
