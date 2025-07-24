# Tailwind CSS Setup in Laravel

This document explains how Tailwind CSS has been set up in this Laravel project.

## Installation

Tailwind CSS v3.4.0 has been installed with the following packages:
- `tailwindcss` - The main Tailwind CSS framework
- `postcss` - PostCSS for processing
- `autoprefixer` - Autoprefixer for vendor prefixes

## Configuration Files

### 1. `tailwind.config.js`
```javascript
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
```

### 2. `postcss.config.js`
```javascript
export default {
  plugins: {
    tailwindcss: {},
    autoprefixer: {},
  },
}
```

### 3. `resources/css/app.css`
```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

## Usage

### In Blade Templates
Include the compiled CSS in your Blade templates using Vite:
```html
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

### Development
Run the development server with hot reloading:
```bash
npm run dev
```

### Production Build
Build for production:
```bash
npm run build
```

## Test Pages

1. **Homepage** (`/`) - Updated welcome page with Tailwind CSS
2. **Tailwind Test Page** (`/tailwind-test`) - Comprehensive demonstration of Tailwind utilities

## Features Demonstrated

- Responsive design with grid layouts
- Color system and utilities
- Spacing and typography
- Hover effects and transitions
- Form styling
- Alert components
- Button variations
- Shadow and border utilities

## Available Commands

- `npm run dev` - Start development server with hot reloading
- `npm run build` - Build assets for production
- `php artisan serve` - Start Laravel development server

## File Structure

```
resources/
├── css/
│   └── app.css          # Main CSS file with Tailwind directives
├── views/
│   ├── welcome.blade.php    # Updated homepage
│   └── tailwind-test.blade.php  # Test page
├── js/
│   └── app.js           # JavaScript file
tailwind.config.js       # Tailwind configuration
postcss.config.js        # PostCSS configuration
vite.config.js          # Vite configuration
```

## Next Steps

1. Start using Tailwind classes in your Blade templates
2. Customize the theme in `tailwind.config.js` if needed
3. Add custom components using `@apply` directive
4. Consider adding Tailwind plugins for additional functionality

## Resources

- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Laravel Vite Integration](https://laravel.com/docs/vite)
- [Tailwind CSS with Laravel](https://tailwindcss.com/docs/guides/laravel) 