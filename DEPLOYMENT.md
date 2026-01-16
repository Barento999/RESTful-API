# Deployment Guide

## Files to Deploy

### Required Files

```
├── models/              # All model files
├── controllers/         # All controller files
├── routes/             # Routing files
├── api/                # API endpoint files
├── .htaccess           # Apache rewrite rules
├── config.example.php  # Configuration template
├── db.php              # Database connection
├── auth.php            # Authentication middleware
├── jwt.php             # JWT utilities
├── index.php           # Entry point
└── README.md           # Documentation
```

### Files NOT to Deploy (Excluded by .gitignore)

- `config.php` - Contains sensitive credentials
- `test-api.html` - Testing files
- `TEST-ALL-ENDPOINTS.html` - Testing files
- `setup.sql` - Database setup (deploy separately)
- `import-database.bat` - Local setup script
- Documentation files (optional)
- `.vscode/` - IDE settings

## Deployment Steps

### 1. Prepare Repository

```bash
git init
git add .
git commit -m "Initial commit - MVC REST API"
```

### 2. Push to GitHub

```bash
git remote add origin https://github.com/yourusername/student-api.git
git branch -M main
git push -u origin main
```

### 3. Server Setup

#### On Your Server:

1. Clone the repository

```bash
git clone https://github.com/yourusername/student-api.git
cd student-api
```

2. Create configuration file

```bash
cp config.example.php config.php
```

3. Edit `config.php` with your server credentials

```php
define('JWT_SECRET', 'your-secure-random-secret-key');
define('DB_HOST', 'your-db-host');
define('DB_NAME', 'your-db-name');
define('DB_USER', 'your-db-user');
define('DB_PASS', 'your-db-password');
```

4. Import database (upload `setup.sql` separately)

```bash
mysql -u username -p database_name < setup.sql
```

5. Set proper permissions

```bash
chmod 755 api/
chmod 755 controllers/
chmod 755 models/
chmod 755 routes/
chmod 644 config.php
```

### 4. Apache Configuration

Ensure `.htaccess` is enabled and mod_rewrite is active:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### 5. Test Deployment

Test the API endpoints:

```bash
curl -X POST http://yourserver.com/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"admin123"}'
```

## Security Checklist

- [ ] Change `JWT_SECRET` to a strong random value
- [ ] Update database credentials
- [ ] Ensure `config.php` is not publicly accessible
- [ ] Enable HTTPS/SSL
- [ ] Set proper file permissions
- [ ] Disable error display in production
- [ ] Enable error logging
- [ ] Implement rate limiting (optional)
- [ ] Set up database backups

## Environment Variables (Alternative)

Instead of `config.php`, you can use environment variables:

1. Create `.env` file (add to .gitignore)
2. Use `getenv()` in your code
3. Configure via server environment or `.htaccess`

## Continuous Deployment

For automatic deployment, consider:

- GitHub Actions
- GitLab CI/CD
- Deployer
- Custom webhook scripts

## Troubleshooting

**500 Internal Server Error**

- Check Apache error logs
- Verify file permissions
- Ensure all required PHP extensions are installed

**Database Connection Failed**

- Verify credentials in `config.php`
- Check database server is running
- Ensure database exists

**404 Not Found**

- Verify `.htaccess` is working
- Check mod_rewrite is enabled
- Confirm file paths are correct
