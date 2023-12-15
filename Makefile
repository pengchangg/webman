createMigrate:
	@echo "Creating migration file..."
	@read -p "Enter Model Name:" name; \
	php vendor/bin/phinx create $$name

migrate:
	@echo "Migrating..."
	php vendor/bin/phinx migrate

migrateStatus:
	@echo "Migrate status..."
	php vendor/bin/phinx status