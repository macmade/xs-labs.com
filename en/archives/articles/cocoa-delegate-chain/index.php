<h2>Implementing a delegate chain system in Objective-C</h2>
<div class="row">
    <div class="col-md-10">
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
        <a name="delegation-about"></a>
        <h3>1. What's delegation?</h3>
        <p>
            Delegation is a concept available in some classes of the Cocoa framework, on Mac OS X (and of course, on iPhone OS).<br />
            That concept allows Cocoa application developers to interact on specific events of core Cocoa objects.
        </p>
        <p>
            Let's take, for instance, the NSWindow object. As it name implies, it allows to display and control a window.
        </p>
        <p>
            This window object has methods, like 'close' or 'open', allowing respectively to open and clase the window.
        </p>
        <p>
            When developping a Cocoa application, it can be very useful to know when a window will open or close, to allocate or free resources, end tasks or threads, etc.
        </p>
        <p>
            The delegation system of the Cocoa framework allows to attach an object's instance to another object, the first one beeing able to act on the second depending on its execution phases.
        </p>
        <p>
            Defining a delegate object on another object is usually done with the 'setDelegate' method, taking as unique parameter the instance of the delegate object.
        </p>
        <p>
            For instance, to define an object of type 'Foo' as the delegate of a NSWindow object:
        </p>
        <pre class="code-block language-objc">
Foo      * foo    = [ [ Foo alloc ] init ];
NSWindow * window = [ [ NSWindow alloc ] initWithContentRect: NSMakeRect( 0, 0, 100, 100 ) styleMask: NSTitledWindowMask backing: NSBackingStoreBuffered defer: NO ];

[ window setDelegate: foo ];
        </pre>
        <p>
            The two first lines respectively creates an object of type 'Foo' (defined in our application), and an object of type 'NSWindow' (from the Cocoa framework).
        </p>
        <p>
            The third line defines the 'Foo' object as a delegate of our 'NSWindow' object.
        </p>
        <p>
            From now on, if we close the window object.
        </p>
        <pre class="code-block language-objc">
[ window close ];
        </pre>
        <p>
            The delegate object can be noticed of the close operation by implementing a specific method. In our case, the 'windowWillClose' method. Here's the method's prototype:
        </p>
        <pre class="code-block language-objc">
- ( void )windowWillClose: ( NSNotification * )notification;
        </pre>
        <p>
            This will allow the delegate, just before the window closes, to perform operations required by the application.
        </p>
        <a name="delegation-explaination"></a>
        <h3>2. How does delegation work?</h3>
        <p>
            Now, let's see how the 'NSWindow' object implements and uses its delegate object.
        </p>
        <p>
            The 'NSWindow' object has of course an instance variable of type 'id', representing the delegate object and usually named delegate, as well as getter/setter methods for the delegate object.
        </p>
        <p>
            In other words:
        </p>
        <pre class="code-block language-objc">
@interface NSWindow: NSObject
{
@protected

    id _delegate;
}

- ( id )delegate;
- ( void )setDelegate: ( id )object;

@end

@implementation NSWindow

- ( id )delegate
{
    return _delegate;
}

- ( void )setDelegate: ( id )object
{
    delegate = _object;
}

@end
        </pre>
        <p>
            It's very important to remember that an object should never retain its delegate object, as this would result in a memory leak (a memory area that will never be freed).
        </p>
        <p>
            If using Objective-C 2.0, note that you can use a property in the interface declaration to allow an easy acces to the delegate object:
        </p>
        <pre class="code-block language-objc">
@property( nonatomic, assign, readwrite ) delegate;
        </pre>
        <p>
            From that point, the getter/setter methods can be automatically declared in the implementation:
        </p>
        <pre class="code-block language-objc">
@synthesize delegate;
        </pre>
        <p>
            Now, back to the 'close' method of the 'NSWindow' object:
        </p>
        <pre class="code-block language-objc">
