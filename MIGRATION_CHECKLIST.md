# PostgreSQL + Aiven Migration Checklist

## For You (Developer) - Local Testing
- [ ] Read `AIVEN_POSTGRESQL_SETUP.md`
- [ ] Create test Aiven PostgreSQL account
- [ ] Test PostgreSQL locally with test credentials
- [ ] Run `php artisan migrate:fresh --seed` successfully
- [ ] Verify lesson data loads with correct file paths
- [ ] Test lesson preview/download locally
- [ ] Document any issues found
- [ ] Push all code changes to GitHub

## For Owner - Aiven Setup
- [ ] Create Aiven account (https://aiven.io)
- [ ] Create PostgreSQL cluster (Business-4 or similar)
- [ ] Copy connection credentials:
  - [ ] Host: ________________
  - [ ] Port: ________________
  - [ ] Database: ________________
  - [ ] Username: ________________
  - [ ] Password: ________________

## For Owner - Render Configuration
- [ ] Go to Render dashboard → EduSpark service
- [ ] Add environment variables:
  - [ ] DB_CONNECTION=pgsql
  - [ ] DB_HOST=(Aiven host)
  - [ ] DB_PORT=5432
  - [ ] DB_DATABASE=(Aiven database)
  - [ ] DB_USERNAME=(Aiven username)
  - [ ] DB_PASSWORD=(Aiven password)
- [ ] Save and wait for redeploy (5-10 mins)

## For Owner - Database Migration
- [ ] Open Render Shell console
- [ ] Run: `php artisan migrate --force`
- [ ] Run: `php artisan db:seed`
- [ ] Verify success

## For Owner - Optional: DigitalOcean Spaces
- [ ] Create DigitalOcean Spaces bucket
- [ ] Generate API credentials
- [ ] Add environment variables to Render:
  - [ ] DO_SPACES_KEY
  - [ ] DO_SPACES_SECRET
  - [ ] DO_SPACES_BUCKET
  - [ ] DO_SPACES_REGION
  - [ ] DO_SPACES_ENDPOINT
  - [ ] DO_SPACES_URL
- [ ] Upload 10 lesson PDFs to `lessons/` folder

## Verification
- [ ] Test lesson list page loads
- [ ] Test lesson preview (should show PDF)
- [ ] Test lesson download (should download PDF)
- [ ] Check Render logs for errors
- [ ] Monitor database performance

## Rollback Plan (if needed)
- If Aiven setup fails:
  1. Remove PostgreSQL env vars from Render
  2. Render will fall back to SQLite
  3. Previous functionality restored
- No data loss (SQLite file still exists)
