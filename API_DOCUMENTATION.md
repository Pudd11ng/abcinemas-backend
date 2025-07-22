# ABC Cinemas API Documentation

## Overview

This document provides comprehensive information about the ABC Cinemas Backend API endpoints, request/response formats, authentication, and usage examples. The API is built with PHP Slim Framework and provides complete cinema management functionality.

## Base URL

```
http://localhost:8088/api
```

## API Status

✅ **Production Ready** - All endpoints tested and fully functional
- 40 movies in catalog
- 22 user accounts  
- 2000+ showtimes across 5 branches
- 20+ customer reviews
- Complete CRUD operations

## Authentication

The API currently uses simple email/password authentication for user login. Future versions will implement JWT token-based authentication.

### Login Endpoint
```bash
POST /api/users/login
Content-Type: application/x-www-form-urlencoded

email=user@example.com&password=userpassword
```

## Response Format

All API responses use consistent JSON format:

**Success Response:**
```json
{
  "success": true,
  "data": {},           // Response data
  "count": 0            // Number of records (for lists)
}
```

**Error Response:**
```json
{
  "success": false,
  "error": "Error message description"
}
```

**Simple Message Response:**
```json
{
  "message": "Operation completed successfully"
}
```

## HTTP Status Codes

- `200` - OK (Success)
- `201` - Created (Resource created successfully)
- `400` - Bad Request (Invalid input)
- `401` - Unauthorized (Authentication required)
- `404` - Not Found (Resource not found)
- `500` - Internal Server Error (Server error)

## Endpoints

### Health Check & Status

#### GET /
Check if the API is running.

**Request:**
```bash
curl http://localhost:8088/
```

**Response:**
```json
{
  "message": "ABC Cinemas Backend API",
  "version": "1.0.0",
  "status": "running",
  "timestamp": "2025-07-23 18:40:11"
}
```

#### GET /api/status
Check API and database connectivity.

**Request:**
```bash
curl http://localhost:8088/api/status
```

**Response:**
```json
{
  "api": "online",
  "database": "connected",
  "timestamp": "2025-07-23 18:40:17"
}
```

---

### Movies API

#### GET /api/movies
Get all movies in the catalog (40 movies available).

**Request:**
```bash
curl http://localhost:8088/api/movies
```

**Response:**
```json
{
  "movies": [
    {
      "id": 1,
      "title": "Avengers: Endgame",
      "description": "The culmination of 22 interconnected films...",
      "genre": "Action, Adventure, Drama",
      "duration": "181 minutes",
      "rating": "PG-13",
      "release_date": "2019-04-26",
      "poster_url": "https://example.com/poster1.jpg",
      "trailer_url": "https://youtube.com/watch?v=trailer1"
    }
    // ... 39 more movies
  ]
}
```

#### GET /api/movies/{id}
Get a specific movie by ID.

**Request:**
```bash
curl http://localhost:8088/api/movies/1
```

**Response (200):**
```json
{
  "id": 1,
  "title": "Avengers: Endgame",
  "description": "The culmination of 22 interconnected films...",
  "genre": "Action, Adventure, Drama",
  "duration": "181 minutes",
  "rating": "PG-13",
  "release_date": "2019-04-26",
  "poster_url": "https://example.com/poster1.jpg",
  "trailer_url": "https://youtube.com/watch?v=trailer1"
}
```

**Error Response (404):**
```json
{
  "error": "Movie not found"
}
```

---

### Users API

#### GET /api/users
Get all users (22 users available).

**Request:**
```bash
curl http://localhost:8088/api/users
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "user_id": 1,
      "full_name": "Sky",
      "email": "skylimsk@hotmail.com",
      "phone_number": "011-5428 5809",
      "role": "user",
      "date_of_birth": "2002-07-29",
      "registration_date": "2024-07-13 15:15:21"
    }
    // ... 21 more users
  ],
  "count": 22
}
```

#### GET /api/users/{id}
Get a specific user by ID.

**Request:**
```bash
curl http://localhost:8088/api/users/1
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "user_id": 1,
    "full_name": "Sky",
    "email": "skylimsk@hotmail.com",
    "phone_number": "011-5428 5809",
    "role": "user",
    "date_of_birth": "2002-07-29",
    "registration_date": "2024-07-13 15:15:21"
  }
}
```

**Error Response (404):**
```json
{
  "success": false,
  "error": "User not found"
}
```

#### POST /api/users
Create a new user account.

