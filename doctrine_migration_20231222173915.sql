-- Doctrine Migration File Generated on 2023-12-22 17:39:15

-- Version database\migrations\Version20231222023007
CREATE TABLE test (id INT AUTO_INCREMENT NOT NULL, name CHAR(32) NOT NULL, age1 INT DEFAULT 10 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
-- Version database\migrations\Version20231222023007 update table metadata;
INSERT INTO migrations (version, executed_at, execution_time) VALUES ('database\\migrations\\Version20231222023007', '2023-12-22 17:39:15', 0);

-- Version database\migrations\Version20231222025405
ALTER TABLE test ADD uuid CHAR(32) NOT NULL, CHANGE age1 age INT DEFAULT 10 NOT NULL;
CREATE UNIQUE INDEX test_uuid_unique ON test (uuid);
-- Version database\migrations\Version20231222025405 update table metadata;
INSERT INTO migrations (version, executed_at, execution_time) VALUES ('database\\migrations\\Version20231222025405', '2023-12-22 17:39:15', 0);
