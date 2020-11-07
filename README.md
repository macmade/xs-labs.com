xs-labs.com
===========

[![Build Status](https://img.shields.io/travis/macmade/xs-labs.com.svg?branch=master&style=flat)](https://travis-ci.org/macmade/xs-labs.com)
[![Issues](http://img.shields.io/github/issues/macmade/xs-labs.com.svg?style=flat)](https://github.com/macmade/xs-labs.com/issues)
![Status](https://img.shields.io/badge/status-active-brightgreen.svg?style=flat)
![License](https://img.shields.io/badge/license-bsd-brightgreen.svg?style=flat)
[![Contact](https://img.shields.io/badge/contact-@macmade-blue.svg?style=flat)](https://twitter.com/macmade)  
[![Donate-Patreon](https://img.shields.io/badge/donate-patreon-yellow.svg?style=flat)](https://patreon.com/macmade)
[![Donate-Gratipay](https://img.shields.io/badge/donate-gratipay-yellow.svg?style=flat)](https://www.gratipay.com/macmade)
[![Donate-Paypal](https://img.shields.io/badge/donate-paypal-yellow.svg?style=flat)](https://paypal.me/xslabs)

Table of contents
-----------------

 * [About](#about)
 * [Motivation](#motivation)
 * [Typical request flow](#typical-request-flow)
 * [Filesystem hierarchy](#filesystem-hierarchy)
 * [Menu XML file](#menu-xml-file)
 * [Blog](#blog)
 * [Modules](#modules)
 * [Classes](#classes)
 * [`XS\Menu` class](#xs-menu-class)
 * [Server technical requirements](#server-technical-requirements)

<a name="about"></a>
About
-----

Source repository for the www.xs-labs.com website.

<a name="motivation"></a>
Motivation
----------

The motivation behind this website was to create a fast, extensible, easy to maintain and easy to publish website, relying on static content pages and using [Bootstrap](http://getbootstrap.com) as a CSS framework.

The whole website was developed from scratch, as most CMS solutions are most often not satisfying.

While some CMS solutions are perfectly fine, they are usually too complicated and slow for simple websites, and requires too much maintenance.
Technical implementation and templating requires specific skills, so does editing.

Simpler solutions do exist, but usually relying on awful legacy and unmaintainable code, and producing tons of crappy HTML output.

Deployment is also usually hard with such CMS solutions, as they do often rely on databases.  
With static content, a complete website can be easily deployed to web servers using versioning systems like Git.  
Publishing content is then as simple as editing, committing, and pushing.

You also get versioning for free, and as Git allows you to use multiple remotes, you can push anytime to multiple servers (development, staging, production, etc.).

This website was also designed with extensibility in mind, with modules that can easily be created or extended with standard PHP coding skills.

The following document describes the basic functionalities, operation and implementation details of this website.

<a name="typical-request-flow"></a>
Typical request flow
--------------------

Let's say the server receives a request like `http://www.xs-labs.com/en/projects/xeos/`.

As the `.htaccess` files specifies rewriting rules (`mod_rewrite`), the request will end up in the main `index.php` file, unless a rewrite rule is matched (for specific pages like the blog, or website resources like images, stylesheets, etc.).

The `index.php` file will first include the `include/init.inc.php` file, which will initialise the environment (PHP requirements checks, autoloading, etc.) and initialise the `XS\Layout` class with the header and footer files (`include/header.inc.php` and `include/footer.inc.php`).

It will the process the request URL and checks if the requested URL is associated to a valid page, defined in the `include/menu.en.xml` file, by using the `XS\Menu` class.

If the page is valid, it will get the page content (in the above example from the `/en/projects/xeos/index.php` file).  
Note that PHP content in that page will be executed, allowing dynamic content.

If not (invalid URL), it will get content from an error page, like `/errors/404/index.php`.

Using the `XS\Layout` class, it will then print the page header, content and footer.

<a name="filesystem-hierarchy"></a>
Filesystem hierarchy
--------------------

 * **banners/default/index.php**
 Default page header's panner.
 * **blog/comments.xml**
 XML file with all blog comments - used by the blog module.
 * **blog/posts.xml**
 XML file with all blog posts - used by the blog module.
 * **blog.php**
 Blog specific page - similar to the main `index.php` file but dedicated to the blog, instantiating the blog module.
 * **bootstrap/**
 Bootstrap distribution
 * **classes/XS/**
 Core classes and predefined modules, automatically loaded on demand.
 * **css/**
 Custom template stylesheets.
 * **en/**
 English website content.
 * **errors/**
 Custom error pages.
 * **feed/**
 RSS and Atom feeds for the blog.
 * **include/footer.inc.php**
 Template footer file.
 * **include/header.inc.php**
 Template header file.
 * **include/init.inc.php**
 Initialisation script, required to set the working PHP environment.
 * **include/menu.en.xml**
 Website menu (english version) describing the whole website structure.
 * **index.php**
 Main page which handles all the pages requests.
 * **js**
 Additional JavaScript files used by the template.
 * **l10n**
 Localisation file for modules, by language.
 * **retina.php**
 Support for retina displays - Automatically tries to load @2x images.
 * **sitemap/index.php**
 Google XML sitemap
 * **tmp**
 General purpose temporary directory used by modules. 
 * **uploads**
 General purpose storage directory for website content: images, resources, etc.

---

<a name="menu-xml-file"></a>
Menu XML file
-------------

**Defines the whole website hierarchy, per language.**  
Used to validate requested URLs, to create navigation menus, sitemaps, etc.

**Location:** `include/menu.en.xml`  
*Note:* Each website language needs a specific XML file.

**Example structure:**

    <?xml version="1.0" encoding="utf-8"?>
    <menu lang="en">
	    <page1>
	        <title>Page 1</title>
	    </page1>
	    <page2>
	        <title>Page 2</title>
	        <sub>
	            <subpage>
	                <title>Subpage</title>
	            </subpage>
	        </sub>
	    </page2>
	</menu>

**Attributes of the root `menu`tag:**

 * `lang`  
 Defines the language of the menu.
 * `root`  
 Defines the title of the home page.
 * `keywords`
 Defines the website's global keywords (comma separated) - used for the meta tags.
 * `description`  
 Defines the website's global description - used for the meta tags.
 * `image`  
 Defines the website's header image - used in the template.

*Note:* The keywords, description and image are inherited for all subpages, unless one override them.

**Root level pages**

Any sub tag in `menu` will define a page.  
The tag name is the URL portion as well as the folder name for the page's content.

In the example above, the URL of `page1` will be `/en/page1/`.  
On the filesystem, the page's content will be in the `/en/page1/index.php` file.

**Possible attributes for page tags:**

 * **hidden="yes"**
 Do not display the page in navigation menus.
 * **sitemap="no"**
 Do not display the page in sitemaps.
 * **preview="yes"**
 Defines the page as a draft.

**Possible sub tags for page tags:**

 * **title**
 Defines the page's title.
 * **subtitle**
 Defines the page's subtitle - may be used in the template.
 * **linkTitle**
 Optionally defines a title for the links to this page (in the `href`).
 * **navTitle**
 Optionally defines a specific title to use in navigation menus.
 * **anchor**
 Adds an anchor to the generated URL for all links. Eg. `#some_anchor`.
 * **redirect**
 Redirects to a specific URL.
 * **sub**
 Defines the subpages - sub tags are pages tags as well.

---

<a name="blog"></a>
Blog
----

This website also includes a blog subsystem, defined by the `XS\Blog` class.

Its basically similar to standard content pages, but the content is retrieved from a different directory on the server.  
Also, a specific XML file is used to defined the blog articles.

This allows a specific presentation for the blog pages, with a front page automatically displaying all articles.

Articles are organised into categories, and can be commented by website visitors.

### Requests

As defined in the `.htaccess`file, all requests beginning with `/blog/` will be redirected to the `/blog.php` file instead of the main `/index.php` file.

As the main index file, it will checks the URL to find a matching blog article to display, or display the article list.

### Blog posts XML file

Defines the blog articles.
Used to validate requested URLs, to create the blog home, related articles, etc.

Location: `blog/posts.xml`

Example structure:

    <?xml version="1.0" encoding="utf-8"?>
    <blog>
    <post>
            <date>2016/03/18</date>
            <time>09:00</time>
            <author>Jean-David Gadina</author>
            <name>hello-world</name>
            <title>Hello, world</title>
            <category>Software</category>
        </post>
    </blog>

Each post is a `post` tag in the root `blog` tag.

Possible sub tags for post tags:

 * **date**
   The post's date - also used in the URL and filesystem hierarchy.
 * **time**
   The post's time.
 * **author**
   The name of the post's author.
 * **name**
   The post's name - also used in the URL and filesystem hierarchy.
 * **title**
   The post's title
 * **category**
   The post's category - used to find related articles.

<a name="modules"></a>
Modules
-------

Modules are standard PHP 5 classes, but a few conventions exist.

### Language files

Modules can use the `XS\Language\File` class in order to get their localised labels.

The `XS\Language\File`is a multiton class, and a specific instance can be retrieved using:

    $this->_lang = \XS\Language\File::getInstance( 'name' );

The `name` parameter can be anything, but usually a module will simply pass `__CLASS__` in order to get its own labels.

Labels are then retrieved as standard properties:

    print $this->_lang->label1;

Language files are stored in the `/l10n` directory.  

#### Example structure:

    <?xml version="1.0" encoding="utf-8"?>
    <labels>
        <label1>This is label 1</label1>
        <label2>This is label 2</label2>
    </labels>

### Outputting HTML

As a convention, modules uses the `__toString` magic method to output their contents.

All HTML output is created using the `XS\XHTML\Tag` class.

A specific tag can be created from the constructor, and then printed:

    $link = new \XS\XHTML\Tag( 'a' );
    
    print ( string )$link;
    
Tag attributes can be added with the subscript syntax:

    $link[ 'href'  ] = 'www.example.com';
    $link[ 'title' ] = 'Example site';
    
Text content can be added with the `addTextData()` method, while child nodes can be added with the `addChildNode()` method.

For convenience, child tags can also be created by using the `->` operator, and text content can then be assigned directly.

    $div = new \XS\XHTML\Tag( 'div' );
    
    $div->ul->li->strong = "Strong text in a list item";

<a name="classes"></a>
Classes
-------

 * **XS\Blog**
   The blog module.
 * **XS\Captcha**
   A Google reCAPTCHA module, to use in other modules. 
 * **XS\ClassManager**
  The main class manager, used for autoloading.
 * **XS\Crypto**
 Crypto functions.
 * **XS\Css\Minifier**
 CSS minifier class.
 * **XS\Debug**
 General purpose debug functions.
 * **XS\Exception\Base**
 Base class for exception classes.
 * **XS\Google\SiteMap**
 Module used to create Google sitemaps.
 * **XS\Language\File\Exception**
 Module exception class.
 * **XS\Language\File**
 Localisation helper class.
 * **XS\Layout\Exception**
 Module exception class.
 * **XS\Layout**
 Class managing the main website's layout.
 * **XS\Mail**
 Class used to create and send emails.
 * **XS\Menu**
 Class used to create navigation menus and get URLs to pages.
 * **XS\Session**
 PHP session helper class.
 * **XS\Singleton\Exception**
 Generic exception class for singleton classes.
 * **XS\SiteMap**
 Sitemap module.
 * **XS\Twitter\Feed\Exception**
 Module exception class.
 * **XS\Twitter\Feed**
 Last tweets module.
 * **XS\UUID**
 UUID class.
 * **XS\XHTML\Comment**
 Class representing an XHTML comment - used for HTML output in modules.
 * **XS\XHTML\Tag**
 Class representing an XHTML generic tag - used for HTML output in modules.

<a name="xs-menu-class"></a>
`XS\Menu` class
---------------

### Useful methods:

 * **`getRootLine( $sep = ' - ' )`**
  
   Gets the rootline (breadcrumb) as text.
   `$sep`: separator to use between elements.
   
 * **`getRootLineMenu( $class = 'rootline' )``**
  
   Gets the rootline (breadcrumb) menu.
   `$class`: CSS class to use.
   
 * **`getHomePageURL()`**
  
   Gets the URL of the home page.
   
 * **`getMenuLevel( $level, $showHidden = false )`**
  
   Gets a menu for a specific level-
   `$level`: the level to show.
   `showHidden`: also include pages marked as hidden.
   
 * **`getPageTitleHeader( $link = true, $lockOnLevel = 2 )`**
  
   Gets the current page's title header tag.
   `$link`: whether to add a link tag to the title.
   `$lockOnLevel`: For pages under the specified level, returns the title of the page at the specified level.
   
 * **`getPageSubtitleHeader()`**
  
   Gets the current page's subtitle header tag.
   
 * **`getPageTitle( $path = NULL )`**
  
   Gets a page's title.
   `$path`: optional - the page's path (none means the current page).
   
 * **`getPageSubtitle( $path = NULL )`**
  
   Gets a page's subtitle.
   `$path`: optional - the page's path (none means the current page).
   
 * **`isPreview( $path = '' )`**
  
 Checks if the current page is defined as a preview.
 
 * **`getLanguage()`**
  
   Gets the current language.
   
 * **`redirectOnFirstSubpage()`**
  
  Redirects the request to the first available subpage.
  
 * **`getPageLink( $path, $customTitle = '', array $params = array() )`**
  
   Gets a link to a specific page.
   `$path`: the page's path.
   `$customTitle`: optional - use a custom title instead of the page title as link text.
   `$params`: optional - additional URL parameters for the generated URL.
     
 * **`getPageUrl( $path, array $params = array() )`**
  
   Gets the URL of a specific page.
   `$path`: the page's path.
   `$params`: optional - additional URL parameters for the generated URL.
   
 * **`getCurrentPageLink( $customTitle = '', array $params = array() )`**
  
   Gets a link to the current page.
   `$customTitle`: optional - use a custom title instead of the page title as link text.
   `$params`: optional - additional URL parameters for the generated URL.
   
 * **`getCurrentPageUrl( array $params = array() )`**
  
   Gets the URL of the current page.
   `$params`: optional - additional URL parameters for the generated URL.
   
 * **`getDescription()`**
  
   Gets the description for the current page.
   
 * **`getKeywords()`**
  
   Gets the keywords for the current page.
   
 * **`isHomePage()`**
  
   Checks if the current page is the website's home page.
   
 * **`getPageImage( $width = 0, $height = 0 )`**
  
   Gets the image for the current page.
   `$width`: optional - the width for the generated `img` tag
   `$height`: optional - the height for the generated `img` tag
   
 * **`getPageBanner()`**
   
   Gets the banner for the current page.

<a name="server-technical-requirements"></a>
Server technical requirements
-----------------------------

 * Apache 2
 * mod_rewrite
 * PHP 5.x (w/ SPL and SimpleXML)

Repository Infos
----------------

    Owner:			Jean-David Gadina - XS-Labs
    Web:			www.xs-labs.com
    Blog:			www.noxeos.com
    Twitter:		@macmade
    GitHub:			github.com/macmade
    LinkedIn:		ch.linkedin.com/in/macmade/
    StackOverflow:	stackoverflow.com/users/182676/macmade
