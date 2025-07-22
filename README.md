# ABC Cinemas Backend API

A comprehensive RESTful API for a cinema booking system built with PHP and the Slim Framework. This backend powers a multi-branch cinema chain management system with support for movie management, user accounts, ticket booking, and reviews.

## 🎬 Features

- **Movie Management**: Complete CRUD operations for 40+ movies with details like title, description, genre, and duration
- **User Management**: User registration, authentication, and profile management with secure password hashing
- **Booking System**: Complete ticket booking workflow with seat selection and booking history
- **Multi-Branch Support**: Manage multiple cinema locations (UTM, Muar, Yong Peng, Kota Tinggi, Cameron Highlands)
- **Showtime Management**: Comprehensive showtime system with 2000+ scheduled showtimes across all branches
- **Reviews & Ratings**: Customer feedback system with 1-5 star ratings and text reviews
- **Hall Management**: Support for multiple halls per branch (up to 6 halls per location)
- **Advanced Filtering**: Filter showtimes by branch, movie, date, and hall
- **Error Handling**: Robust error handling with proper HTTP status codes and JSON responses

## 🏗️ Architecture

The system follows a clean MVC-inspired architecture:

```
abcinemas-backend/
├── api/
│   ├── config/           # Database configuration
│   │   └── config.php   # Database connection settings
│   ├── controllers/     # API endpoint controllers
│   │   ├── UserController.php      # User management (22 users)
│   │   ├── MovieController.php     # Movie management (40 movies)
│   │   ├── BookingController.php   # Booking system (12+ bookings)
│   │   ├── ReviewController.php    # Reviews system (20+ reviews)
│   │   └── ShowtimeController.php  # Showtime management (2000+ showtimes)
│   └── index.php        # Main application entry point
├── database/            # Database schema and data
│   └── abcinemas.sql   # Complete database dump with sample data
├── utilities/           # Helper scripts
│   └── generate_showtimes.php  # Automated showtime generation
├── vendor/             # Composer dependencies
├── API_DOCUMENTATION.md # Comprehensive API documentation
└── API_TEST_RESULTS.md # Testing results and validation
```

### Key Components

- **Slim Framework**: Lightweight PHP micro-framework for REST API
- **PDO**: Secure database abstraction layer with prepared statements
- **CORS Middleware**: Cross-origin resource sharing support
- **Error Handling**: Comprehensive error handling with proper HTTP status codes
- **Input Validation**: Server-side validation for all user inputs

## 🚀 Getting Started

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Composer
- XAMPP (for local development)

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd abcinemas-backend
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Set up the database**
   - Start XAMPP and enable Apache and MySQL
   - Create a database named `abcinemas`
   - Import the database schema:
     ```bash
     mysql -u root -p abcinemas < database/abcinemas.sql
     ```

4. **Configure database connection**
   - Update database credentials in `api/config/config.php` if needed
   - Default configuration uses:
     - Host: `localhost`
     - Database: `abcinemas`
     - Username: `root`
     - Password: (empty)

5. **Start the development server**
   ```bash
   php -S localhost:8088 -t .
   ```

6. **Test the API**
   - Open your browser and visit: `http://localhost:8088`
   - API endpoints available at: `http://localhost:8088/api`
   - Check API status: `http://localhost:8088/api/status`

## 📚 API Documentation

For comprehensive API documentation, see [API_DOCUMENTATION.md](API_DOCUMENTATION.md)

### Base URL
```
http://localhost:8088/api
```

### Quick Start Endpoints

#### Health Check
```bash
curl http://localhost:8088/
curl http://localhost:8088/api/status
```

#### Get All Movies
```bash
curl http://localhost:8088/api/movies
```

#### Get Showtimes for a Specific Branch and Date
```bash
curl "http://localhost:8088/api/showtimes?branch=UTM&date=2024-07-14"
```

#### User Authentication
```bash
curl -X POST -H "Content-Type: application/x-www-form-urlencoded" \
  -d "email=admin@gmail.com&password=password123" \
  http://localhost:8088/api/users/login
```

### Main Endpoint Categories

- **Users** (`/api/users`) - User management and authentication
- **Movies** (`/api/movies`) - Movie catalog and information  
- **Bookings** (`/api/bookings`) - Ticket booking and management
- **Showtimes** (`/api/showtimes`) - Movie scheduling and availability
- **Reviews** (`/api/reviews`) - Customer reviews and ratings

For detailed endpoint documentation, request/response examples, and error codes, see the complete [API Documentation](API_DOCUMENTATION.md).

## 🗄️ Database Schema

The system uses MySQL with the following main tables:

- **`user`** (22 records) - User accounts with secure password hashing
- **`movies`** (40 records) - Movie catalog with complete metadata
- **`bookings`** (12+ records) - Ticket booking records with payment info
- **`showtimes`** (2000+ records) - Comprehensive movie schedules across all branches
- **`rating_reviews`** (20+ records) - Customer reviews with 1-5 star ratings

### Database Features

- **Referential Integrity**: Proper foreign key relationships
- **Data Validation**: Check constraints for ratings, dates, and enums
- **Indexing**: Optimized for common query patterns
- **Sample Data**: Pre-populated with realistic cinema data