- ( void )close
{
    /* Do something... */
    
    if( [ _delegate respondsToSelector: @selector( windowWillClose ) ] )
    {
        [ _delegate windowWillClose ];
    }
    
    /* Do something... */
}
        </pre>
        <p>
            At a specific time during the execution of the 'close' method, the 'NSWindow' object checks if delegate object implements a method named 'windowWillClose'.<br />
            If it has, it's executed. The 'close' method then continues its own execution.
        </p>
        <p>
            The 'close' method does not need to check if a delegate object has been previously defined, as it is valid in Objective-C to send a message (call a method) to 'nil' (a NULL pointer on an object).
        </p>
        <p>
            The delegate object will then be notified that the 'NSWindow' object did close, if it implements the 'windowWillClose' method.
        </p>
        <a name="delegation-notification"></a>
        <h3>3. Delegation and notification</h3>
        <p>
            The Cocoa framework also include a notification system, allowing objects to be notified about execution stages of other objects.
        </p>
        <p>
            In the previous example, we could also have written the following code, to be noticed about the window's close event:
        </p>
        <pre class="code-block language-objc">
[ [ NSNotificationCenter defaultCenter ] addObserver: foo selector: @selector:( myObserverMethod: ) name: NSWindowWillCloseNotification object: window ]:
        </pre>
        <p>
            In other words, we declare that the 'myObserverMethod' method of the 'Foo' object must be called when the window's 'NSWindowWillCloseNotification' event occurs. In such a case, here's the prototype of the 'myObserverMethod' method:
        </p>
        <pre class="code-block language-objc">
- ( void )myObserverMethod: ( NSNotification * )notification;
        </pre>
        <p>
            So what are the differences between those two methodologies?<br />
            The notification system only allows to be notified about some events, while the delegation system also allows to modify the behaviour of the concerned object.
        </p>
        <p>
            Let's take the 'windowShouldClose' method as an example. It can be implemented in the delegate of a 'NSWindow' object, and here's it's prototype:
        </p>
        <pre class="code-block language-objc">
- ( BOOL )windowShouldClose: ( NSWindow * )window;
        </pre>
        <p>
            We can here see that the method returns a boolean value.
        </p>
        <p>
            If our delegate object does implement this method, and if we call the 'close' method on the window, it will effectively close only if the delegate's method returns the 'YES' value. If not, the window will stay on the screen.
        </p>
        <p>
            This allows, for instance, to block the window's close process to display an alert message, asking the user if he wants to save it's data before closing the window.
        </p>
        <p>
            At this time, the delagate object takes the responsibility to know if the window has to be closed, and when. This would be impossible through the notification system.
        </p>
        <p>
            We can clearly see here the difference of logic between delegation and notification.
        </p>
        <p>
            Some classes of the Cocoa framework also use their delegate to obtain other types of informations, like the 'NSBrowser' (the column view of the Finder), which uses its delegate to know which items to display.
        </p>
        <a name="delegate-chain"></a>
        <h3>4. Chaining delegates</h3>
        <p>
            At this time, we can notice a limitation of the delegation system: an object can only have one unique delegate.
        </p>
        <p>
            Let's take the following code:
        </p>
        <pre class="code-block language-objc">
[ window setDelegate: foo ];
[ window setDelegate: bar ];
        </pre>
        <p>
            The delegate object of the 'window' object will be 'bar', which will override the 'foo' object, which won't be able to control the window anymore.
        </p>
        <p>
            Having multiple delegate objects could be useful in many cases, so we are going to implement a system allowing the delegates to be chained.
        </p>
        <a name="implementation-object"></a>
        <h3>5. Implementation - MultipleDelegateObject</h3>
        <p>
            First, we are going to create a base class for the classes needing multiple delegate objects:
        </p>
        <pre class="code-block language-objc">
/* MultipleDelegateObject.h */
@interface MultipleDelegateObject: NSObject
{
@protected

    DelegateChain * _delegate;
}

- ( void )addDelegate: ( id )object;
- ( void )removeDelegate: ( id )object;
- ( NSArray * )delegates;

@end;
        </pre>
        <p>
            We won't manage the delegate chain here, but in another class, named 'DelegateChain'. We'll see this class in a few moments.
        </p>
        <p>
            Our first class has methods allowing a delegate object to be added or removed, and a method allowing to get all the delegates in an array.
        </p>
        <p>
            Here's the implementation:
        </p>
        <pre class="code-block language-objc">
/* MultipleDelegateObject.m */
@implementation

