# PlaceHolderSystem

A simple class for replace value in a string pattern


## Requirements

 - PHP >= 7.4
 - Composer

## Installation

```bash
composer require smn/phsystem
```

## Usage

This simple class use a pattern and a list of placeholders

Pattern is the string or phrase with placeholder. Placeholder are wrapped in "{ }"

List of placeholders is a list containing pair key/value , where value is a simple string or a callback 


#### Simple value
```php
<?php
$ph = new PlaceHolderSystem();
$ph->setPattern('I have 5 {fruit}');
$ph->addPlaceHolder('fruit', 'apple');
echo $ph->render(); // I have 5 apple

```

#### Callback function
```php
<?php
$ph = new PlaceHolderSystem();
$ph->setPattern('i learned how to count to {number}');
$ph->addPlaceHolder('number',function() {
 return 30;
});

echo $ph->render(); // i learned how to count to 30
```

#### Callback function with parameters

```php
<?php

class Num {

  public $val = 5;

}

$instance = new Num();
$ph = new PlaceHolderSystem();
$ph->setPattern('i learned how to count to {number}');
$ph->addPlaceHolder('number', function($param) {
 return $param->val;
}, [$instance]);
// Third parameter of addPlaceHolder method is an array with a list of parameters for callback function.

echo $ph->render(); // i learned how to count to 5

$instance->val = pow(2,16);
echo $ph->render(); // i learned how to count to 65536

```