**Request:**
```bash
curl -X POST -H "Content-Type: application/x-www-form-urlencoded" \
  -d "full_name=John Doe&email=john@example.com&password=password123&date_of_birth=1990-01-01&phone_number=012-345-6789" \
  http://localhost:8088/api/users
```

**Required Fields:**
- `full_name` (string) - Full name of the user
- `email` (string) - Valid email address
- `password` (string) - User password (will be hashed)
- `date_of_birth` (string) - Date in YYYY-MM-DD format
- `phone_number` (string) - Phone number

**Optional Fields:**
- `role` (string) - User role (default: 'user')

**Response (201):**
```json
{
  "success": true,
  "message": "User created successfully",
  "data": {
    "user_id": "23",
    "email": "john@example.com"
  }
}
```

**Validation Error (400):**
```json
{
  "success": false,
  "error": "Field 'password' is required"
}
```

**Email Exists Error (400):**
```json
{
  "success": false,
  "error": "Email already exists"
}
```

#### PUT /api/users/{id}
Update user information.

**Request:**
```bash
curl -X PUT -H "Content-Type: application/x-www-form-urlencoded" \
  -d "full_name=John Smith&email=john@example.com&date_of_birth=1990-01-01&phone_number=012-345-9999" \
  http://localhost:8088/api/users/23
```

**Required Fields:**
- `full_name` (string)
- `email` (string)
- `date_of_birth` (string)
- `phone_number` (string)

**Response (200):**
```json
{
  "success": true,
  "message": "User updated successfully"
}
```

#### DELETE /api/users/{id}
Delete a user account.

**Request:**
```bash
curl -X DELETE http://localhost:8088/api/users/23
```

**Response (200):**
```json
{
  "success": true,
  "message": "User deleted successfully"
}
```

**Error Response (404):**
```json
{
  "success": false,
  "error": "User not found"
}
```

#### POST /api/users/login
Authenticate a user.

**Request:**
```bash
curl -X POST -H "Content-Type: application/x-www-form-urlencoded" \
  -d "email=admin@gmail.com&password=password123" \
  http://localhost:8088/api/users/login
```

**Required Fields:**
- `email` (string) - User email
- `password` (string) - User password

**Response (200):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user_id": 2,
    "full_name": "Azman Ahmad",
    "email": "admin@gmail.com",
    "role": "admin"
  }
}
```

**Authentication Error (401):**
```json
{
  "success": false,
  "error": "Invalid email or password"
}
```

---

### Showtimes API

#### GET /api/showtimes
Get all showtimes with optional filtering (2000+ showtimes available).

**Request (All showtimes):**
```bash
curl http://localhost:8088/api/showtimes
```

**Request (Filtered by branch and date):**
```bash
curl "http://localhost:8088/api/showtimes?branch=UTM&date=2024-07-14"
```

**Request (Filtered by movie ID):**
```bash
curl "http://localhost:8088/api/showtimes?movie_id=21"
```

**Request (Multiple filters):**
```bash
curl "http://localhost:8088/api/showtimes?branch=UTM&movie_id=21&date=2024-07-14"
```

**Query Parameters:**
- `branch` (string, optional) - Filter by cinema branch
- `movie_id` (integer, optional) - Filter by movie ID
- `date` (string, optional) - Filter by date (YYYY-MM-DD format)

**Response:**
```json
{
  "showtimes": [
    {
      "id": 27497,
      "movie_id": 21,
      "branch": "UTM",
      "hall": 1,
      "show_time": "21:45:00",
      "show_date": "2024-07-13"
    },
    {
      "id": 27498,
      "movie_id": 21,
      "branch": "UTM",
      "hall": 2,
      "show_time": "19:15:00",
      "show_date": "2024-07-13"
    }
    // ... more showtimes
  ]
}
```

#### GET /api/showtimes/{id}
Get a specific showtime by ID.

**Request:**
```bash
curl http://localhost:8088/api/showtimes/27497
```

**Response (200):**
```json
{
  "id": 27497,
  "movie_id": 21,
  "branch": "UTM",
  "hall": 1,
  "show_time": "21:45:00",
  "show_date": "2024-07-13"
}
```

**Error Response (404):**
```json
{
  "error": "Showtime not found"
}
```

#### POST /api/showtimes
Create a new showtime.

**Request:**
```bash
curl -X POST -H "Content-Type: application/x-www-form-urlencoded" \
  -d "movie_id=21&branch=UTM&hall=7&show_time=15:30:00&show_date=2024-07-20" \
  http://localhost:8088/api/showtimes
