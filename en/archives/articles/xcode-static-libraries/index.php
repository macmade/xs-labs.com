<h2>Using static libraries with XCode</h2>
<p>
    <dl class="dl-horizontal">
        <dt>Author</dt>
        <dd>
            Jean-David Gadina
        </dd>
        <dt>Copyright</dt>
        <dd>
            &copy; <?php print date( 'Y', time() ); ?> Jean-David Gadina - www.xs-labs.com - All Rights Reserved
        </dd>
        <dt>License</dt>
        <dd>
            This article is published under the terms of the <?php print  \XS\Menu::getInstance()->getPageLink( '/licenses/freebsd-documentation' ); ?>
        </dd>
    </dl>
</p>
<p>
    It's often usefull to split an XCode project in several parts: the UI related code, and the code that's not related to the UI (tools, libraries, etc).<br />
    Such a split has many advantages. It allows different compilation settings for each part and a better compilation time, as each part is built separately, if needed.<br />
    This article will teach you how to setup such an XCode project.
</p>
<h3>Projet creation</h3>
<p>
    First of all, let's create a new iPhone XCode project (the same applies for a Mac OS X application project).
</p>
<p>
    <img src="/uploads/image/archives/articles/xcode-static-libraries/objc-lib-1.png" />
</p>
<p>
    XCode creates by default the necessary files for a basic application.
</p>
<p>
    <img src="/uploads/image/archives/articles/xcode-static-libraries/objc-lib-2.png" />
</p>
<p>
    The «Targets» section consists of the different products that may be built by XCode.
</p>
<p>
    In our case, we have an application. This target type includes the copy of the resources (XIB files - UI), the compilation of the application source code, and finally the link of the object code with the libraries and frameworks used by the application.
</p>
<p>
    Each target can have specific build settings, that can be accessed through the contextual menu.
</p>

<p>
    <img src="/uploads/image/archives/articles/xcode-static-libraries/objc-lib-3.png" />
</p>
<h3>Targets</h3>
<p>
    An XCode project can be composed of several targets. It is necessary if external libraries are used, but it may also be useful to split a project and optimize the compilation of the final application.
</p>
<p>
    Let's start by creating a new class, named «MethodProvider», that we'll use, as it name implies, as a method provider for our application.
</p>
<p>
    <img src="/uploads/image/archives/articles/xcode-static-libraries/objc-lib-4.png" />
</p>
<p>
    We are now going to implement a static method named «doSomething».
</p>
<pre class="code-block language-objc">
/* MethodProvider.h */
#import &lt;UIKit/UIKit.h&gt;

@interface MethodProvider: NSObject
{}

+ ( void )doSomething;

@end

/* MethodProvider.m */
#import "MethodProvider.h"

@implementation MethodProvider

+ ( void )doSomething
{
    NSLog( @"Method 'doSomething' called..." );
}

@end
</pre>
<p>
    We are going to call that method from our application, when it's loaded. We'll add the call on the «application: didFinishLaunchingWithOptions:» method of the «MyAppAppDelegate» class.
</p>
<pre class="code-block language-objc">
- ( BOOL )application: ( UIApplication * )application didFinishLaunchingWithOptions: ( NSDictionary * )launchOptions
{
    [ _window addSubview: _viewController.view ];
    [ _window makeKeyAndVisible ];
    
    [ MethodProvider doSomething ];
    
    return YES;
}
</pre>
<p>
    Do not forget to include the header file for the method provider class in the «MyAppAppController.m» file:
</p>
<pre class="code-block language-objc">
#import "MyAppAppDelegate.h"
#import "MyAppViewController.h"
#import "MethodProvider.h"
</pre>
<p>
    Now, the «MethodProvider» class is compiled at the same time as other project files. We are now going to change that.
</p>
<p>
    Let's start by removing the class from our application's target. Simply uncheck the box at the side of the source file name.
</p>
<p>
    <img src="/uploads/image/archives/articles/xcode-static-libraries/objc-lib-5.png" />
</p>
<p>
    From now on, the class won't be compiled with other source files. The application build process will now fail, with the following message:
</p>
<pre class="code-block nohighlight">
_OBJC_CLASS_$_MethodProvider", referenced from:
objc-class-ref-to-MethodProvider in MyAppAppDelegate.o
ld: symbol(s) not found
collect2: ld returned 1 exit status
</pre>
<p>
    It tells that the linker has not found the symbol corresponding to our class, even if it's used from the «MyAppAppDelegate» class.
</p>
<p>
    So we are going to create a new target, whose goal will be to compile the «MethodProvider» class.
</p>
<p>
    <img src="/uploads/image/archives/articles/xcode-static-libraries/objc-lib-6.png" />
