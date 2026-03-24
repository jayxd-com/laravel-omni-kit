# Laravel Omni Kit

Welcome to **Laravel Omni Kit**, a comprehensive full-stack starter kit designed for modern application development. This repository is structured as a monorepo to house all components of your ecosystem, from the backend API to multi-platform applications and marketing sites.

## 🚀 Project Overview

Laravel Omni Kit is built to be a robust starting point for developers who want to scale quickly across different platforms while maintaining a unified codebase structure.

### 🏗 Monorepo Structure

- **`backend/`**: Laravel 13 API powerhouse.
- **`apps/next-app`**: High-performance web application (Next.js + Shadcn).
- **`marketing/`**: (Coming Soon) **Astro** powered marketing website for SEO and performance.

## 🛠 Tech Stack (Backend)

- **Framework**: Laravel 13 (Octane + FrankenPHP)
- **Database**: PostgreSQL
- **Cache/Queue**: Redis
- **Real-time**: Laravel Reverb
- **Admin Panels**: Filament v5 (Hybrid Strategy)
- **Roles/Teams**: Spatie Permissions (Team-based Multi-tenancy)

---

## 🔐 Default Credentials

The following users are created by the `php artisan db:seed` command. All passwords are set to `password`.

### 🛡 Platform Admin (Filament `/admin`)
Used for managing the entire platform.
- **Super Admin**: `superadmin@example.com`
- **Manager**: `admin@example.com`

### 🏢 Team/Brand Accounts (Filament `/app` & Next.js)
Used for business/brand owners and their members.
- **Team Owner**: `owner@example.com` (Owns "Default Team")
- **Team Member**: `member@example.com` (Member of "Default Team")

---

## 🚦 Getting Started

1.  Clone the repository.
2.  Follow the [Backend Setup Guide](./backend/README.md).
3.  Follow the [Docker & Deployment Guide](./backend/DOCKER_GUIDE.md).

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
