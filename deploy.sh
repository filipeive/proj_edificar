#!/bin/bash

git add . && git commit -m "Atualiza seeders e correcoes recentes" && git push && ssh -i ~/.ssh/oracle-2025 ubuntu@146.235.224.99 "cd /var/www/html/edificar && git pull && php artisan migrate --force && php artisan db:seed --class=MaritalCourse2025DetailedSeeder --force && php artisan db:seed --class=EdificarPackagesSeeder --force && php artisan db:seed --class=Group2PackageSeeder --force && php artisan db:seed --class=RegisterGroup3Seeder --force && php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear"
