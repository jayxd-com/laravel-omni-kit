# 🧪 Production Stack Testing Guide

This guide explains how to test the **actual production Docker images** on your local machine before deploying to Dokploy. This ensures that your Octane, Reverb, Horizon, and Scheduler services are all working correctly in a multi-container environment.

## 📋 Prerequisites

- **Docker** and **Docker Compose** installed.
- **Sail must be stopped** to avoid port conflicts:
  ```bash
  ./vendor/bin/sail stop
  ```

---

## 🚀 How to Run the Test Stack

Navigate to the `backend/` directory and run the dedicated test-prod compose file:

```bash
cd backend
docker compose -f docker-compose.test-prod.yml up --build
```

### What happens next?
- **Build**: Docker will build your production `Dockerfile` (installing Octane, extensions, etc.).
- **Infrastructure**: Postgres, Redis, and Meilisearch containers will start.
- **Services**: Your 4 production services (Web, Reverb, Horizon, Scheduler) will start.

---

## 🔍 How to Verify the Services

### 1. Web Application (Octane/FrankenPHP)
- **URL**: [http://localhost:8000](http://localhost:8000)
- **Check**: You should see the Laravel welcome page or your API response.
- **Logs**: Look for `Octane server started` in the terminal.

### 2. Horizon Dashboard (Queue)
- **URL**: [http://localhost:8000/horizon](http://localhost:8000/horizon)
- **Check**: Ensure the status is **Active**.
- **Test**: Dispatch a job via tinker to see it processed in the logs:
  ```bash
  docker compose -f docker-compose.test-prod.yml exec web php artisan tinker --execute="App\Jobs\TestJob::dispatch()"
  ```

### 3. Reverb (WebSockets)
- **URL**: `ws://localhost:8080`
- **Check**: Open your browser console's **Network > WS** tab. You should see a successful connection to the Reverb server.
- **Logs**: Look for `Starting server on 0.0.0.0:8080`.

### 4. Scheduler
- **Check**: Watch the logs for the `scheduler` container. Every minute, you should see it checking for tasks:
  ```text
  scheduler-1 | Running [php artisan schedule:run] ...
  ```

---

## 🛠 Useful Commands during Testing

### Running Migrations
Since this is a fresh database, you'll need to run migrations inside the running container:
```bash
docker compose -f docker-compose.test-prod.yml exec web php artisan migrate --seed
```

### Entering the Container
To explore the production environment:
```bash
docker compose -f docker-compose.test-prod.yml exec web sh
```

### Viewing Logs for a Specific Service
If the combined logs are too noisy:
```bash
docker compose -f docker-compose.test-prod.yml logs -f reverb
```

---

## 🧹 Cleaning Up

When you are finished testing, stop the containers and remove the volumes:

```bash
docker compose -f docker-compose.test-prod.yml down -v
```

---

## 💡 Pro-Tip
Use this guide every time you make a major change to your `Dockerfile` or `docker-compose.yml` before pushing to **Dokploy**.
