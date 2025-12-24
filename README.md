# Google Review Reply Simulator

A Laravel 10 + Vue 3 application for generating AI-powered reply suggestions for Google reviews.

## Features

- ✅ Review submission with sentiment analysis
- ✅ Automatic reply generation (friendly, professional, witty tones)
- ✅ Modern Vue 3 + TailwindCSS frontend
- ✅ RESTful API architecture
- ✅ Ready for Google API integration

## Tech Stack

- **Backend**: Laravel 10
- **Frontend**: Vue 3 (Composition API) + Vite
- **Styling**: TailwindCSS
- **Database**: MySQL/PostgreSQL
- **Architecture**: API-first, SOLID principles

## Project Structure

```
app/
├── Contracts/              # Service interfaces
│   └── ReviewReplyServiceInterface.php
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── ReviewController.php
│   ├── Requests/
│   │   └── StoreReviewRequest.php
│   └── Resources/
│       ├── ReviewResource.php
│       └── ReplySuggestionResource.php
├── Models/
│   ├── Review.php
│   └── ReplySuggestion.php
└── Services/
    ├── ReviewService.php
    ├── ReviewSentimentService.php
    ├── ReplyGeneratorService.php
    └── GoogleReviewApiService.php (future integration)

resources/
├── js/
│   ├── components/
│   │   ├── App.vue
│   │   ├── ReviewForm.vue
│   │   ├── ReviewCard.vue
│   │   ├── ReplySuggestions.vue
│   │   └── Toast.vue
│   └── app.js
└── css/
    └── app.css

database/
└── migrations/
    ├── create_reviews_table.php
    └── create_reply_suggestions_table.php
```

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd google-review-reply-simulator
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Set up database**
   - Update `.env` with your database credentials
   ```bash
   php artisan migrate
   ```

6. **Start development servers**
   ```bash
   # Terminal 1: Laravel
   php artisan serve
   
   # Terminal 2: Vite
   npm run dev
   ```

## Environment Variables

Add to `.env`:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Google API (for future integration)
GOOGLE_API_KEY=
GOOGLE_API_SECRET=
GOOGLE_PROJECT_ID=
```

## API Endpoints

### GET /api/reviews
List all reviews with reply suggestions.

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "reviewer_name": "John Doe",
      "rating": 5,
      "review_text": "Great service!",
      "sentiment": "positive",
      "reply_suggestions": [...]
    }
  ]
}
```

### POST /api/reviews
Create a new review with automatic reply generation.

**Request:**
```json
{
  "reviewer_name": "John Doe",
  "rating": 5,
  "review_text": "Amazing experience!"
}
```

**Response:** 201 Created with review and reply suggestions

## Google API Integration (Future)

The application is prepared for Google API integration:

1. **Interface**: `App\Contracts\ReviewReplyServiceInterface`
2. **Service**: `App\Services\GoogleReviewApiService` (placeholder)
3. **Configuration**: `config/services.php`

To integrate:
1. Add Google API credentials to `.env`
2. Install Google API client: `composer require google/apiclient`
3. Implement `GoogleReviewApiService::generateReplies()`
4. Update service binding in `AppServiceProvider`

## Code Quality

- ✅ SOLID principles
- ✅ Service layer architecture
- ✅ Request validation
- ✅ API resources
- ✅ Error handling & logging
- ✅ Type hints & PHPDoc
- ✅ Database transactions

## Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --filter ReviewSentimentServiceTest
php artisan test --filter ReplyGeneratorServiceTest
php artisan test --filter ReviewApiTest
```

## Production Deployment

1. **Optimize for production**
   ```bash
   composer install --optimize-autoloader --no-dev
   npm run build
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

2. **Set environment**
   ```bash
   APP_ENV=production
   APP_DEBUG=false
   ```

3. **Run migrations**
   ```bash
   php artisan migrate --force
   ```

## License

MIT