### Branches Supported

- **UTM** - University Teknologi Malaysia campus
- **Muar** - Muar town center location  
- **Yong Peng** - Yong Peng district branch
- **Kota Tinggi** - Kota Tinggi regional branch
- **Cameron Highlands** - Tourist destination branch

Each branch supports multiple halls (1-6 halls) with varying capacities and showtimes.

## 🔧 Configuration

### Database Configuration

Update the database settings in `api/config/config.php`:

```php
class Database {
    private $host = 'localhost';        // Database host
    private $user = 'root';             // Database username
    private $password = '';             // Database password (empty for XAMPP)
    private $dbname = 'abcinemas';     // Database name
}
```

### Current Configuration

The system is currently configured for:
- **Host**: `localhost` (XAMPP default)
- **Database**: `abcinemas` 
- **User**: `root`
- **Password**: Empty (XAMPP default)
- **Port**: 3306 (MySQL default)

## 🧪 Testing

The API has been comprehensively tested with all endpoints verified. See [API_TEST_RESULTS.md](API_TEST_RESULTS.md) for detailed test results.

### Test Tools

- **curl** - Command line testing (recommended for quick tests)
- **Postman** - GUI-based API testing
- **Browser** - For GET endpoints

### Quick Tests

**Health Check:**
```bash
curl http://localhost:8088/
```

**Database Connection:**
```bash
curl http://localhost:8088/api/status
```

**Get Sample Data:**
```bash
# Get all movies
curl http://localhost:8088/api/movies

# Get all users  
curl http://localhost:8088/api/users

# Get showtimes for UTM branch
curl "http://localhost:8088/api/showtimes?branch=UTM&date=2024-07-14"
```

### Test Results Summary

✅ **All endpoints fully functional**
- 40 movies in catalog
- 22 user accounts
- 2000+ showtimes across 5 branches
- 20+ customer reviews
- 12+ booking records
- Complete CRUD operations verified
- Error handling tested and working

## 🛠️ Development

### Code Structure & Standards

- **Controllers**: Handle HTTP requests with proper error handling and validation
- **Database Class**: Centralized PDO connection management with prepared statements
- **CORS Middleware**: Configured for cross-origin requests
- **Error Handling**: Consistent JSON error responses with proper HTTP status codes
- **Input Validation**: Server-side validation for all user inputs
- **Security**: Password hashing, SQL injection prevention

### Current Status

✅ **Production Ready**
- All CRUD operations implemented and tested
- Comprehensive error handling
- Database relationships properly configured
- Input validation in place
- Security measures implemented

### Development Workflow

1. Controllers located in `api/controllers/`
2. Database configuration in `api/config/config.php`
3. Main application entry point: `api/index.php`
4. Test changes using `php -S localhost:8088`
5. Verify with curl or browser testing

### Adding New Features

1. Create new controller in `api/controllers/`
2. Implement proper error handling and validation
3. Register routes in the controller function
4. Import controller in `api/index.php`
5. Test all CRUD operations
6. Update API documentation

## 🚀 Production Deployment

### Deployment Checklist

✅ **Code Ready**
- All endpoints tested and working
- Error handling implemented
- Input validation in place
- Security measures active

🔧 **Production Requirements**
1. **Web Server**: Apache/Nginx with PHP 7.4+
2. **Database**: MySQL 5.7+ with proper user privileges
3. **HTTPS**: SSL certificate for secure communication
4. **Environment**: Update database credentials for production
5. **Logging**: Configure error and access logs
6. **Backups**: Set up automated database backups
7. **Monitoring**: Implement uptime and performance monitoring

### Recommended Production Settings

```php
// Production database configuration
class Database {
    private $host = 'your-production-host';
    private $user = 'your-db-user';           
    private $password = 'secure-password';     
    private $dbname = 'abcinemas_prod';       
}
```

### Security Considerations

- Change default database credentials
- Implement proper authentication/authorization
- Enable HTTPS only
- Configure firewall rules
- Regular security updates
- Monitor for suspicious activity

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🆘 Support & Documentation

### Documentation Files

- **[README.md](README.md)** - This overview and setup guide
- **[API_DOCUMENTATION.md](API_DOCUMENTATION.md)** - Comprehensive API endpoint documentation
- **[API_TEST_RESULTS.md](API_TEST_RESULTS.md)** - Testing results and validation report

### Troubleshooting

**Database Connection Issues:**
- Verify XAMPP MySQL is running
- Check database name is `abcinemas`
- Confirm credentials in `api/config/config.php`

**API Not Responding:**
- Ensure PHP development server is running: `php -S localhost:8088`
- Check for PHP syntax errors in console
- Verify file permissions

**CORS Issues:**
- CORS middleware is configured for cross-origin requests
- Check browser developer console for CORS errors

### Sample Data

The system comes with pre-populated sample data:
- 40 movies across various genres
- 22 user accounts (including admin)
- 2000+ showtimes for the next week
- 20+ customer reviews
- Multiple cinema branches and halls

### Getting Help

1. Check the comprehensive API documentation
2. Review test results for working examples
3. Verify database schema in `database/abcinemas.sql`
4. Test endpoints using provided curl examples

---

**Built with ❤️ for ABC Cinemas**