</p>
<h3>Static library</h3>
<p>
    The type of the target will be «static library»:
</p>
<p>
    <img src="/uploads/image/archives/articles/xcode-static-libraries/objc-lib-7.png" />
</p>
<p>
    A library is a specific object code format, which can provide applications with functions. A library can be static or dynamic.
</p>
<p>
    With a static library, the library's object code is integrated in the final binary, during link time.<br />
    There's only one binary, containing the library and the application codes. This way, the binary is larger, but the library does not need to be present on the host machine.
</p>
<p>
    With a dynamic library, only it's path is included in the application's binary. The kernel will load the library code in memory when the application is launched.<br />
    The final binary is smaller, but the library needs to be available on the host computer. The application's launch time may also be a bit slower.<br />
    The main advantage of a dynamic library is that different applications can refer to the same library.
</p>
<p>
    In our application, we're gonna choose a static library. On iOS, it's actually impossible to generate dynamic libraries.
</p>
<p>
    When several targets are available, we can switch from one to another from a drop-down menu, in the tool bar:
</p>
<p>
    <img src="/uploads/image/archives/articles/xcode-static-libraries/objc-lib-8.png" />
</p>
<p>
    We can now switch to our «Methods» target, and add the file(s) that need to be compiled:
</p>
<p>
    <img src="/uploads/image/archives/articles/xcode-static-libraries/objc-lib-9.png" />
</p>
<p>
    We can also see the library in the «Products» section:
</p>
<p>
    <img src="/uploads/image/archives/articles/xcode-static-libraries/objc-lib-10.png" />
</p>
<p>
    Now we have to link our main application with the library. This is done through the «Link Binary With Libraries» phase of the application's target.
</p>
<p>
    <img src="/uploads/image/archives/articles/xcode-static-libraries/objc-lib-11.png" />
</p>
<p>
    Let's add the library.
</p>
<p>
    <img src="/uploads/image/archives/articles/xcode-static-libraries/objc-lib-12.png" />
</p>
<p>
    The last step is to tell XCode that our application depends on the library, so it's compiled before the application, if necessary.<br />
    This can be done by adding a dependancy, in the «General» section of the application's target:
</p>
<p>
    <img src="/uploads/image/archives/articles/xcode-static-libraries/objc-lib-13.png" />
</p>
<p>
    Now we can build our application without any problem.
</p>
<p>
    This way of doing things can be used to split different parts of our code.<br />
    It's also used when we need to use external sources and libraries, like OpenCV (C++), JSON-Framework (Objective-C), etc.<br />
    We could choose to include the external source files in the same target as the application, but then it would be impossible to specify different compilation settings, and it will slow down the application's compilation time.
</p>
<h3>Categories</h3>
<p>
    When using Objective-C categories, the use of a static library may cause problems (a category is a way of adding methods to an existing class).
</p>
<p>
    We are going to add a category on the «UIApplication» class, in our library's target:
</p>
<pre class="code-block language-objc">
/* MyUIApp.h */
#import &lt;UIKit/UIKit.h&gt;

@interface UIApplication( MyUIApp )

- ( void )sayHello;

@end

/* MyUIApp.m */
#import "MyUIApp.h"

@implementation UIApplication( MyUIApp )

- ( void )sayHello
{
    NSLog( @"Hello" );
}

@end
</pre>
<p>
    Let's use the method in our application:
</p>
<pre class="code-block language-objc">
/* MyAppAppDelegate.m */
- ( BOOL )application: ( UIApplication * )application didFinishLaunchingWithOptions: ( NSDictionary * )launchOptions
{
    [ window addSubview: viewController.view ];
    [ window makeKeyAndVisible ];
    
    [ MethodProvider doSomething ];
    [ application sayHello ];
    
    return YES;
}
</pre>
<p>
    Do not forget to include the header file:
</p>
<pre class="code-block language-objc">
#import "MyUIApp.h"
</pre>
<p>
    Compilation will be successfull, but the application will crash, with the following message, displayed in the console:
</p>
<pre class="code-block nohighlight">
- [ UIApplication sayHello ]: unrecognized selector sent to instance 0x5911700
</pre>
<p>
    This particular case is caused by the dynamic aspect of the Objective-C language (symbol resolution occures at run-time), and by the linker that does not generate symbols for categories.
</p>
<p>
    To solve the problem, we have to fix the linker parameters, in the informations of the application's target, and add, in the «Other Linker Flags» section:
</p>
<pre class="code-block nohighlight">
-ObjC -all_load
</pre>
<p>
    <img src="/uploads/image/archives/articles/xcode-static-libraries/objc-lib-14.png" />
</p>
