@echo off
docker exec -it 5e45d594ec37 /var/www/html/wp-content/plugins/src/vendor/bin/phpunit -c wp-content/plugins/src/phpunit.xml.dist