```

**Required Fields:**
- `movie_id` (integer) - Movie ID
- `branch` (string) - Cinema branch name
- `hall` (integer) - Hall number
- `show_time` (string) - Time in HH:MM:SS format
- `show_date` (string) - Date in YYYY-MM-DD format

**Response (201):**
```json
{
  "message": "Showtime created successfully"
}
```

**Validation Error (400):**
```json
{
  "error": "Missing required fields: movie_id, branch, hall, show_date, show_time"
}
```

#### PUT /api/showtimes/{id}
Update a showtime.

**Request:**
```bash
curl -X PUT -H "Content-Type: application/x-www-form-urlencoded" \
  -d "movie_id=21&branch=UTM&hall=8&show_time=16:00:00&show_date=2024-07-20" \
  http://localhost:8088/api/showtimes/28476
```

**Response (200):**
```json
{
  "message": "Showtime updated successfully"
}
```

#### DELETE /api/showtimes/{id}
Delete a showtime.

**Request:**
```bash
curl -X DELETE http://localhost:8088/api/showtimes/28476
```

**Response (200):**
```json
{
  "message": "Showtime deleted successfully"
}
```

---

### Reviews API

#### GET /api/reviews
Get all customer reviews (20+ available).

**Request:**
```bash
curl http://localhost:8088/api/reviews
```

**Response:**
```json
{
  "reviews": [
    {
      "review_id": 1,
      "movieID": 1,
      "rating": 5,
      "review": "An amazing experience!",
      "created_at": "2024-07-14 18:00:00"
    },
    {
      "review_id": 2,
      "movieID": 2,
      "rating": 4,
      "review": "Very touching and emotional.",
      "created_at": "2024-07-14 19:00:00"
    }
    // ... more reviews
  ]
}
```

#### GET /api/reviews/{id}
Get a specific review by ID.

**Request:**
```bash
curl http://localhost:8088/api/reviews/1
```

**Response (200):**
```json
{
  "review_id": 1,
  "movieID": 1,
  "rating": 5,
  "review": "An amazing experience!",
  "created_at": "2024-07-14 18:00:00"
}
```

**Error Response (404):**
```json
{
  "error": "Review not found"
}
```

#### POST /api/reviews
Create a new review.

**Request:**
```bash
curl -X POST -H "Content-Type: application/x-www-form-urlencoded" \
  -d "movieID=21&rating=5&review=Great movie! Loved every minute of it." \
  http://localhost:8088/api/reviews
```

**Required Fields:**
- `movieID` (integer) - Movie ID to review
- `rating` (integer) - Rating from 1 to 5

**Optional Fields:**
- `review` (string) - Review text content

**Response (201):**
```json
{
  "message": "New review created successfully"
}
```

**Validation Errors (400):**
```json
{
  "error": "movieID and rating are required"
}
```

```json
{
  "error": "Rating must be between 1 and 5"
}
```

#### PUT /api/reviews/{id}
Update a review.

**Request:**
```bash
curl -X PUT -H "Content-Type: application/x-www-form-urlencoded" \
  -d "rating=4&review=Good movie but could be better." \
  http://localhost:8088/api/reviews/21
