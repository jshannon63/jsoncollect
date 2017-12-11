[![Build Status](https://travis-ci.org/jshannon63/jsoncollect.svg?branch=master)](https://travis-ci.org/jshannon63/jsoncollect)
[![StyleCI](https://styleci.io/repos/113889574/shield?branch=master)](https://styleci.io/repos/113889574)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)


# Supercharge your JSON using collections in PHP  

__Framework Agnostic.__

__100% PHPUnit Test Coverage.__
  
__Heavily dependent on [tightenco/collect](https://github.com/tightenco/collect), [Matt Stauffer's](https://twitter.com/stauffermatt) split of Laravel's Illuminate Collections.__

Surround your json objects with the power of collection methods. Allowing you to 
easily traverse your data with methods like first(), last(), reduce(), search(), filter(), map(), transform(), each() and many others.
  
See the Illuminate Collections documentation [here](https://laravel.com/docs/5.5/collections#available-methods) for more on available methods and usage.
  
Additionally, this package provides customized getters and setters for accessing keyed data elements. Described in more detail below.
## Installation
```
composer require jshannon63/jsoncollect  
```

## Usage

Supply your json to the JsonCollect constructor. The form of your 
JSON can be a String, a stdClass object or an Array. JsonCollect 
will recursively dive into the deepest depths of your JSON tree 
and convert everything to collections.
### Injecting your JSON
```php
use Jshannon63\JsonCollect\JsonCollect;

 
$collection = new JsonCollect($json);  
```
### Working with your JSON collection

As mentioned earlier, you should visit the Illuminate Collections documentation [here](https://laravel.com/docs/5.5/collections#available-methods) 
for more on the *"~100 available methods"* and their usage.
  
  Some fun examples:
```php
// send an email to all friends
$collection->getfriends()->each(function ($item, $key) use ($mailer,$subject,$body){
    $mailer->sendmail($item->emailaddress,$subject,$body);
});
  
// total all your invoices
$total = $collection->getinvoices()->pluck('total')->sum();
  
// update the sales tax rate for all Kentucky stores
$collection->getstores()->transform(function ($item, $key) use ($rate) {
    $item->settaxrate($rate);
});
```
Additionally there are custom getter and setter methods for your data. Simply call the
methods get or set with the key name appended to the method name to 
access your data directly to set or retrieve. 

```php

// to retrieve the element with the key "name"
$collection->getname(); 
  
// will set the value of the element with the key "phone"
$collection->setphone('123-456-7890');  
```

### Exporting your JSON when needed
The following export() method will return a complete JSON string representation 
of your collection's data.
```php
$json = $collection->export();
```