- ( id )init
{
    if( ( self = [ super init ] ) )
    {
        _delegate = [ [ DelegateChain alloc ] init ];
    }
    
    return self;
}

- ( void )dealloc
{
    [ _delegate release ];
    [ super     dealloc ];
}

- ( void )addDelegate: ( id )object
{
    [ _delegate addDelegate: object ];
}

- ( void )removeDelegate: ( id )object
{
    [ _delegate removeDelegate: object ];
}

- ( NSArray * )delegates
{
    return [ _delegate delegates ];
}

@end
        </pre>
        <p>
            The 'init' method creates a new instance of the 'DelegateChain' class and stores it in the 'delegate' instance variable. The 'dealloc' method releases this resource when the object is freed.
        </p>
        <p>
            The three other methods only route the calls to the 'DelegateChain' object, which will manage the multiple delegates.
        </p>
        <a name="implementation-chain"></a>
        <h3>6. Implementation - DelegateChain</h3>
        <p>
            Let's see the interface of the 'DelegateChain' class:
        </p>
        <pre class="code-block language-objc">
/* DelegateChain.h */
@interface DelegateChain: NSObject
{
@protected

    id                  * _delegates;
    NSUInteger            _numberOfDelegates;
    NSUInteger            _sizeOfDelegatesArray;
    NSMutableDictionary * _hashs;
}

- ( void )addDelegate: ( id )object;
- ( void )removeDelegate: ( id )object;
- ( NSArray * )delegates;

@end
        </pre>
        <p>
            We've seen previously that we cannot retain a delegate object. So we cannot use an 'NSMutableArray' or 'NSMutableDictionary' object to store the delegates, as they would be automatically retained when added to the array or dictionary.
        </p>
        <p>
            But we can still use an array of pointers to the delegates (the 'id' type is in fact a pointer), allocated and re-allocated when necessary with the standard C library memory allocation functions. That's our 'delegates' instance variable.
        </p>
        <p>
            We also have a variable keeping the number of the associated delegates ('numberOfDelegates'), and another ('sizeOfDelegatesArray') keeping the size of the array of pointers.
        </p>
        <p>
            The 'hash' variable will be used to store the memory addresses of the delegate objects, so we'll be able to find their position easily in the array of pointers.
        </p>
        <p>
            Now let's see, method by method, the implementation of the 'DelegateChain' class. First of all, its initialization:
        </p>
        <pre class="code-block language-objc">
- ( id )init
{
    if( ( self = [ super init ] ) )
    {
        _hashs = [ [ NSMutableDictionary dictionaryWithCapacity: 10 ] retain ];
        
        if( NULL = ( _delegates = ( id * )calloc( 10, sizeof( id ) ) ) )
        {
            /* Error management... */
        }
    }
    
    return self;
}
        </pre>
        <p>
            We create the dictionary which will store the memory addresses, and we ask for a memory area to store the pointers to the delegate objects. At the initialization time, this area can store 10 objects. We are doing this to improve the performances, as we won't need to call the memory allocation functions each time a delegate is added. If we need more than 10 delegates, we will increase this area so it can store 10 objects more.
        </p>
        <p>
            As we allocated memory, we need to free it when the object is deallocated:
        </p>
        <pre class="code-block language-objc">
- ( void )dealloc
{
    free( _delegates );
    
    [ _hashs release ];
    [ super  dealloc ];
}
        </pre>
        <p>
            Now let's see the method used to add a delegate:
        </p>
        <pre class="code-block language-objc">
