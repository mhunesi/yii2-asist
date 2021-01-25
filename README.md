Yii2 Asist BT SMS
=================
Yii2 Asist BT SMS Service Component

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist mhunesi/yii2-asist "*"
```

or add

```
"mhunesi/yii2-asist": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
try{
    $asist->smsProxy()
        ->addReceiver(['905555555555','905377289552'])
        ->setMessage(['Message1 text','Message2 text'])
        ->setOriginator('COMPANY')
        ->sendSms();
}catch (AsistExceptions $e){
    $e->getMessage();
}
```