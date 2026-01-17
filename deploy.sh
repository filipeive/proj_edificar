#!/bin/bash

ssh -i ~/.ssh/oracle-2025 ubuntu@146.235.224.99 "cd /var/www/html/edificar && git pull && php artisan migrate --force && php artisan db:seed --class=MaritalCourse2025DetailedSeeder --force && php artisan config:clear && php artisan cache:clear && php artisan view:clear"
