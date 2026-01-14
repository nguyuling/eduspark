# EduSpark PostgreSQL + Aiven Setup Guide

This guide helps migrate from SQLite to PostgreSQL with Aiven for persistent database on Render.

## Prerequisites
- Aiven account (create at https://aiven.io)
- Access to Render dashboard
- Access to GitHub repository

## Step 1: Create Aiven PostgreSQL Cluster

1. Go to https://console.aiven.io
2. Click "Create Service"
3. Select **PostgreSQL**
4. Choose:
   - Plan: Business-4 (or smaller if cost is concern)
   - Cloud: Same region as Render (e.g., US East)
   - Service name: `eduspark-postgres` (or similar)
5. Click "Create Service"
6. Wait for cluster to be ready (5-10 minutes)

## Step 2: Get Connection Details

Once ready, go to your PostgreSQL service:
1. Click "Overview"
2. Look for **Connection information**
3. Copy these details:
   - **Host** (e.g., `pg-12345678.a.aivencloud.com`)
   - **Port** (usually 5432)
   - **Database name** (e.g., `defaultdb`)
   - **User** (e.g., `avnadmin`)
   - **Password** (click "Show" to reveal)

## Step 3: Add to Render Environment Variables

1. Go to Render dashboard
2. Select your EduSpark service
3. Go to **Environment**
4. Add these variables:

```
DB_CONNECTION=pgsql
DB_HOST=pg-12345678.a.aivencloud.com
DB_PORT=5432
DB_DATABASE=defaultdb
DB_USERNAME=avnadmin
DB_PASSWORD=your_password_here
```

5. Click "Save"
6. Render will automatically redeploy

## Step 4: Run Database Migrations

Once Render redeploys with new environment variables:

**Option A: Via Render Console (Recommended for owner)**
1. In Render dashboard, click "Shell"
2. Run:
```bash
php artisan migrate --force
php artisan db:seed
```

**Option B: Local testing first (recommended)**
1. Update your local `.env` file with test PostgreSQL credentials
2. Run locally:
```bash
php artisan migrate:fresh --seed
```
3. Verify everything works
4. Push code and let owner run migrations on Render

## Step 5: Verify Migration Success

Run this command to check:
```bash
php artisan tinker
>>> \App\Models\Lesson::count()
>>> \App\Models\User::count()
```

Should return counts of your data.

## Troubleshooting

### Connection refused?
- Check DB_HOST, DB_PORT, DB_USERNAME in Render environment
- Verify Aiven firewall allows Render IP

### Password doesn't work?
- Check Aiven password in dashboard (copy again, no special chars issues)
- Verify password in Render env var matches exactly

### Data lost after migration?
- Previous SQLite data won't auto-migrate (different databases)
- Use seeders or manually re-add important data
- For lesson PDFs: they're already in GitHub/DigitalOcean Spaces

## Next Steps: DigitalOcean Spaces

After PostgreSQL is working, set up DigitalOcean Spaces for PDF storage:

```
DO_SPACES_KEY=your_access_key
DO_SPACES_SECRET=your_secret_key
DO_SPACES_BUCKET=eduspark-lessons
DO_SPACES_REGION=nyc3
DO_SPACES_ENDPOINT=https://nyc3.digitaloceanspaces.com
DO_SPACES_URL=https://eduspark-lessons.nyc3.digitaloceanspaces.com
```

Then upload the 10 lesson PDFs to the Spaces bucket.

## Cost Estimate

- **Aiven PostgreSQL**: ~$20-50/month (Business-4)
- **DigitalOcean Spaces**: ~$5/month (250GB included)
- **Total**: ~$25-55/month for full persistence

## Support

If issues arise, check:
1. Aiven service status
2. Render environment variables (no typos)
3. Database connection with `php artisan migrate --force`
4. Check Render logs for errors
