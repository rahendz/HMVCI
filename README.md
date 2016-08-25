# HMVCI for Codeigniter v2.x
Custom Codeigniter based on Official v2.2.x, if you're using CI version 3.x then i'm not recommended
using my hmvci. My Hmvci for CI version 3.x is still under developing.

### Under-developed Features:
- [x] HMVC Modular
- [x] Wordpress-like Theme Structure
- [x] Simple RESTful API server and client
- [ ] Scaffolding-like from CI v1.7.x (will integrate soon)
- [ ] OAUTH 2.0 (coming soon)

### Included Assets:
- [x] Bootstrap
- [x] Font Awesome
- [x] Dashicons
- [x] Lato Font
- [x] Select2
- [x] TinyMCE
- [x] HTML5Shiv.js
- [x] JQuery
- [x] Nestable
- [x] Less.js

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
#### Registering file CSS to theme
Push the **stylesheet** to theme using `enqueue_style` with 4 available parameter:
- **$id**: (*string/array*) Style id for registering.
- **$file**: (*string*) Stylesheet filename and path.
- **$dependency**: (*array*) File that needed for your stylesheet run properly, leave it blank array when your script doesn't have any dependency.
- **$version**: (*string*) Optional. your stylesheet version.

<!-- -->

	$this->enqueue_style( $id, $file, $dependency, $version );

OR

	$this->enqueue_style( array( $id => array ( $file, $dependency, $version ) ) );

example:

	$this->enqueue_style( 'style', 'css/style.css', array('bootstrap'), '1.1.2' );

and then use `theme_enqueue_head();` put it in tag head on your theme to load all your registered stylesheet.

---
#### Registering file JS to theme
Push the **javascript** to theme using `enquque_script` with 4 available parameter:
- **$id**: (*string/array*) Script id for registering.
- **$file**: (*string*) Stylesheet filename and path.
- **$dependency**: (*array*) File that needed for your stylesheet run properly, leave it blank array when your script doesn't have any dependency.
- **$version**: (*string*) Optional. your stylesheet version.
- **$footer**: (*boolean*) It set your script placement, will be loaded at footer or inside tag head, default value is **false**.

<!-- -->

	$this->enqueue_script( $id, $file, $dependency, $version, $footer );

OR

	$this->enqueue_script( array( $id => array ( $file, $dependency, $version, $footer ) ) );

example:

	$this->enqueue_script( 'scripts', 'js/scripts.js', array('jquery'), '1.1.0', true );

and then use

	theme_enqueue_foot();

put it in end of tag body on your theme to load all your registered stylesheet.