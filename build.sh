#!/bin/bash
set -e

echo "🚀 Starting build process..."

# Install Node.js dependencies
echo "📦 Installing Node.js dependencies..."
npm ci

# Build Vite assets (this compiles Tailwind CSS)
echo "🏗️ Building Vite assets..."
npm run build

# Verify build directory exists
if [ ! -d "public/build" ]; then
    echo "❌ Error: Build directory not found after npm run build"
    exit 1
fi

echo "✅ Build directory created successfully"

# Install PHP dependencies
echo "📦 Installing PHP dependencies..."
composer install --optimize-autoloader --no-scripts

# Laravel optimization
echo "⚡ Optimizing Laravel..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Build completed successfully!"
