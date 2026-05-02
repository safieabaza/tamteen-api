#!/usr/bin/env bash
set -e

# ─── Colors ───────────────────────────────────────────────────────────────────
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m'

print_step()  { echo -e "\n${BLUE}━━━ $1 ${NC}"; }
print_ok()    { echo -e "  ${GREEN}✓${NC} $1"; }
print_warn()  { echo -e "  ${YELLOW}⚠${NC}  $1"; }
print_error() { echo -e "  ${RED}✗${NC} $1"; }
print_info()  { echo -e "  ${CYAN}ℹ${NC}  $1"; }

echo ""
echo -e "${PURPLE}╔══════════════════════════════════════╗${NC}"
echo -e "${PURPLE}║       Tamteen API Setup Script       ║${NC}"
echo -e "${PURPLE}║    CV Builder + ATS Analysis API     ║${NC}"
echo -e "${PURPLE}╚══════════════════════════════════════╝${NC}"

# ─── Prerequisites check ──────────────────────────────────────────────────────
print_step "Checking prerequisites"

if ! command -v php &> /dev/null; then
  print_error "PHP not found. Please install PHP 8.2+"
  exit 1
fi

PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
print_ok "PHP $PHP_VERSION found"

if ! command -v composer &> /dev/null; then
  print_error "Composer not found. Install from https://getcomposer.org"
  exit 1
fi
print_ok "Composer found"

# ─── Install dependencies ─────────────────────────────────────────────────────
print_step "Installing Composer dependencies"
composer install --no-interaction --optimize-autoloader
print_ok "Dependencies installed"

# ─── Environment setup ────────────────────────────────────────────────────────
print_step "Setting up environment"

if [ ! -f .env ]; then
  cp .env.example .env
  print_ok ".env file created from .env.example"
else
  print_warn ".env already exists, skipping copy"
fi

# ─── App key ─────────────────────────────────────────────────────────────────
if grep -q "APP_KEY=$" .env || grep -q "APP_KEY=base64:$" .env 2>/dev/null; then
  php artisan key:generate
  print_ok "Application key generated"
else
  print_warn "APP_KEY already set, skipping"
fi

# ─── JWT secret ───────────────────────────────────────────────────────────────
if ! grep -q "^JWT_SECRET=." .env 2>/dev/null; then
  php artisan jwt:secret --no-interaction
  print_ok "JWT secret generated"
else
  print_warn "JWT_SECRET already set, skipping"
fi

# ─── Database ─────────────────────────────────────────────────────────────────
print_step "Setting up database"

DB_CONNECTION=$(grep "^DB_CONNECTION=" .env | cut -d '=' -f2)

if [ "$DB_CONNECTION" = "sqlite" ]; then
  DB_PATH=$(grep "^DB_DATABASE=" .env | cut -d '=' -f2)
  if [ -z "$DB_PATH" ]; then
    DB_PATH="database/database.sqlite"
  fi
  if [ ! -f "$DB_PATH" ]; then
    touch "$DB_PATH"
    print_ok "SQLite database file created at $DB_PATH"
  else
    print_warn "SQLite database already exists"
  fi
else
  print_info "Using $DB_CONNECTION database — ensure it's running and configured in .env"
fi

# ─── Migrations & Seeding ─────────────────────────────────────────────────────
print_step "Running migrations"
php artisan migrate --no-interaction
print_ok "Migrations completed"

print_step "Seeding ATS keywords"
php artisan db:seed --class=ATSKeywordSeeder --no-interaction
print_ok "75 ATS keywords seeded"

# ─── Storage link ─────────────────────────────────────────────────────────────
print_step "Creating storage symlink"
php artisan storage:link --no-interaction 2>/dev/null || true
print_ok "Storage linked"

# ─── Cache clear ─────────────────────────────────────────────────────────────
print_step "Clearing application caches"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
print_ok "Caches cleared"

# ─── Mail reminder ───────────────────────────────────────────────────────────
print_step "Mail configuration reminder"
echo ""
print_info "To receive OTP emails, configure Mailtrap in .env:"
echo -e "    ${CYAN}MAIL_MAILER=smtp${NC}"
echo -e "    ${CYAN}MAIL_HOST=sandbox.smtp.mailtrap.io${NC}"
echo -e "    ${CYAN}MAIL_PORT=2525${NC}"
echo -e "    ${CYAN}MAIL_USERNAME=<your-mailtrap-username>${NC}"
echo -e "    ${CYAN}MAIL_PASSWORD=<your-mailtrap-password>${NC}"
echo ""
print_info "Get credentials at: https://mailtrap.io/inboxes"

# ─── Done ─────────────────────────────────────────────────────────────────────
echo ""
echo -e "${GREEN}╔══════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║          Setup completed successfully!       ║${NC}"
echo -e "${GREEN}╚══════════════════════════════════════════════╝${NC}"
echo ""
echo -e "  ${CYAN}Start the server:${NC}"
echo -e "    ${YELLOW}php artisan serve${NC}"
echo ""
echo -e "  ${CYAN}API base URL:${NC}"
echo -e "    ${YELLOW}http://localhost:8000/api${NC}"
echo ""
echo -e "  ${CYAN}Import Postman collection:${NC}"
echo -e "    ${YELLOW}postman_collection.json${NC}"
echo ""
echo -e "  ${CYAN}Endpoints:${NC}"
echo -e "    POST   ${YELLOW}/api/auth/send-otp${NC}"
echo -e "    POST   ${YELLOW}/api/auth/verify-otp${NC}"
echo -e "    GET    ${YELLOW}/api/auth/me${NC}           (Bearer token)"
echo -e "    GET    ${YELLOW}/api/cvs${NC}               (Bearer token)"
echo -e "    POST   ${YELLOW}/api/cvs${NC}               (Bearer token)"
echo -e "    GET    ${YELLOW}/api/cvs/{id}${NC}          (Bearer token)"
echo -e "    PUT    ${YELLOW}/api/cvs/{id}${NC}          (Bearer token)"
echo -e "    DELETE ${YELLOW}/api/cvs/{id}${NC}          (Bearer token)"
echo -e "    POST   ${YELLOW}/api/cvs/{id}/analyze${NC}  (Bearer token)"
echo -e "    GET    ${YELLOW}/api/cvs/{id}/analyses${NC} (Bearer token)"
echo ""
