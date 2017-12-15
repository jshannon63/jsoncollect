[![Build Status](https://travis-ci.org/jshannon63/jsoncollect.svg?branch=master)](https://travis-ci.org/jshannon63/jsoncollect)
[![StyleCI](https://styleci.io/repos/113889574/shield?branch=master)](https://styleci.io/repos/113889574)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)


# Supercharge your JSON using collections in PHP  

The JsonCollect package allows you to surround your JSON objects with the power of collection methods. Making it easy to traverse your data with methods like first(), last(), reduce(), search(), filter(), map(), transform(), each() and many others.

__Framework Agnostic.__

__100% PHPUnit Test Coverage.__
  
__Heavily dependent on [tightenco/collect](https://github.com/tightenco/collect), [Matt Stauffer's](https://twitter.com/stauffermatt) split of Laravel's Illuminate Collections.__

See the Illuminate Collections documentation [here](https://laravel.com/docs/5.5/collections#available-methods) for more on available methods and usage.
  
Additionally, this package provides customized getters and setters for accessing keyed data elements. Described in more detail below.
## Installation
```
composer require jshannon63/jsoncollect  
```
if installing in the Laravel framework, JsonCollect will depend on the frameworks copy of Illuminate Collections and tightenco/collect will not be required.
## Usage

Supply your data to the JsonCollect constructor. The form of your 
data can be a JSON String, a stdClass object or an Array. JsonCollect 
will recursively dive into the deepest depths of your JSON tree 
and convert everything to collections.
### Injecting your JSON
```php
use Jshannon63\JsonCollect\JsonCollect;

 
$collection = new JsonCollect($json);  
```
### Working with your JSON collection

JsonCollect provides custom getter and setter methods for your data. Simply call the methods "get" or "set" with the key name appended to the method name to access your data directly to retrieve or to create/update. 

```php
// to retrieve the element with the key "name"
$collection->getname(); 
  
// will set the value of the element with the key "phone"
$collection->setphone('123-456-7890');  
```

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
$collection->getstores()->where('state','KY')->transform(function ($item, $key) use ($rate) {
    return $item->settaxrate($rate);
});
```

### Starting from scratch with an empty JsonCollect object 
It is not necessary to provide data to JsonCollect if your goal is to build a new collection of JSON data. Simply "new up" an instance of JsonCollect and begin adding data. Notice how we use ArrayAccess to simplify our code, and to show flexibility we used the custom getter to retrieve the address collection for setting the city.
```php
$collection = new JsonCollect();

$collection['names'] = 'John Doe';

// or if you have multi-level data, you may add another JsonCollect

$collection['address'] = new JsonCollect();
$collection['address'] = new JsonCollect();
$collection['address']->setstreet('123 Fourth Street');
$collection->getaddress()->setcity('Louisville');
$collection['address']->setstate('KY');
$collection['address']->setzip('40201');

// and we can use the collection method dd() to view the contents...

$collection->dd();
```
Which generates the following output from the die-and-dump.
```bash
array(2) {
  'names' =>
  string(8) "John Doe"
  'address' =>
  class Jshannon63\JsonCollect\JsonCollect#327 (1) {
    protected $items =>
    array(4) {
      'street' =>
      string(6) "123 Fourth Street"
      'city' =>
      string(4) "Louisville"
      'state' =>
      string(5) "KY"
      'zip' =>
      string(3) "40201"
    }
  }
}
```
### Exporting your JSON when needed
The following export() method will return a complete JSON string representation 
of your collection's data. Note that export will accept the standard json_encode options.
```php
$json = $collection->export(JSON_PRETTY_PRINT);
```
Based on the previous example, this is what we would expect to see from our export.
```json
{
    "names": "John Doe",
    "address": {
        "street": "123 Fourth Street",
        "city": "Louisville",
        "state": "KY",
        "zip": "40201"
    }
}
```
## Contributing

If you would like to contribute refer to CONTRIBUTING.md