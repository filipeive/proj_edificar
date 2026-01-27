#!/bin/bash

DEFAULT_SEEDERS="MaritalCourse2025DetailedSeeder,EdificarPackagesSeeder,Group2PackageSeeder,RegisterGroup3Seeder"

read -r -p "Modo dry-run (não executa SSH)? [s/N]: " DRY_RUN; \
read -r -p "Seeders (separados por vírgula, Enter para padrão): " SEEDERS_INPUT; \
read -r -p "Timeout SSH em segundos (Enter para 10): " SSH_TIMEOUT; \

if [ -z "$SEEDERS_INPUT" ]; then SEEDERS_INPUT="$DEFAULT_SEEDERS"; fi; \
if [ -z "$SSH_TIMEOUT" ]; then SSH_TIMEOUT=10; fi; \

SEED_CMD=""; \
IFS=',' read -ra SEEDERS <<< "$SEEDERS_INPUT"; \
for s in "${SEEDERS[@]}"; do \
  s_trim=$(echo "$s" | xargs); \
  if [ -n "$s_trim" ]; then \
    SEED_CMD="$SEED_CMD php artisan db:seed --class=$s_trim --force &&"; \
  fi; \
done; \

REMOTE_CMD="cd /var/www/html/edificar && git pull && php artisan migrate --force && $SEED_CMD php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear"; \

git add . && \
if git diff --cached --quiet; then \
  echo "Sem alterações para commit."; \
else \
  read -r -p "Fazer commit/push? [S/n]: " DO_COMMIT; \
  if [ -z "$DO_COMMIT" ] || [ "$DO_COMMIT" = "S" ] || [ "$DO_COMMIT" = "s" ]; then \
    read -r -p "Mensagem do commit (enter para padrão): " COMMIT_MSG; \
    if [ -z "$COMMIT_MSG" ]; then COMMIT_MSG="Atualiza seeders e correcoes recentes"; fi; \
    read -r -p "Prefixar data no commit? [s/N]: " ADD_DATE; \
    if [ "$ADD_DATE" = "s" ] || [ "$ADD_DATE" = "S" ]; then \
      COMMIT_MSG="$(date +%F) - $COMMIT_MSG"; \
    fi; \
    git commit -m "$COMMIT_MSG"; \
    git push; \
  else \
    echo "Commit/push ignorado."; \
  fi; \
fi && \
read -r -p "Confirmar deploy remoto? [S/n]: " DO_DEPLOY; \
if [ -z "$DO_DEPLOY" ] || [ "$DO_DEPLOY" = "S" ] || [ "$DO_DEPLOY" = "s" ]; then \
  if [ "$DRY_RUN" = "s" ] || [ "$DRY_RUN" = "S" ]; then \
    echo "Dry-run: $REMOTE_CMD"; \
  else \
    ssh -o ConnectTimeout="$SSH_TIMEOUT" -i ~/.ssh/oracle-2025 ubuntu@146.235.224.99 "$REMOTE_CMD"; \
  fi; \
else \
  echo "Deploy remoto cancelado."; \
fi
