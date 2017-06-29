# laravel-resource
RESTful api layer over Laravel 5

```bash
$ php artisan vendor:publish --provider="Karellens\Resource\ResourceServiceProvider"
```

Then add the following line into the `providers` array:
```php
    Karellens\Resource\ResourceServiceProvider::class,
```

And middleware
```php
    'resource' => \Karellens\Resource\Http\Middleware\CheckRequest::class,
```
to `routeMiddleware` array in `app/Http/Kernel.php`.