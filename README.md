# Poll App

## Backend setup
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
# Configure DB in .env (DB_DATABASE, DB_USERNAME, DB_PASSWORD)
php artisan migrate
php artisan serve
```

## Frontend setup
```bash
cd frontend
npm install
cp .env.example .env
# Edit VITE_API_URL=http://localhost:8000
npm run dev
```

Open http://localhost:5173/create to create a poll.