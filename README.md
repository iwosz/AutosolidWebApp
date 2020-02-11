# Autosolid WebApp
Web application managing Autosolid web page. Application is powered by Symphony/Silex v2.

### Requirements
* Composer
* NPM

### Installation
1. Run `composer install` in console inside project root directory
2. Run `npm install` in console inside project root directory
3. Change file name *appconfig.sample.json* to *appconfig.json* and set config options specyfic to your server.
4. Edit gulpfile.js and change *projectDir* to your application wwwroot directory
5. Run `gulp deploy` in console inside project root directory
6. Open your application wwwroot in the browser

### Changelog
Description of important changes in each version of application.

#### v1.3 @2020-02-10
* Added reCaptcha v3 support
* Added EmailValidator plugin
* Added anty-spam-bots validation

#### v1.2 @2018-04-28
* Added Config class
* Added Lang class
* Moved Page class to separate file
* Moved WebAction class to separate file
* Minor changes in content

#### v1.1 @2018-03-22
* New HTML template
* Optimized sending contact mails
* Optimized pages control
* Added Monolog logger

#### v1.0 @2018-01-14
* Base working application
* Base controller providers
* HTML template

#### v0.1 @2018-01-11
* Initial Silex skeleton
* Base application to test framework behaviour