```

**Fields:**
- `rating` (integer, optional) - Updated rating 1-5
- `review` (string, optional) - Updated review text

**Response (200):**
```json
{
  "message": "Review updated successfully"
}
```

#### DELETE /api/reviews/{id}
Delete a review.

**Request:**
```bash
curl -X DELETE http://localhost:8088/api/reviews/21
```

**Response (200):**
```json
{
  "message": "Review deleted successfully"
}
```

---

### Bookings API

#### GET /api/bookings
Get all bookings (12+ available).

**Request:**
```bash
curl http://localhost:8088/api/bookings
```

**Response:**
```json
{
  "bookings": [
    {
      "booking_id": 2,
      "movie_id": 21,
      "movie_title": "Gold",
      "branch": "UTM",
      "hall": "1",
      "show_time": "11:00:00",
      "show_date": "2024-07-14",
      "seats": "R3S7",
      "customer_name": "Sky"
    },
    {
      "booking_id": 3,
      "movie_id": 21,
      "movie_title": "Gold",
      "branch": "UTM",
      "hall": "1",
      "show_time": "11:00:00",
      "show_date": "2024-07-14",
      "seats": "R3S3, R3S4, R3S5",
      "customer_name": "Sky"
    }
    // ... more bookings
  ]
}
```

#### GET /api/booking-showtimes
Get available showtimes for booking (requires all parameters).

**Request:**
```bash
curl "http://localhost:8088/api/booking-showtimes?movie=Gold&branch=UTM&date=2024-07-14"
```

**Required Query Parameters:**
- `movie` (string) - Movie title (exact match)
- `branch` (string) - Cinema branch name
- `date` (string) - Date in YYYY-MM-DD format

**Response (200):**
```json
{
  "showTimes": [
    {
      "hall": 1,
      "show_time": "11:00:00"
    },
    {
      "hall": 2,
      "show_time": "22:15:00"
    },
    {
      "hall": 3,
      "show_time": "13:00:00"
    }
  ]
}
```

**Missing Parameters Error (400):**
```json
{
  "error": "Missing parameters"
}
```

---

## Cinema Branches

The system supports 5 cinema branches:

- **UTM** - University Teknologi Malaysia campus (6 halls)
- **Muar** - Muar town center location (6 halls)
- **Yong Peng** - Yong Peng district branch (6 halls)
- **Kota Tinggi** - Kota Tinggi regional branch (6 halls)  
- **Cameron Highlands** - Tourist destination branch (6 halls)

Each branch operates multiple halls with varying schedules and capacities.

---

## Error Handling

The API provides consistent error handling with appropriate HTTP status codes:

### Common Error Responses

**400 Bad Request - Missing Required Fields:**
```json
{
  "success": false,
  "error": "Field 'password' is required"
}
```

**400 Bad Request - Invalid Data:**
```json
{
  "error": "Rating must be between 1 and 5"
}
```

**401 Unauthorized - Authentication Failed:**
```json
{
  "success": false,
  "error": "Invalid email or password"
}
```

**404 Not Found - Resource Not Found:**
```json
{
  "error": "Movie not found"
}
```

**500 Internal Server Error - Database/Server Issues:**
```json
{
  "error": "Error fetching showtime: Connection failed"
}
```

---

## Request Formats

The API accepts data in `application/x-www-form-urlencoded` format for most endpoints:

```bash
curl -X POST -H "Content-Type: application/x-www-form-urlencoded" \
  -d "field1=value1&field2=value2" \
  http://localhost:8088/api/endpoint
```

**Note:** JSON format may require additional configuration. Form-encoded data is the recommended format.

---

## Testing Examples

### Complete User Workflow

**1. Create User:**
```bash
curl -X POST -H "Content-Type: application/x-www-form-urlencoded" \
  -d "full_name=Test User&email=test@example.com&password=password123&date_of_birth=1990-01-01&phone_number=012-345-6789" \
  http://localhost:8088/api/users
```

**2. Login:**
```bash
curl -X POST -H "Content-Type: application/x-www-form-urlencoded" \
  -d "email=test@example.com&password=password123" \
  http://localhost:8088/api/users/login
```

**3. Browse Movies:**
```bash
curl http://localhost:8088/api/movies
```

**4. Check Showtimes:**
```bash
curl "http://localhost:8088/api/showtimes?branch=UTM&date=2024-07-14"
```

**5. Leave Review:**
```bash
curl -X POST -H "Content-Type: application/x-www-form-urlencoded" \
  -d "movieID=21&rating=5&review=Excellent movie!" \
  http://localhost:8088/api/reviews
```

### Admin Operations

**Create Showtime:**
```bash
curl -X POST -H "Content-Type: application/x-www-form-urlencoded" \
  -d "movie_id=21&branch=UTM&hall=7&show_time=15:30:00&show_date=2024-07-25" \
  http://localhost:8088/api/showtimes
```

**Update Showtime:**
```bash
curl -X PUT -H "Content-Type: application/x-www-form-urlencoded" \
  -d "movie_id=21&branch=UTM&hall=8&show_time=16:00:00&show_date=2024-07-25" \
  http://localhost:8088/api/showtimes/28476
```

---

## Production Considerations

### Security
- Implement JWT authentication
- Use HTTPS in production
- Add rate limiting
- Validate and sanitize all inputs
- Use environment variables for secrets

### Performance
- Add database indexing
- Implement caching for frequently accessed data
- Consider pagination for large datasets
- Monitor query performance

### Monitoring
- Add request/response logging
- Implement health checks
- Set up error tracking
- Monitor API usage patterns

---

*For setup instructions and development guidelines, see [README.md](README.md)*
*For testing results and validation, see [API_TEST_RESULTS.md](API_TEST_RESULTS.md)*

### Users

#### GET /api/users
Get all users.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "user_id": 1,
      "full_name": "John Doe",
      "email": "john@example.com",
      "phone_number": "+1234567890",
      "role": "user",
      "date_of_birth": "1990-01-01",
      "created_at": "2025-07-22 10:00:00"
    }
  ],
  "count": 1
}
```

