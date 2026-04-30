# Polling App

## Project description
Simple polling app. Users create polls, share a short link, others vote once (by IP).

**Stack**: Laravel 13, Vue 3, Vuex, Vue Router, Axios, MySQL.

**Project structure**:

```text
polls/
  backend/   ← Laravel 13
  frontend/  ← Vue 3 + Vite
```

## Requirements
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL

## Backend setup

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
# Set DB_DATABASE, DB_USERNAME, DB_PASSWORD in .env
php artisan migrate
php artisan serve
```

Runs at `http://localhost:8000`.

## Frontend setup

```bash
cd frontend
npm install
cp .env.example .env
# Set VITE_API_URL=http://localhost:8000 in .env
npm run dev
```

Runs at `http://localhost:5173`.

## Usage
- Open `http://localhost:5173/create` to create a poll.
- Share the link `/poll/{code}` with others to vote.

## Running tests

```bash
cd backend
php artisan test
```

## Architecture notes (brief)
- DDD structure: `Domain/Poll` contains Models, Factories, Repositories, Contracts, Services
- `PollFactory` handles poll creation and unique code generation
- `CodeGeneratorInterface` allows swapping code generation strategy
- Votes are protected by IP — one vote per IP per poll (unique index on `poll_id` + `ip_address`)
- CORS configured for `http://localhost:5173`

## API endpoints
- `POST /api/polls` — create poll
- `GET /api/polls/{short_code}` — get poll (includes results if already voted)
- `POST /api/polls/{short_code}/vote` — vote (returns 409 if already voted)