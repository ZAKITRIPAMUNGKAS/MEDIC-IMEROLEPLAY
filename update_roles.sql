-- Update role names from Indonesian to English
UPDATE staff_roles SET name = 'staff' WHERE name = 'staf';
UPDATE staff_roles SET name = 'manager' WHERE name = 'manajer';

-- Insert IT Support role if not exists
INSERT IGNORE INTO staff_roles (name, level, created_at, updated_at) 
VALUES ('it_support', 50, NOW(), NOW());

-- Show all roles after update
SELECT * FROM staff_roles ORDER BY level;
