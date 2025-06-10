-- Промяна на ENUM стойностите
ALTER TABLE user_workspaces MODIFY COLUMN role ENUM('owner', 'member') DEFAULT 'member';

-- Промяна на съществуващите записи от 'editor' и 'viewer' на 'member'
UPDATE user_workspaces SET role = 'member' WHERE role IN ('editor', 'viewer'); 