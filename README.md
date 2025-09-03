# 🎨 FAL AI - Laravel Design Tool

A modern Laravel application that integrates with FAL AI for AI-powered image generation and model training. Built with a clean, professional UI using Bootstrap 5 and modern Laravel practices.

## ✨ Features

### 🖼️ **Album Management**
- Create and manage photo albums
- Upload multiple photos per album
- Organize photos with descriptions and trigger words
- Professional album interface with status tracking

### 🤖 **AI Model Training**
- Train custom AI models using FAL AI's Flux LoRA Fast Training
- Automatic photo preprocessing and zip archive creation
- Cloud storage integration for training data
- Real-time training progress monitoring
- Support for custom trigger words

### 🎭 **Image Generation**
- Generate images using trained models
- Multiple theme support
- Professional generation interface
- Gallery view of generated images

### 🔐 **User Management**
- Secure authentication system
- User-specific albums and models
- Permission-based access control
- Clean dashboard with statistics

## 🚀 **Technology Stack**

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Bootstrap 5, Blade Templates
- **Database**: SQLite (development), MySQL/PostgreSQL (production)
- **AI Integration**: FAL AI API
- **File Storage**: Laravel Storage with cloud support
- **Build Tool**: Vite
- **Testing**: PHPUnit

## 📋 **Requirements**

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- SQLite (or MySQL/PostgreSQL)
- FAL AI API Key

## 🛠️ **Installation**

### 1. Clone the Repository
```bash
git clone <your-repo-url>
cd fal
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure FAL AI
Add your FAL AI API key to `.env`:
```env
FAL_KEY=your_fal_ai_api_key_here
```

### 5. Database Setup
```bash
php artisan migrate
php artisan db:seed
```

### 6. Storage Setup
```bash
php artisan storage:link
```

### 7. Build Assets
```bash
npm run build
```

### 8. Start Development Server
```bash
php artisan serve
```

## 🔑 **Default Users**

After seeding, you can login with:
- **Admin**: `admin@admin.com` / `password`
- **Test User**: `test@example.com` / `password`

## 📁 **Project Structure**

```
fal/
├── app/
│   ├── Http/Controllers/     # Application controllers
│   ├── Models/               # Eloquent models
│   ├── Policies/             # Authorization policies
│   └── Services/             # Business logic services
├── database/
│   ├── migrations/           # Database migrations
│   ├── seeders/             # Database seeders
│   └── factories/           # Model factories
├── resources/
│   ├── views/               # Blade templates
│   ├── css/                 # Stylesheets
│   └── js/                  # JavaScript
├── routes/                   # Application routes
├── tests/                    # Test files
└── storage/                  # File storage
```

## 🎯 **Key Workflows**

### **Album Creation & Training**
1. Create album with name, description, and optional trigger word
2. Upload 4+ photos to the album
3. Start AI model training
4. Monitor training progress
5. Use trained model for image generation

### **Image Generation**
1. Select a trained photo model
2. Choose a theme
3. Generate custom images
4. View and manage generated images

## 🔧 **Configuration**

### **FAL AI Service**
The application integrates with FAL AI's Flux LoRA Fast Training API:
- Automatic zip archive creation from photos
- Cloud storage integration
- Proper API endpoint usage
- Training progress tracking

### **File Storage**
- Photos stored in `storage/app/public/photos/`
- Training archives in `storage/app/public/training-archives/`
- Generated images in `storage/app/public/generated/`

## 🧪 **Testing**

Run the test suite:
```bash
php artisan test
```

Run specific test files:
```bash
php artisan test tests/Feature/PhotoEditDeleteTest.php
php artisan test tests/Feature/UIImprovementsTest.php
```

## 📊 **Database Schema**

### **Core Tables**
- `users` - User accounts and authentication
- `albums` - Photo albums with training metadata
- `photo_models` - Individual photos within albums
- `training_sessions` - AI training session tracking
- `generated_images` - AI-generated images
- `themes` - Image generation themes

### **Key Relationships**
- User → Albums (1:many)
- Album → Photos (1:many)
- Album → Training Sessions (1:many)
- Photo → Generated Images (1:many)

## 🚀 **Deployment**

### **Production Considerations**
1. Set `APP_ENV=production` in `.env`
2. Configure production database
3. Set up cloud storage (AWS S3, Google Cloud, etc.)
4. Configure web server (Nginx/Apache)
5. Set up SSL certificates
6. Configure queue workers for background jobs

### **Environment Variables**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password
FAL_KEY=your-fal-ai-key
```

## 🤝 **Contributing**

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass
6. Submit a pull request

## 📝 **License**

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🆘 **Support**

For support and questions:
- Check the [Laravel documentation](https://laravel.com/docs)
- Review [FAL AI documentation](https://fal.ai/docs)
- Open an issue in this repository

## 🔄 **Changelog**

### **v1.0.0** - Initial Release
- Complete UI redesign with Bootstrap 5
- Album-based photo management system
- FAL AI integration for model training
- Image generation with themes
- Comprehensive testing suite
- Professional-grade user experience

---

**Built with ❤️ using Laravel and FAL AI**
