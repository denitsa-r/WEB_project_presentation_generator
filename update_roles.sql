ALTER TABLE user_workspaces MODIFY COLUMN role ENUM('owner', 'member') DEFAULT 'member';

UPDATE user_workspaces SET role = 'member' WHERE role IN ('editor', 'viewer'); 