- ( void )addDelegate: ( id )object
{
    NSString * hash;
    
    if( object == nil )
    {
        return;
    }
    
    if( _numberOfDelegates == _sizeOfDelegatesArray )
    {
        if( NULL == ( _delegates = ( id * )realloc( _delegates, ( _sizeOfDelegatesArray + 10 ) * sizeof( id ) ) ) )
        {
            /* Error management... */
        }
        
        _sizeOfDelegatesArray += 10;
    }
    
    hash = [ [ NSNumber numberWithUnsignedInteger: ( NSUInteger )object ] stringValue ];
    
    if( [ _hashs objectForKey: hash ] != nil )
    {
        return;
    }
    
    _delegates[ _numberOfDelegates ] = object;
    
    [ _hashs setObject: [ NSNumber numberWithUnsignedInteger: numberOfDelegates ] forKey: hash ];
    
    _numberOfDelegates++;
}
        </pre>
        <p>
            We have previously allocated enough space for 10 delegates. If ten are set, and if another one is added, we just add space for 10 more objects with the 'realloc' function.
        </p>
        <p>
            Then we take the memory address of the object, as a string, and we check that the object is not already present in the delegates. This way, the same object can be added only once as a delegate.
        </p>
        <p>
            Finally, we need to store the pointer to our object, its memory address with its position in the pointer array, and incremenr by 1 the variable keeping the number of delegates.
        </p>
        <p>
            Now here's the method used to remove a delegate:
        </p>
        <div class="code-block language-objc">
- ( void )removeDelegate: ( id )object
{
    NSString   * hash;
    NSUInteger   index;
    NSUInteger   i;
    
    if( object == nil || _numberOfDelegates == 0 )
    {
        return;
    }
    
    hash = [ [ NSNumber numberWithUnsignedInteger: ( NSUInteger )object ] stringValue ];
    
    if( [ _hashs objectForKey: hash ] == nil )
    {
        return;
    }
    
    index = [ [ _hashs objectForKey: hash ] unsignedIntegerValue ];
    
    for( i = index; i < _numberOfDelegates - 1; i++ )
    {
        _delegates[ i ] = _delegates[ i + 1 ];
    }
    
    [ _hashs removeObjectForKey: hash ];
    
    _numberOfDelegates--;
}
        </div>
        <p>
            It's the same kind of stuff, but with a little extra.
        </p>
        <p>
            Suppose we have 5 delegates, and that we removed the object placed at the third position of the array of pointers. We have a gap. To avoid this, we re-arrange all the pointers placed after the one we just removed.
        </p>
        <p>
            And finally, the method used to get an array containing all the delegate objects.
        </p>
        <div class="code-block language-objc">
- ( NSArray * )delegates
{
    NSUInteger       i;
    NSMutableArray * delegatesArray;
    
    if( _numberOfDelegates == 0 )
    {
        return [ NSArray array ];
    }
    
    delegatesArray = [ NSMutableArray arrayWithCapacity: _numberOfDelegates ];
    
    for( i = 0; i < _numberOfDelegates; i++ )
    {
        [ delegatesArray addObject: _delegates[ i ] ];
    }
    
    return [ NSArray arrayWithArray: delegatesArray ];
}
        </div>
        <p>
            It's just a loop on the array of pointers, that adds the pointed objects to a 'NSArray' object.
        </p>
        <a name="runtime"></a>
        <h3>7. Runtime and method routing</h3>
        <p>
            We've seen previously that we can use the 'respondsToSelector' method to check if a delegate has a specific method.
        </p>
        <pre class="code-block language-objc">
if( [ _delegate respondsToSelector: @selector( someMethod ) ] )
{}
        </pre>
        <p>
            We are going to implement that behaviour on the 'DelegateChain' class.
        </p>
        <p>
            Actually, the code we just see can't work, as the 'DelegateChain' object, which stores the delegates, does not implement their methods.
        </p>
        <p>
            But we can override (re-declare) in that class the 'respondToSelector' method (which is declared originally in the 'NSObject' class), so it has another behaviour than the default one.
        </p>
        <div class="code-block language-objc">
- ( BOOL )respondsToSelector: ( SEL )selector
{
    NSUInteger i;
    
    for( i = 0; i < _numberOfDelegates; i++ )
    {
        if( [ _delegates[ i ] respondsToSelector: selector ] == YES )
        {
            return YES;
        }
    }
    
    return NO;
}
        </div>
        <p>
            We are looping on the array of pointers, and we check if one of the delegates has the method. This way, we can use the 'DelegateChain' object as if it were a normal and unique delegate object.
        </p>
        <p>
            For this to work, we also have to override the 'methodSignatureForSelector' method (NSObject). It allows the Objective-C runtime environment to get informations about a specific method, like its return type, its arguments, etc.
        </p>
        <div class="code-block language-objc">
