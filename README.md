![](https://github.com/rahendz/hmvci/raw/master/application/includes/assets/images/hmvci-logo.png)

# HMVCI for Codeigniter v2.x
Hierarchical MVC with customized Codeigniter based on Official v2.2.x, if you're using CI version 3.x then i'm not recommended using my hmvci. My Hmvci for CI version 3.x is still under developing.

### Features:
- [x] Hierarchical MVC Modular
- [x] Wordpress-like Theme Structure with different path for backend and frontend (beta)
- [x] Simple RESTful API server and client (beta)
- [ ] Scaffolding-like from CI v1.7.x (coming soon)
- [ ] OAUTH 2.0 (coming soon)

### Included System
- [x] Codeigniter v2.2.5
- [x] Codeigniter v3.1.9

### Included Assets:
Some of it will removed or replaced considering to reduced the size of frameworks.
- [x] Bootstrap v3.3.7
- [x] Bootstrap v4.0.0-alpha.4 (will updated to v4.1)
- [x] Font Awesome v4.6.3 (will replaced with simple line icon)
- [x] Dashicons (removed)
- [x] Google Lato Font
- [x] Select2 v3.2 (removed)
- [x] TinyMCE v4.1.9 (removed)
- [x] HTML5Shiv.js v3.7.0 (will updated)
- [x] JQuery v2.2.4 (removed)
- [x] JQuery v3.1.1 (will updated to v3.3.1)
- [x] Nestable (removed)
- [x] Respons JS v1.1.0 (removed)

## How-To
1.  Download or Clone HMVCI
2.  In root directory, duplicate `paths-sample.php` and renamed into `paths.php`
3.  And choose included system on `paths.php` file, v2.2.5 or v3.1.9. Or if you have your own version, put it in system folder and set to it.
4.  If you are using database then rename `config-sample.php` into `config.php` then edit the configuration.
5.  By default the controller route is set to basic at `application/controller/basic.php`
6.  Accessing home url `(*http://localhost/index.php*)` will show the `basic_message.php` that stored at `application/views`.
7. To show the modular works just accesing the welcome page `(*http://localhost/index.php/welcome*)`, it will call the welcome controller which stored at `application/modules/welcome/controller/welcome.php`

## Simple Documentation
You can check manually simple documentation that implemented in welcome controller at `application/modules/welcome`, 
there will be 4 folders, config, controllers, models and views.

Or, visit the [wiki](https://github.com/rahendz/HMVCI/wiki) for complete documentation and feel free to ask for more guidance or just report bugs.