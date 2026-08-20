# DevOps Task API

A Laravel API project using Docker, PostgreSQL, Redis, and a GitHub Actions CI/CD Pipeline (Self-Hosted Runner).

## 📋 Table of Contents

- [Overview](#overview)
- [Architecture](#architecture)
- [Prerequisites](#prerequisites)
- [Setup](#setup)
- [Development Workflow](#development-workflow)
- [Testing](#testing)
- [Git Workflow](#git-workflow)
- [CI/CD Pipeline](#cicd-pipeline)
- [Production Deployment](#production-deployment)
- [Manual Pre-Deploy (Local Build & Push)](#manual-pre-deploy-local-build--push)
- [Ports](#ports)
- [Quick Commands](#quick-commands)

## Overview

This project follows the flow below whenever code is updated:

```
Edit Code → Test Locally → Git Add → Git Commit → Git Push
   → GitHub Actions CI → Test → Build Docker Image
   → Push to GHCR → Deploy Production
   → Run Docker Containers → Health Check → Production ✅
```

## Architecture

```
                 DEVELOPMENT
                      │
                 Write Code
                      ↓
              Local Docker Test
                      ↓
        git add . → git commit → git push
                      ↓
                    GITHUB
                      ↓
              GitHub Actions
                      ↓
                   CI TEST
                ┌─────┴─────┐
              FAIL         PASS
                │           ↓
                ❌     Docker Build
                            ↓
                           GHCR
                            ↓
                   Production Deploy
                   (Self-Hosted Runner)
                            ↓
                      Docker Pull
                            ↓
                   docker compose up
                            ↓
                    Health Check
                            ↓
                       ✅ LIVE
```

## Prerequisites

- Docker Desktop (Windows)
- GitHub Self-Hosted Runner (`C:\actions-runner`)
- Git

## Setup

### 1. Start Docker Desktop

Open Docker Desktop before doing anything else, then check which containers are currently running:

```powershell
docker ps
```

### 2. Start the GitHub Self-Hosted Runner

Open a separate PowerShell window and leave it running:

```powershell
cd C:\actions-runner
.\run.cmd
```

Wait until you see:

```
Connected to GitHub
Current runner version: '2.336.0'
Listening for Jobs
```

### 3. Open the Project

```powershell
cd D:\ITE\Year_4\Devops\week_2\devops-task-api
```

### 4. Start the Development Environment

```powershell
docker compose up -d
docker compose ps
```

You should see these containers running: `devops-task-api`, `devops-postgres`, `devops-redis`

## Development Workflow

Edit the Laravel code within a structure such as:

```
app/
├── Http/Controllers/TaskController.php
├── Models/Task.php
routes/api.php
```

Example endpoints:

```
GET    /api/tasks
POST   /api/tasks
PUT    /api/tasks/{id}
DELETE /api/tasks/{id}
```

## Testing

Before pushing, always test locally first:

```powershell
docker compose exec app php artisan test
```

- `PASS` → ✅ Code is OK
- `FAIL` → Fix the issue before pushing

## Git Workflow

```powershell
git status
git add .
git commit -m "feat: add task filtering"
git push origin develop
```

## CI/CD Pipeline

When you push to `develop`, GitHub Actions (`.github/workflows/ci.yml`) runs:

```
Checkout Code → Setup PHP 8.4 → Start PostgreSQL → Start Redis
   → Composer Install → Laravel Migration → Laravel Tests
```

If all tests pass → it proceeds to build the Docker image → push it to GHCR (`ghcr.io/sopheaklaing/devops-task-api`).

## Production Deployment

`deploy.yml` uses `runs-on: self-hosted` to run directly on your Windows PC:

```powershell
docker login ghcr.io
docker pull ghcr.io/sopheaklaing/devops-task-api:1.0
docker compose -f compose.prod.yaml up -d
docker compose -f compose.prod.yaml ps
```

### Health Check

The workflow checks `http://localhost:8002` using `Invoke-WebRequest` — a `200 OK` response means the deployment succeeded.

## Ports

| Service     | Local Port |
|-------------|-----------|
| Laravel API | 8002      |
| PostgreSQL  | 5434      |
| Redis       | 6380      |

## Manual Pre-Deploy (Local Build & Push)

If you want to build and push the image manually before deploying (bypassing CI):

### 1. Start the Self-Hosted Runner

```powershell
cd C:\actions-runner
.\run.cmd
```

Keep this window open.

### 2. Build the Docker Image

```powershell
docker build -t devops-task-api:1.0.0 .
```

⚠️ Don't forget the `.` at the end of the command (build context path) — without it, Docker will error out.

### 3. Tag the Image for GHCR

```powershell
docker tag devops-task-api:1.0.0 ghcr.io/sopheaklaing/devops-task-api:1.0.0
```

### 4. Login (if not already logged in)

```powershell
docker login ghcr.io
```

### 5. Push to GHCR

```powershell
docker push ghcr.io/sopheaklaing/devops-task-api:1.0.0
```

### 6. Pull the Image on Production

```powershell
docker compose -f compose.prod.yaml pull
```

### 7. Start Production Containers

```powershell
docker compose -f compose.prod.yaml up -d
```

### 8. Verify Containers Are Running

```powershell
docker compose -f compose.prod.yaml ps
```

You should see `devops-task-api-prod`, `devops-postgres-prod`, and `devops-redis-prod` all showing `Up`.

**Flow:**

```
Runner ready → Build image → Tag for GHCR → Push to GHCR
   → Pull on prod → Start containers → Verify status ✅
```

## Quick Commands

Whenever you want to update code, just follow these steps:

```powershell
# 1. Start development
docker compose up -d

# 2. Test
docker compose exec app php artisan test

# 3. Check Git status
git status

# 4. Stage changes
git add .

# 5. Commit
git commit -m "feat: your fix"

# 6. Push
git push origin develop
```

After that, GitHub Actions will automatically continue: CI Test → Docker Build → GHCR → Deploy Production.