#### GET /api/users/{id}
Get user by ID.

**Parameters:**
- `id` (integer) - User ID

#### POST /api/users
Create a new user.

**Request Body:**
```json
{
  "full_name": "John Doe",
  "email": "john@example.com",
  "password": "securepassword",
  "phone_number": "+1234567890",
  "date_of_birth": "1990-01-01",
  "role": "user"
}
```

#### POST /api/users/login
User authentication.

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "securepassword"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user_id": 1,
    "full_name": "John Doe",
    "email": "john@example.com",
    "role": "user"
  }
}
```

---

### Movies

#### GET /api/movies
Get all movies.

**Response:**
```json
{
  "movies": [
    {
      "id": 1,
      "title": "Movie Title",
      "description": "Movie description",
      "genre": "Action",
      "duration": 120
    }
  ]
}
```

#### POST /api/movies
Create a new movie.

**Request Body:**
```json
{
  "id": 1,
  "title": "New Movie",
  "description": "Movie description",
  "genre": "Action",
  "duration": 120
}
```

---

### Bookings

#### GET /api/bookings
Get all bookings with optional filters.

**Query Parameters:**
- `branch` (string) - Filter by branch
- `movie` (string) - Filter by movie title
- `date` (string) - Filter by date (YYYY-MM-DD)

#### POST /api/bookings
Create a new booking.

**Request Body:**
```json
{
  "user_id": 1,
  "movie_id": 21,
  "branch": "UTM",
  "hall": "1",
  "show_time": "19:30:00",
  "show_date": "2025-07-25",
  "total_price": 32.00,
  "payment_method": "card",
  "seats": [
    {
      "row": "5",
      "seat": "A",
      "ticket_type": "Adult",
      "price": 16.00
    },
    {
      "row": "5",
      "seat": "B", 
      "ticket_type": "Adult",
      "price": 16.00
    }
  ]
}
```

#### GET /api/blocked-seats
Get unavailable seats for a specific showtime.

**Query Parameters:**
- `branch` (string, required) - Cinema branch
- `hall` (string, required) - Hall number
- `show_time` (string, required) - Show time (HH:MM:SS)
- `show_date` (string, required) - Show date (YYYY-MM-DD)

---

### Showtimes & Branches

#### GET /api/branches
Get all cinema branches.

#### GET /api/showtimes
Get showtimes with filters.

**Query Parameters:**
- `movie` (string, required) - Movie title
- `branch` (string, required) - Branch name
- `date` (string, required) - Date (YYYY-MM-DD)

---

### Reviews

#### POST /api/reviews
Create a movie review.

**Request Body:**
```json
{
  "movieID": 1,
  "rating": 5,
  "review": "Great movie!"
}
```

## Error Handling

When an error occurs, the API returns:

```json
{
  "success": false,
  "error": "Error description"
}
```

Common error scenarios:
- Missing required fields
- Invalid data format
- Database connection issues
- Resource not found
- Authentication failures

## Rate Limiting

Currently, no rate limiting is implemented. Consider implementing rate limiting for production use.

## Pagination

For endpoints returning large datasets, consider implementing pagination:

```
GET /api/users?page=1&limit=10
```

## CORS

The API supports Cross-Origin Resource Sharing (CORS) with the following configuration:
- Origins: `*` (all origins allowed)
- Methods: `GET, POST, PUT, DELETE, OPTIONS`
- Headers: `Content-Type, Authorization, Accept, Origin`

## Security Notes

1. Always use HTTPS in production
2. Implement proper authentication tokens (JWT)
3. Validate and sanitize all inputs
4. Use environment variables for sensitive configuration
5. Implement rate limiting
6. Add request logging for monitoring

## Testing

Use tools like Postman, curl, or any HTTP client to test the API:

```bash
# Test health check
curl http://localhost:8088/

# Test user creation
curl -X POST http://localhost:8088/api/users \
  -H "Content-Type: application/json" \
  -d '{"full_name":"Test User","email":"test@example.com","password":"password123","phone_number":"1234567890","date_of_birth":"1990-01-01"}'

# Test login
curl -X POST http://localhost:8088/api/users/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}'
```
