# Guzzle Jwt middleware

[![Build Status](https://img.shields.io/travis/eljam/guzzle-jwt-middleware.svg?branch=master&style=flat-square)](https://travis-ci.org/eljam/guzzle-jwt-middleware)
[![Code Quality](https://img.shields.io/scrutinizer/g/eljam/guzzle-jwt-middleware.svg?b=master&style=flat-square)](https://scrutinizer-ci.com/g/eljam/guzzle-jwt-middleware/?branch=master)
[![Code Coverage](https://img.shields.io/coveralls/eljam/guzzle-jwt-middleware.svg?style=flat-square)](https://coveralls.io/r/eljam/guzzle-jwt-middleware)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/87bbdd85-2cd8-4556-94c6-5ed9f501cf7d/mini.png)](https://insight.sensiolabs.com/projects/87bbdd85-2cd8-4556-94c6-5ed9f501cf7d)
[![Latest Unstable Version](https://poser.pugx.org/eljam/guzzle-jwt-middleware/v/unstable)](https://packagist.org/packages/eljam/guzzle-jwt-middleware)
[![Latest Stable Version](https://poser.pugx.org/eljam/guzzle-jwt-middleware/v/stable)](https://packagist.org/packages/eljam/guzzle-jwt-middleware)
[![Downloads](https://img.shields.io/packagist/dt/eljam/guzzle-jwt-middleware.svg)](https://packagist.org/packages/eljam/guzzle-jwt-middleware)
[![license](https://img.shields.io/packagist/l/eljam/guzzle-jwt-middleware.svg)](https://github.com/eljam/guzzle-jwt-middleware/blob/master/LICENSE)

## Introduction

Works great with [LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle)

## Installation

`composer require eljam/guzzle-jwt-middleware`

## Usage

```php
<?php

use Eljam\GuzzleJwt\JwtMiddleware;
use Eljam\GuzzleJwt\Manager\JwtManager;
use Eljam\GuzzleJwt\Strategy\Auth\QueryAuthStrategy;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

require_once 'vendor/autoload.php';

//Create your auth strategy
$authStrategy = new QueryAuthStrategy(['username' => 'admin', 'password' => 'admin']);

$baseUri = 'http://api.example.org/';

// Create authClient
$authClient = new Client(['base_uri' => $baseUri]);

//Create the JwtManager
$jwtManager = new JwtManager(
    $authClient,
    $authStrategy,
    [
        'token_url' => '/api/token',
    ]
);

// Create a HandlerStack
$stack = HandlerStack::create();

// Add middleware
$stack->push(new JwtMiddleware($jwtManager));

$client = new Client(['handler' => $stack, 'base_uri' => $baseUri]);

try {
    $response = $client->get('/api/ping');
    echo($response->getBody());
} catch (TransferException $e) {
    echo $e->getMessage();
}

//response
//{"data":"pong"}

```

## Auth Strategies

### QueryAuthStrategy

```php
$authStrategy = new QueryAuthStrategy(
    [
        'username' => 'admin',
        'password' => 'admin',
        'query_fields' => ['username', 'password'],
    ]
);
```

### FormAuthStrategy

```php
$authStrategy = new FormAuthStrategy(
    [
        'username' => 'admin',
        'password' => 'admin',
        'form_fields' => ['username', 'password'],
    ]
);
```

### HttpBasicAuthStrategy

```php
$authStrategy = new HttpBasicAuthStrategy(
    [
        'username' => 'admin',
        'password' => 'password',
    ]
);
```

## Token key

By default this library assumes your json response has a key `token`, something like this:

```javascript
{
    token: "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXUyJ9..."
}
```

but now you can change the token_key in the JwtManager options:

```php
$jwtManager = new JwtManager(
    $authClient,
    $authStrategy,
    [
        'token_url' => '/api/token',
        'token_key' => 'access_token',
    ]
);
```

## Cached token

To avoid too many calls between multiple request, there is a cache system.

Json example:

```javascript
{
    token: "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXUyJ9...",
    expires_in: "3600"
}
```

```php
$jwtManager = new JwtManager(
    $authClient,
    $authStrategy,
    [
        'token_url' => '/api/token',
        'token_key' => 'access_token',
        'expire_key' => 'expires_in', # default is expires_in if not set
    ]
);
