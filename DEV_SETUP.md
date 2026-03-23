# 🛠 Local Development Setup

Follow these instructions to get your development environment up and running for the **Laravel Omni Kit**.

## 📋 Prerequisites

- **Docker** and **Docker Compose** installed on your machine.
- **PHP 8.3** or higher installed locally (to run composer if you don't use sail initially).
- **Composer** (optional, you can also use sail's composer).
- **Node.js & NPM/Yarn**.

---

## 🐘 Backend Setup (Laravel 13)

The backend is powered by Laravel 13 and uses **Laravel Sail** for a seamless Docker-based experience.

### 1. Initial Installation

Navigate to the `backend/` directory:

```bash
cd backend
```

Copy the `.env.example` file:

```bash
cp .env.example .env
```

Install the dependencies:

```bash
composer install
# or if you don't have composer locally:
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

### 2. Start the Environment

Run Laravel Sail in the background:

```bash
./vendor/bin/sail up -d
```

### 3. Generate Key & Run Migrations

```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
```

### ⛵ Services Included in Sail

Our Docker setup (via `docker-compose.yml`) includes:

- **🐘 PHP 8.3**: Application environment.
- **🐘 PostgreSQL**: Primary database.
- **🔴 Redis**: Caching and queuing.
- **✉️ Mailpit**: Local email testing (Access at [localhost:8025](http://localhost:8025)).
- **🔍 Meilisearch**: Full-text search engine.

---

## 📦 Planned Components

Instructions for the following will be added as they are implemented:

- [ ] **Next.js Web App** (`app/web`)
- [ ] **React Native Mobile App** (`app/mobile`)
- [ ] **Astro Marketing Site** (`marketing`)

---

## 💡 Quick Tips

- **Sail Alias**: It's highly recommended to alias `sail`:
  ```bash
  alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
  ```
  Then you can just run `sail up -d`, `sail artisan ...`, etc.
- **Logs**: View application logs using `./vendor/bin/sail logs -f`.
