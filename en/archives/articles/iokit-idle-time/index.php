<h2>Detecting idle time and activity with I/O Kit</h2>
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
    It may be sometimes useful, from an application, to know if the user is currently interacting with the computer (or phone), or if he's away.<br />
    This article explains how to detect user's activity, on Mac OS X and on iOS.
</p>
<h3>I/O Kit</h3>
<p>
    There's in no direct way, with Cocoa, to detect if the computer is idle.<br />
    Idle means no interaction of the user with the computer. No mouse move nor keyboard entry, etc. Actions made solely by the OS are not concerned.
</p>
<p>
    The OS has of course access to that information, to allow a screensaver to activate, or to initiate computer sleep.
</p>
<p>
    To access this information, we'll have to use I/O Kit.<br />
    It consist in a collection of frameworks, libraries and tools used mainly to develop drivers for hardware components.
</p>
<p>
    In our case, we are going to use IOKitLib, a library that allows programmers to access hardware resources through the Mac OS kernel.
</p>
<p>
    As it's a low-level library, we need to code in C to use it.<br />
    So we are going to wrap the C code in an Objective-C class, to allow an easier and generic usage, as the C code may be harder to code for some programmers.
</p>
<h3>Project configuration</h3>
<p>
    Before writing the actual code, we are going to configure our XCode project, so it can use IOKitLib.<br />
    As it's a library, it must be linked with the final binary.
</p>
<p>
    Let's add a framework to our project:
</p>
<p>
    <img src="/uploads/image/archives/articles/iokit-idle/framework-add.png" alt="" width="377" height="267" />
</p>
<p>
    For a Mac OS X application, we can choose «IOKit.framework».<br />
    For iOS, this framework is not available, so we must choose «libIOKit.dylib».
</p>
<p>
    <img src="/uploads/image/archives/articles/iokit-idle/framework-list.png" alt="" width="338" height="534" />
</p>
<p>
    The framework is added to our project, and will now be linked with the application, after compilation time.
</p>
<p>
    <img src="/uploads/image/archives/articles/iokit-idle/framework-cocoa.png" alt="" width="250" height="120" /><br />
    <img src="/uploads/image/archives/articles/iokit-idle/framework-iphone.png" alt="" width="250" height="120" />
</p>
<h3>IOKitLib usage</h3>
<p>
    First of all, here are the reference manuals for I/O Kit:
</p>
<ul>
<li>
        <a href="http://developer.apple.com/library/mac/#documentation/Darwin/Reference/IOKit/">IOKitLib</a>
    </li>
<li>
        <a href="http://developer.apple.com/mac/library/documentation/DeviceDrivers/Conceptual/AccessingHardware/">Accessing Hardware</a>
    </li>
<li>
        <a href="http://developer.apple.com/mac/library/documentation/DeviceDrivers/Conceptual/IOKitFundamentals/">I/O Kit Fundamentals</a>
    </li>
</ul>
<p>
    Now let's create an Objective-C class that will detect the idle time:
</p>
<pre class="code-block language-objc">
#include &lt;IOKit/IOKitLib.h&gt;

@interface IdleTime: NSObject
{
    mach_port_t   _ioPort;
    io_iterator_t _ioIterator;
    io_object_t   _ioObject;
}

@property( readonly ) uint64_t   timeIdle;
@property( readonly ) NSUInteger secondsIdle;

@end
</pre>
<p>
    This class has three instance variables, which will be used to communicate with I/O Kit.<br />
    The variables' types are defined by the «IOKit/IOKitLib.h», which we are including.
</p>
<p>
    We are also defining two properties, that we'll use to access the idle time. The first one in nanoseconds, the second one in seconds.
</p>
<p>
    Here's the basic implementation of the class:
</p>
<pre class="code-block language-objc">
#include "IdleTime.h"

@implementation IdleTime

- ( id )init
{
    if( ( self = [ super init ] ) )
    {
        
    }
    
    return self;
}

- ( void )dealloc
{
    [ super dealloc ];
}

- ( uint64_t )timeIdle
{
    return 0;
}

