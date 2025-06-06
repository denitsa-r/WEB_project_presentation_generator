-- Добавяне на нова колона layout
ALTER TABLE slides ADD COLUMN layout VARCHAR(50) DEFAULT 'full' AFTER type;

-- Премахване на колоната type, тъй като вече не е нужна
ALTER TABLE slides DROP COLUMN type;
 
-- Премахване на колоната style, тъй като вече не е нужна
ALTER TABLE slides DROP COLUMN style; 