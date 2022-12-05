# testimonials

Testimonial and portfolio post type for WordPress.

## Description

This library provides two custom post types.

- Portfolio
- Testimonial

These are very similar to Jetpack's CPT but simpler than that.

## Installation

Install via composer.

```
composer require kunoichi/testimonials
```

And initialize the class from your entry point: `functions.php` for themes, anytime before `init` hook in pllugin.

```php
// In theme's functions.php
PortfolioPostType::get_instance();

// In plugin's mail file.
TestimonialPostType::get_instance();
```