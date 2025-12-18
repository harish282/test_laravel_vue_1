# Limit-Order Exchange Mini Engine

A full-stack trading application built with Laravel (backend) and Vue.js (frontend) featuring real-time order matching and balance management.

## Features

- User authentication with Laravel Sanctum
- Limit order placement for BTC and ETH
- Real-time order matching with commission handling
- Live balance and asset updates
- Orderbook display
- Atomic transactions for data integrity

## Tech Stack

- **Backend**: Laravel 11, MySQL, Laravel Sanctum, Laravel Broadcasting
- **Frontend**: Vue.js 3, Composition API, Tailwind CSS
- **Real-time**: Laravel Broadcasting (Pusher compatible)
- **Database**: MySQL

## Setup Instructions

### Prerequisites

- Docker and Docker Compose
- Git

### Installation

1. Clone the repository:

   ```bash
   git clone <repository-url>
   cd virgosoft
   ```

2. Start the Docker containers:

   ```bash
   ./docker.sh start
   ```

   This will build and start the backend (Laravel) and frontend (Vue.js) containers.

3. Access the application:
   - Frontend: http://localhost:8080
   - Backend API: http://localhost:8000

### Database Setup

The application uses MySQL in Docker. Migrations will run automatically on container start.

### Environment Configuration

Copy the example environment file and configure as needed:

```bash
cp www/backend/.env.example www/backend/.env
```

Update the following in `www/backend/.env`:

- Database credentials (handled by Docker)
- Broadcasting configuration (Pusher if used)
- Sanctum configuration

### API Endpoints

- `POST /api/login` - User login
- `GET /api/profile` - User balance and assets
- `GET /api/orders?symbol=BTC` - Orderbook
- `POST /api/orders` - Place limit order
- `POST /api/orders/{id}/cancel` - Cancel order
- `GET /api/my-orders` - User's orders

### Frontend Screens

- `/login` - Authentication
- `/orders` - Wallet overview and orderbook
- `/place-order` - Order placement form

### Real-time Updates

The application uses Laravel Broadcasting for real-time updates. When orders are matched, users receive live updates via private channels.

## Development

### Backend

```bash
cd www/backend
composer install
php artisan migrate
php artisan serve
```

### Frontend

```bash
cd www/frontend
npm install
npm run dev
```

## Testing

Run the test suite:

```bash
cd www/backend
php artisan test
```

## Security Features

- Atomic database transactions
- Balance validation before order placement
- Commission deduction
- Sanctum token authentication
- Input validation and sanitization

## License

This project is for assessment purposes.