- ( NSMethodSignature * )methodSignatureForSelector: ( SEL )selector
{
    NSUInteger i;
    
    for( i = 0; i < _numberOfDelegates; i++ )
    {
        if( [ _delegates[ i ] respondsToSelector: selector ] == YES )
        {
            return [ [ _delegates[ i ] class ] instanceMethodSignatureForSelector: selector ];
        }
    }
    
    return nil;
}
        </div>
        <p>
            Now we can know if at least one of the delegate objects has a specific method. But then how can we call it?
        </p>
        <p>
            We are going to keep the same way of calling a unique delegate. The method will be called directly on the 'DelegateChain' object, which will have to manage and re-route the call on the concerned delegates.
        </p>
        <p>
            We are going to implement the 'forwardInvocation' method:
        </p>
        <p>
            This method is automatically called by the Objective-C runtime environment when a method is called on an object which does not implement it. This way, the object has a last chance to manage the error.
        </p>
        <p>
            The same kind of concept is used in many different programming languages. It can be seen like C++ virtual function, or like the PHP5 '__call' method.
        </p>
        <div class="code-block language-objc">
- ( void )forwardInvocation: ( NSInvocation * )invocation
{
    NSUInteger i;
    
    for( i = 0; i < _numberOfDelegates; i++ )
    {
        if( [ _delegates[ i ] respondsToSelector: [ invocation selector ] ] == YES )
        {
            [ invocation invokeWithTarget: _delegates[ i ] ];
        }
    }
}
        </div>
        <p>
            The delegate chain system is now functionnal. To use it in a class, we just have to extend the 'MultipleDelegateObject' class. Nothing more is needed.
        </p>
        <a name="conclusion"></a>
        <h3>8. Afterwords</h3>
        <p>
            Such a system allows to define classes with an infinite number of delegates. But of course the Cocoa core objects, like 'NSWindow', won't be able to use that system.
        </p>
        <p>
            That said, it is possible to implement that multiple delegate system on objects like 'NSWindow'.
        </p>
        <p>
            The Objective-C language allows the definition of categories, which allows methods to be added on any existing class, even if it's a core Objective-C class. For instance:
        </p>
        <pre class="code-block language-objc">
@interface NSObject( MyCategory )

- ( void )sayHello;

@end

@implementation NSObject( MyCategory )

- ( void )sayHello
{
    NSLog( @"hello, world" );
}

@end
        </pre>
        <p>
            This code adds a 'sayHello' method in the 'NSObject' class, which is part of the Cocoa framework. As 'NSObject' is the root class of all Objective-C classes, all available classes will respond to the 'sayHello' method.
        </p>
        <p>
            So we could add in the same way the 'addDelegate', 'removeDelegate' and 'delegates' methods to the 'NSWindow' object.
        </p>
        <p>
            The only limitation with categories is that we cannot add instance variables to a class. But the 'NSWindow' object already have an instance variable for the delegate. We'll just have to override the 'setDelegate' method of 'NSWindow' in the category. A global static variable (whose access is limited to the file which declared it) is also a possibility to store the delegate chains.
        </p>
    </div>
    <div class="col-md-2 resume-menu">
        <div data-spy="affix" data-offset-top="0" data-offset-bottom="0" class="resume-menu-affix">
            <ul class="hidden-xs">
                <li>
                    <a href="#delegation-about" title="Go to this section">What's delegation?</a>
                </li>
                <li>
                    <a href="#delegation-explaination" title="Go to this section">How does delegation work?</a>
                </li>
                <li>
                    <a href="#delegation-notification" title="Go to this section">Delegation and notification</a>
                </li>
                <li>
                    <a href="#delegate-chain" title="Go to this section">Delegation chain</a>
                </li>
                <li>
                    <a href="#implementation-object" title="Go to this section">Implementation - MultipleDelegateObject</a>
                </li>
                <li>
                    <a href="#implementation-chain" title="Go to this section">Implementation - DelegateChain</a>
                </li>
                <li>
                    <a href="#runtime" title="Go to this section">Runtime and method routing </a>
                </li>
                <li>
                    <a href="#conclusion" title="Go to this section">Afterwords</a>
                </li>
            </ul>
        </div>
    </div>
</div>