- ( NSUInteger )secondsIdle
{
    uint64_t time;
    
    time = self.timeIdle;
    
    return ( NSUInteger )( time >> 30 );
}

@end
</pre>
<p>
    We've got an «init» method that we will use to establish the base communication with I/O Kit, a «dealloc» method that will free the allocated resources, and a getter method for each property.
</p>
<p>
    The second method (secondsIdle) only takes the time in nanoseconds and converts it into seconds. To do so, we just have to divide the nano time by 10 raised to the power of 9. As we have integer values, a 30 right shift does exactly that, in a more efficient way.
</p>
<p>
    Now let's concentrate to the «init» method, and let's establish communication with I/O Kit, to obtain hardware informations.
</p>
<pre class="code-block language-objc">
- ( id )init
{
    kern_return_t status;
    
    if( ( self = [ super init ] ) )
    {
        
    }
    
    return self;
}
</pre>
<p>
    We a declaring a variable of type «kern_status» that we'll use to check the status of the I/O Kit communication, and to manage errors.<br />
    The following code is inside the «if» statement:
</p>
<pre class="code-block language-objc">
status = IOMasterPort( MACH_PORT_NULL, &#038;_ioPort );
</pre>
<p>
    Here, we establish the connection with I/O Kit, on the default port (MACH_PORT_NULL).
</p>
<p>
    To know if the operation was successfull, we can check the value of the status variable with «KERN_SUCCESS»:
</p>
<pre class="code-block language-objc">
if( status != KERN_SUCCESS )
{
    /* Error management... */
}
</pre>
<p>
    I/O Kit has many services. The one we are going to use is «IOHID». It will allow us to know about user interaction.<br />
    In the following code, we get an iterator on the I/O Kit services, so we can access to IOHID.
</p>
<pre class="code-block language-objc">
status = IOServiceGetMatchingServices
(
    _ioPort,
     IOServiceMatching( "IOHIDSystem" ),
    &#038;_ioIterator
);
</pre>
<p>
    Now we can store the IOHID service:
</p>
<pre class="code-block language-objc">
_ioObject = IOIteratorNext( _ioIterator );

if ( ioObject == 0 )
{
    /* Error management */
}

IOObjectRetain( _ioObject );
IOObjectRetain( _ioIterator );
</pre>
<p>
    Here, we are doing a retain, so the objects won't be automatically freed.<br />
    So we'll have to release then in the «dealloc» method:
</p>
<pre class="code-block language-objc">
- ( void )dealloc
{
    IOObjectRelease( _ioObject );
    IOObjectRelease( _ioIterator );
    
    [ super dealloc ];
}
</pre>
<p>
    Now the I/O Kit communication is established, and we have access to IOHID.<br />
    We can now use that service in the «timeIdle» method.
</p>
<pre class="code-block language-objc">
- ( uint64_t )timeIdle
{
    kern_return_t          status;
    CFTypeRef              idle;
    CFTypeID               type;
    uint64_t               time;
    CFMutableDictionaryRef properties;
    
    properties = NULL;
</pre>
<p>
    Let's start by declaring the variables we are going to use.
</p>
<p>
    First of all, we are going to access the IOHID properties.
</p>
<pre class="code-block language-objc">
status = IORegistryEntryCreateCFProperties
(
   _ioObject,
   &#038;properties,
   kCFAllocatorDefault,
   0
);
</pre>
<p>
    Here, we get a dictionary (similar to NSDictionary) in the «properties» variable.<br />
    We also get a kernel status, that we have to check, as usual.
</p>
<p>
    Now we can get the IOHID properties. The one we'll used is called «HIDIdleTime»:
</p>
<pre class="code-block language-objc">
idle = CFDictionaryGetValue( properties, CFSTR( "HIDIdleTime" ) );
    
if( !idle )
{
    CFRelease( ( CFTypeRef )properties );
    
    /* Error management */
}
</pre>
<p>
    If an error occurs, we have to release the «properties» object, in order to avoid a memory leak.
</p>
<p>
    A dictionary can contains several types of values, so we have to know the type of the «HIDIdleTime» property, before using it.
</p>
<pre class="code-block language-objc">
type = CFGetTypeID( idle );
</pre>
<p>
    The property can be of type «number» or «data». To obtain the correct value, each case must be managed.
</p>
<pre class="code-block language-objc">
if( type == CFDataGetTypeID() )
{
    CFDataGetBytes( ( CFDataRef )idle, CFRangeMake( 0, sizeof( time ) ), ( UInt8 * )&#038;time );
    
}
else if( type == CFNumberGetTypeID() )
{
    CFNumberGetValue( ( CFNumberRef )idle, kCFNumberSInt64Type, &#038;time );
}
else
{
    CFRelease( idle );
    CFRelease( ( CFTypeRef )properties );
    
    /* Error management */
}
</pre>
<p>
    Then we can release the objects, and return the value:
</p>
<pre class="code-block language-objc">
CFRelease( idle );
CFRelease( ( CFTypeRef )properties );

return time;
</pre>
<p>
    The class is done. To use it, we just have to instantiate it and read the «secondsIdle» property (from a timer, for instance).
</p>
<h3>Demo</h3>
<p>
    Here's an example program using that class to display the idle time:
</p>
<p>
    <button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#idle_code">
        idle.m
    </button>
</p>
<p>
    To compile and execute it:
</p>
<pre class="code-block nohighlight">
gcc -Wall -framework Cocoa -framework IOKit -o idle idle.m &#038;&#038; ./idle
</pre>
<div class="modal fade" id="idle_code">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">idle.m</h4>
            </div>
            <div class="modal-body">
                <pre class="code-block language-objc">
/*******************************************************************************
 * Copyright (c) 2011, Jean-David Gadina &lt;macmade@eosgarden.com&gt;
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 
 *  -   Redistributions of source code must retain the above copyright notice,
 *      this list of conditions and the following disclaimer.
 *  -   Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the
 *      documentation and/or other materials provided with the distribution.
 *  -   Neither the name of 'Jean-David Gadina' nor the names of its
 *      contributors may be used to endorse or promote products derived from
 *      this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 ******************************************************************************/

#include &lt;stdio.h&gt;
#include &lt;stdlib.h&gt;
#include &lt;unistd.h&gt;

#include &lt;CoreFoundation/CoreFoundation.h&gt;
#include &lt;CoreServices/CoreServices.h&gt;
#include &lt;IOKit/IOKitLib.h&gt;
#include &lt;Cocoa/Cocoa.h&gt;

/******************************************************************************/

@interface IdleTime: NSObject
{
@protected
    
    mach_port_t   _ioPort;
    io_iterator_t _ioIterator;
    io_object_t   _ioObject;
    
@private
    
    id r1;
    id r2;
}

@property( readonly ) uint64_t   timeIdle;
@property( readonly ) NSUInteger secondsIdle;

@end

/******************************************************************************/

@implementation IdleTime

- ( id )init
{
    kern_return_t status;
    
    if( ( self = [ super init ] ) )
    {
        status = IOMasterPort( MACH_PORT_NULL, &_ioPort );
        
        if( status != KERN_SUCCESS )
        {
            @throw [ NSException
                        exceptionWithName:  @"IdleTimeIOKitError"
                        reason:             @"Error communicating with IOKit"
                        userInfo:           nil
                   ];
        }
        
        status = IOServiceGetMatchingServices
        (
            _ioPort,
            IOServiceMatching( "IOHIDSystem" ),
            &_ioIterator
        );
        
        if( status != KERN_SUCCESS )
        {
            @throw [ NSException
                        exceptionWithName:  @"IdleTimeIOHIDError"
                        reason:             @"Error accessing IOHIDSystem"
                        userInfo:           nil
                   ];
        }
        
        _ioObject = IOIteratorNext( _ioIterator );
        
        if( _ioObject == 0 )
        {
            IOObjectRelease( _ioIterator );
            
            @throw [ NSException
                        exceptionWithName:  @"IdleTimeIteratorError"
                        reason:             @"Invalid iterator"
                        userInfo:           nil
                   ];
        }
        
        IOObjectRetain( _ioObject );
        IOObjectRetain( _ioIterator );
    }
    
    return self;
}

- ( void )dealloc
{
    IOObjectRelease( _ioObject );
    IOObjectRelease( _ioIterator );
    
    [ super dealloc ];
}

- ( uint64_t )timeIdle
{
    kern_return_t          status;
    CFTypeRef              idle;
    CFTypeID               type;
    uint64_t               time;
    CFMutableDictionaryRef properties;
    
    properties = NULL;
    status     = IORegistryEntryCreateCFProperties
    (
        _ioObject,
        &properties,
        kCFAllocatorDefault,
        0
    );
    
    if( status != KERN_SUCCESS || properties == NULL )
    {
        @throw [ NSException
                    exceptionWithName:  @"IdleTimeSystemPropError"
                    reason:             @"Cannot get system properties"
                    userInfo:           nil
               ];
    }
    
    idle = CFDictionaryGetValue( properties, CFSTR( "HIDIdleTime" ) );
    
    if( !idle )
    {
        CFRelease( ( CFTypeRef )properties );
        
        @throw [ NSException
                    exceptionWithName:  @"IdleTimeSystemTimeError"
                    reason:             @"Cannot get system idle time"
                    userInfo:           nil
               ];
    }
    
    CFRetain( idle );
    
    type = CFGetTypeID( idle );
    
    if( type == CFDataGetTypeID() )
    {
        CFDataGetBytes
        (
            ( CFDataRef )idle,
            CFRangeMake( 0, sizeof( time ) ),
            ( UInt8 * )&time
        );
        
    }
    else if( type == CFNumberGetTypeID() )
    {
        CFNumberGetValue
        (
            ( CFNumberRef )idle,
            kCFNumberSInt64Type,
            &time
        );
    }
    else
    {
        CFRelease( idle );
        CFRelease( ( CFTypeRef )properties );
        
        @throw [ NSException
                    exceptionWithName:  @"IdleTimeTypeError"
                    reason:             [ NSString stringWithFormat: @"Unsupported type: %d\n", ( int )type ]
                    userInfo:           nil
               ];
    }
    
    CFRelease( idle );
    CFRelease( ( CFTypeRef )properties );
    
    return time;
}

- ( NSUInteger )secondsIdle
{
    uint64_t time;
    
    time = self.timeIdle;
    
    return ( NSUInteger )( time &gt;&gt; 30 );
}

@end

/******************************************************************************/

@interface Test: NSObject
{
@protected
    
    NSTimer  * _timer;
    IdleTime * _idle;
    
@private
    
    id r1;
    id r2;
}

@end

/******************************************************************************/

@implementation Test

- ( void )printIdleTime: ( NSTimer * )timer
{
    NSLog( @"Idle time in seconds: %u", ( unsigned int )_idle.secondsIdle );
}

- ( id )init
{
    if( ( self = [ super init ] ) )
    {
        _idle  = [ [ IdleTime alloc ] init ];
        _timer = [ NSTimer
                    scheduledTimerWithTimeInterval: 1
                    target:                         self
                    selector:                       @selector( printIdleTime: )
                    userInfo:                       NULL
                    repeats:                        YES
                ];
    }
    
    return self;
}

- ( void )dealloc
{
    [ _timer invalidate ];
    [ _idle release ];
    [ super dealloc ];
}

@end

/******************************************************************************/

BOOL shouldKeepRunning = YES;

int main( int argc, char * argv[] )
{
    NSRunLoop         * loop;
    NSAutoreleasePool * pool;
    Test              * test;
    
    loop = [ NSRunLoop currentRunLoop ];
    pool = [ [ NSAutoreleasePool alloc ] init ];
    test = [ [ Test alloc ] init ];
    
    while( shouldKeepRunning && [ loop runMode: NSDefaultRunLoopMode beforeDate: [ NSDate distantFuture ] ] );
    
    [ test release ];
    [ pool release ];
    
    return EXIT_SUCCESS;
}
                </pre>
            </div>
        </div>
    </div>
</div>
