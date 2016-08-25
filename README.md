# HMVCI for Codeigniter v2.x
Custom Codeigniter based on Official v2.2.x, if you're using CI version 3.x then i'm not recommended
using my hmvci. My Hmvci for CI version 3.x is still under developing.

### Under-developed Features:
- HMVC Modular
- Wordpress-like Theme Structure
- Simple RESTful API server and client
- Scaffolding-like from CI v1.7.x (will integrate soon)
- OAUTH 2.0 (coming soon)

### Included Assets:
- Bootstrap
- Font Awesome
- Dashicons
- Lato Font
- Select2
- TinyMCE
- HTML5Shiv.js
- JQuery
- Nestable
- Less.js

## How-To
1.  Download HMVCI (codeigniter core system not included)
2.  Download Codeigniter core system v2.x from official site (recommended to use v2.2.5 instead)
3.  Put system folder from Official Codeigniter inside HMVCI folder
4.  in root directory, duplicate and rename `index-sample.php` into `index.php`
5.  in `application/config` directory, duplicate and rename `config-sample.php` into `config.php`
6.  in `application/config` directory, duplicate and rename `database-sample.php` into `database.php`

## Simple Documentation
#### Extends Controller
There is 3 optional extends controller to use my modular and theme engine with properly.
- Api Controller *for using restful service as server or client*
- Private Controller *for backend purpose*
- Public Controller *for frontend purpose*
---
#### Theme Configuration
Configuration for theme, put it in `construct` each controller to have different theme on each of it. Or
just set it on `application/includes/controller/(public or private)` for all of public controller.

	$this->theme_var['config']['frontend'] = 'default';
*those are for frontend*

##### OR

	$this->theme_var['config']['backend'] = 'default';
*those are for backend*
---
#### Load file asset css and js
Push the **stylesheet** to theme

	$this->enqueue_style( $id, $file, $dependency, $version );

OR

	$this->enqueue_style( array( $id => array ( $file, $dependency, $version ) ) );

and then use

	theme_enqueue_head();

put it in tag head on your theme to load all your registered stylesheet.