<p align="center">
  <img src="./public/favicon.svg" alt="CivicGuard Logo" width="100"/>
</p>

<h1 align="center">CivicGuard</h1>

<p align="center">
  Community Monitoring, Incident Reporting, and Curfew Tracking System
</p>

---

## Requirements

Before installing CivicGuard, make sure you have:

* **Docker Desktop** installed and running
* **Git** installed
* **Terminal** access

  * Windows: WSL/Ubuntu recommended
  * macOS/Linux: Native terminal

---

## Installation & Setup

### 1. Clone the Repository

```bash
git clone https://github.com/miconvr/civicguard.git
cd civicguard
```

### 2. Create the Environment File

```bash
cp .env.example .env
```

### 3. Start Docker

```bash
./vendor/bin/sail up -d
```

### 4. Generate the Application Key

```bash
./vendor/bin/sail artisan key:generate
```

### 5. Install Dependencies

```bash
./vendor/bin/sail npm install
```

### 6. Start the Frontend

Keep this command running while developing:

```bash
./vendor/bin/sail npm run dev
```

### 7. Setup the Database

Run the migrations and seed the default data:

```bash
./vendor/bin/sail artisan migrate --seed
```

> **Using an existing database:**
> If you already have a CivicGuard `.sql` database backup, import it through phpMyAdmin instead of running `migrate --seed` on an existing database.

---

## Daily Development

After the initial setup, you only need:

### Start CivicGuard

```bash
cd ~/civicguard
./vendor/bin/sail up -d
```

### Start Vite

```bash
./vendor/bin/sail npm run dev
```

### Stop CivicGuard

```bash
./vendor/bin/sail stop
```

---

## Local Services

Once Docker is running, you can access:

| Service        | URL                   |
| -------------- | --------------------- |
| **CivicGuard** | http://localhost      |
| **phpMyAdmin** | http://localhost:8080 |

Use the database credentials configured in your `.env` file to access phpMyAdmin.

---

## Project Information

**CivicGuard** is a Laravel-based community monitoring system designed to support:

* Incident reporting
* Community monitoring
* Curfew tracking
* Report management
* AI-assisted report classification
* Administrative monitoring

---

## Repository

**GitHub:**
https://github.com/miconvr/civicguard.git
