# 🐳 Docker & Deployment Guide

This project provides a multi-layered Docker strategy to support the entire development lifecycle, from local coding to production-ready VPS deployments via Dokploy.

---

## 🏗 1. Local Development (Laravel Sail)
**Files:** `compose.yaml`, `vendor/bin/sail`

Laravel Sail is the primary tool for daily development. It provides a rich set of services (Postgres, Redis, Mailpit, Meilisearch) and mounts your local code directly into the container so you can see changes instantly.

*   **When to use:** Daily coding, running tests locally, adding new features.
*   **Command:** `./vendor/bin/sail up -d`
*   **Key Feature:** Code is "hot-reloaded" via volumes. Uses standard PHP-FPM for easy debugging.

---

## 🧪 2. Production Mirror / Local Test (Traefik + Octane)
**Files:** `docker-compose.local-test.yml`, `Dockerfile`

This configuration is designed to **mimic your production environment** as closely as possible on your local machine. It uses the same high-performance `Dockerfile` as production but provides its own infrastructure (DB/Redis) so it's self-contained.

*   **When to use:** Verifying that your production build works, testing WebSockets (Reverb) through a proxy, performance testing Octane/FrankenPHP.
*   **Infrastructure:** Includes its own Postgres and Redis.
*   **Routing:** Uses **Traefik** to provide local domains:
    *   `http://omni-kit.localhost` (API/Web)
    *   `http://monitor.omni-kit.localhost` (Traefik Dashboard)

### 🛠 Essential Commands

*   **Start the environment:**
    ```bash
    docker compose -f docker-compose.local-test.yml up -d
    ```
*   **Stop and remove containers:**
    ```bash
    docker compose -f docker-compose.local-test.yml down
    ```
*   **Rebuild and start (after code changes):**
    ```bash
    docker compose -f docker-compose.local-test.yml up -d --build
    ```
*   **View live logs:**
    ```bash
    docker compose -f docker-compose.local-test.yml logs -f
    ```
*   **Check container status:**
    ```bash
    docker compose -f docker-compose.local-test.yml ps
    ```

---

## 🚀 3. Production Deployment (Dokploy)
**Files:** `docker-compose.yml`, `Dockerfile`

This is the final, optimized configuration for your VPS. It is "lean" because it relies on Dokploy-managed infrastructure for databases and networking.

*   **When to use:** Deploying to a live VPS via Dokploy.
*   **Key Differences:**
    *   **External Network:** Joins `dokploy-network` to communicate with Dokploy-managed Postgres/Redis.
    *   **Octane/FrankenPHP:** Optimized for high-concurrency production traffic.
    *   **Horizon/Reverb:** Dedicated containers with production-grade `stop_signals` and health checks.
*   **Scaling:** Designed to let you scale `web` or `horizon` workers independently in the Dokploy UI.

---

## 🛠 Technical Details

### The Dockerfile
The `Dockerfile` is a **multi-stage build**:
1.  **Stage 1 (Builder):** Uses Alpine Linux to compile assets (Vite) and install Composer dependencies.
2.  **Stage 2 (Production):** Uses the `dunglas/frankenphp` base image. It is significantly faster than Nginx + PHP-FPM and handles the Octane runtime natively.

### Octane & FrankenPHP
We use **Laravel Octane** with the **FrankenPHP** server. This keeps your application in memory, resulting in sub-10ms response times.
*   **Admin Port:** Always set to `2019` in the start command.
*   **Binary:** The Dockerfile automatically symlinks the container-native binary to prevent "binary not found" errors during local testing.

### Reverb (WebSockets)
Reverb runs in its own container to ensure WebSocket connections stay stable during application updates or scaling events. It listens on port `8080` internally and is exposed via the reverse proxy (Traefik) on port `80` (Standard HTTP) or `443` (HTTPS).

---

## 📝 Maintenance & Updates
When adding new system-level dependencies (e.g., a new PHP extension or a system library like `libpng`):
1.  Add it to the **Stage 2** `RUN install-php-extensions` section in the `Dockerfile`.
2.  If it's needed during the build process (like `npm install`), add it to **Stage 1**.
3.  Rebuild the local test stack to verify: `docker compose -f docker-compose.local-test.yml up -d --build`.
