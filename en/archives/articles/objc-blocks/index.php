<h2>Closure and anonymous functions in Objective-C</h2>
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
        <a name="about"></a>
        <h3>1. About</h3>
        <p>
            Many scripting languages allows the use of «lambda» or «anonymous functions». That concept is often associated with the «closure» concept.<br />
            Such concepts are well known in JavaScript, ActionScript or PHP since version 5.3.<br />
            The Objective-C language offers an implementation of both concepts, called «blocks».<br />
            Blocks are available since Mac OS X 10.6, thanks to the use of Clang.
        </p>
        <a name="about-lambda"></a>
        <h4>Anonymous functions</h4>
        <p>
            As it name implies, an anonymous function is a function without a name, nor identifier. It only has its content (body), and can be stored in a variable, for a later use, or to be passed as an argument to another function.
        </p>
        <p>
            This concept is often used in scripting langauges for callbacks.
        </p>
        <p>
            In JavaScript, for instance, let's take a standard function called «foo», taking a callback as parameter, and executing it:
        </p>
        <pre class="code-block language-javascript">
function foo( callback )
{
    callback();
}
        </pre>
        <p>
            It is possible to define another standard function, and pass it as argument of the first function:
        </p>
        <pre class="code-block language-javascript">
function bar()
{
    alert( 'hello, world' );
}

foo( bar );
        </pre>
        <p>
            The problem is that we are declaring a «bar» function in the global scope. So we risk to override another function having the same name.
        </p>
        <p>
            The JavaScript language allows us to declare the callback function at call time:
        </p>
        <pre class="code-block language-javascript">
foo
{
    function()
    {
        alert( 'hello, world' );
    }
);
        </pre>
        <p>
            Here, the callback has no identifier. It won't exist in the global scope, so it can't conflict with another existing function.
        </p>
        <p>
            We can also define the callback as a variable. It still won't exist in the global scope, but it will be possible to re-use it through the variable:
        </p>
        <pre class="code-block language-javascript">
myCallback = function()
{
    alert( 'hello, world' );
};

foo( myCallback );
        </pre>
        <a name="about-closure"></a>
        <h4>Closure</h4>
        <p>
            The closure concept consist of the possibility for a function to access the variables available in its declaration context, even if it's not the same as it's execution context.
        </p>
        <p>
            In JavaScript again, let's see the following code:
        </p>
        <pre class="code-block language-javascript">
function foo( callback )
{
    alert( callback() );
}

function bar()
{
    var str = 'hello, world';

    foo
    (
        function()
        {
            return str;
        }
    );
}

bar();
        </pre>
        <p>
            The callback, passed to the «foo» function from the execution context of the «bar» function, returns a variable named «str».<br />
            But this variable, declared in the «bar» function's context, is local. It means it exists only from inside that context.<br />
            As the callback is executed from a different context than the one containing the variable declaration, we might think the code does not display anything.<br />
            But here comes the closure concept.<br />
            Even if its execution context is different, a function keeps an access to the variables avaialable in its declaration context.
        </p>
        <p>
            So the callback will have access to the «str» variable, even if it's called from the «foo» function, which does not have access to the variable.
        </p>
        <a name="objc"></a>
        <h3>Objective-C implementation</h3>
        <p>
            That kind of concept is available in Objective-C, with some differences, as Objective-C is a strongly typed compiled languaged, built on top of the C language, so very different of an interpreted scripting language.
        </p>
        <p>
            Note that blocks are also available in pure C, or C++ (and of course also in Objective-C++).
        </p>
        <p>
            As a standard C function, the declaration of a block (anonymous function) must be preceded by the declaration of its prototype.
        </p>
        <p>
            The syntax of a block is a bit tricky at first sight, but as function pointers, we get used to it with some time.<br />
            Here's a block prototype:
        </p>
        <pre class="code-block language-objc">
NSString * ( ^ myBlock )( int );
        </pre>
        <p>
            We are declaring here the prototype of a block («^»), that will be named «myBlock», taking as unique parameter an «int», et returning a pointer on a «NSString» object.
        </p>
        <p>
            Now we can declare the block itself:
        </p>
        <pre class="code-block language-objc">
myBlock = ^( int number )
{
    return [ NSString stringWithFormat: @"Passed number: %i", number ];
};
        </pre>
        <p>
            We assign to the «myBlock» variable a function's body, taking an integer as the «number» argument. This function returns a «NSString» object, in which the integer will be displayed.
        </p>
        <p>
            <strong>Notice: do not forget the semicolon at the end of the block declaration!</strong>
        </p>
        <p>
            If it can be ommited in scripting langauges, it's absolutely necessary for compiled languages like Objective-C.<br />
            If it's ommited, the compiler will produce an error, and the final executable won't be generated.
        </p>
        <p>
            The block can now be used, as a standard function:
        </p>
        <pre class="code-block language-objc">
myBlock();
        </pre>
        <p>
            Here's the complete source code of an Objective-C program, with the previous example:
        </p>
        <pre class="code-block language-objc">
#import &lt;Cocoa/Cocoa.h&gt;

int main( void )
{
    NSAutoreleasePool * pool;
    NSString * ( ^ myBlock )( int );

    pool    = [ [ NSAutoreleasePool alloc ] init ];
    myBlock = ^( int number )
    {
        return [ NSString stringWithFormat: @"Passed number: %i", number ];
    };

    NSLog( @"%@", myBlock() );

    [ pool release ];

    return EXIT_SUCCESS;
}
        </pre>
        <p>
            Such a program can be compiled with the following command (Terminal):
        </p>
        <pre class="code-block nohighlight">
gcc -Wall -framework Cocoa -o test test.m
        </pre>
        <p>
            It will generate an executable named «test», from the «test.m» source file.<br />
            To launch the executable:
        </p>
        <pre class="code-block nohighlight">
./test
        </pre>
        <p>
            The declaration of a block prototype can be ommited if the block is not assigned to a variable. For instance, if it's passed directly as a parameter.
        </p>
        <p>
            For instance:
        </p>
        <pre class="code-block language-objc">
someFunction( ^ NSString * ( void ) { return @"hello, world" } );
        </pre>
        <p>
            Note that in such a case, the return type must be declared. Here, it's a «NSString» object.
        </p>
        <a name="objc-argument"></a>
        <h4>Passing a block as a parameter</h4>
        <p>
            A block can of course be passed as an argument of a C function.<br />
            Here again, the syntax is a bit tricky at first sight:
        </p>
        <pre class="code-block language-objc">
void logBlock( NSString * ( ^ theBlock )( int ) )
{
    NSLog( @"Block returned: %@", theBlock() );
}
        </pre>
        <p>
            Of course, as Objective-C is a strongly typed language, a function taking a block as argument must also declare it's return type and the type of it's arguments, if any.
        </p>
        <p>
            Same thing for an objective-C method:
        </p>
        <pre class="code-block language-objc">
- ( void )logBlock: ( NSString * ( ^ )( int ) )theBlock;
        </pre>
        <a name="objc-closure"></a>
        <h4>Closure</h4>
        <p>
            The closure concept is also available in Objective-C, even if its behaviour is of course different than in interpreted languages.
        </p>
        <p>
            Let's see the following program:
        </p>
        <pre class="code-block language-objc">
#import &lt;Cocoa/Cocoa.h&gt;

void logBlock( int ( ^ theBlock )( void ) )
{
    NSLog( @"Closure var X: %i", theBlock() );
}

int main( void )
{
    NSAutoreleasePool * pool;
    int ( ^ myBlock )( void );
    int x;

    pool = [ [ NSAutoreleasePool alloc ] init ];
    x    = 42;

    myBlock = ^( void )
    {
        return x;
    };

    logBlock( myBlock );

    [ pool release ];

    return EXIT_SUCCESS;
}
        </pre>
        <p>
            The «main» function declares an integer, with 42 as value, and a block, returning that variable.<br />
            The block is then passed to the «logBlock» function, that will display its return value.
        </p>
        <p>
            Even in the execution context of the «logBlock» function, the block, declared in the «main» function, still has access to the «x» integer, and is able to return its value.
        </p>
        <p>
            Note that blocks also have access to global variable, even the static ones, if they are available in the block declaration context.
        </p>
        <p>
            Here comes a first difference. The variables available in a block by closure are typed as «const». It means their values can't be modified from inside the block.
        </p>
        <p>
            For instance, let's see what happen when our block tries to increment the value of «x»:
        </p>
        <pre class="code-block language-objc">
myBlock = ^( void )
{
    x++

    return x;
};
        </pre>
        <p>
            The compiler will produce an error, as the «x» variable is only available to read from inside the block.
        </p>
        <p>
            To allow a block to modify a variable, it has to be declared with the «__block» keyword.<br />
            The previous code is valid if we declare the «x» variable in the following way:
        </p>
        <pre class="code-block language-objc">
__block int x;
        </pre>
        <a name="objc-memory"></a>
        <h4>Memory management</h4>
        <p>
            At the C level, a block is a structure, which can be copied or destroyed.<br />
            Two C functions are available for that use: «Block_copy()» and «Block_destroy()».<br />
            In Objective-C, a block can also receive the «retain», «release» and «copie» messages, as a normal object.
        </p>
        <p>
            It's extremely important if a block must be stored for a later use (for instance, stored in a class instance variable).
        </p>
        <p>
            In such a case, the block must be retained, in order to avoid a segmentation fault.
        </p>
        <a name="objc-example"></a>
        <h4>Examples</h4>
        <p>
            Blocks can be used in a lot of different contexts, to keep the code simple and to reduce the number of declared functions.
        </p>
        <p>
            Here's an example:
        </p>
        <p>
            We are going to add to the «NSArray» class a static method (class method) that will generate an array by filtering the items of another array, by a callback.
        </p>
        <p>
            For the PHP programmers, it's the same as the «array_filter()» function.
        </p>
        <p>
            First, we need to declare a category for the «NSArray» class.<br />
            A category allows to add methods to existing classes.
        </p>
        <pre class="code-block language-objc">
@interface NSArray( BlockExample )

+ ( NSArray * )arrayByFilteringArray: ( NSArray * )source withCallback: ( BOOL ( ^ )( id ) )callback;

@end
        </pre>
        <p>
            Here, we are declaring a method returning a «NSArray» object, and taking as parameter another «NSArray» object, and a callback, as a block.
        </p>
        <p>
            The callback will be executed for each item of the array which is passed as parameter.<br />
            It will return a boolean value, in order to know if the current array item must be stored or not in the returned array.<br />
            The block takes as unique parameter an object, representing the current array item.
        </p>
        <p>
            Let's see the implementation of that method:
        </p>
        <pre class="code-block language-objc">
@implementation NSArray( BlockExample )

+ ( NSArray * )arrayByFilteringArray: ( NSArray * )source withCallback: ( BOOL ( ^ )( id ) )callback
{
    NSMutableArray * result;
    id               element;

    result = [ NSMutableArray arrayWithCapacity: [ source count ] ];

    for( element in source ) {

        if( callback( element ) == YES ) {
    
            [ result addObject: element ];
        }
    }

    return result;
}

@end
        </pre>
        <p>
            First, we create an array with a dynamic size («NSMutableArray»). It's initial capacity is the same as the number of items of the source array.
        </p>
        <p>
            Then, we iterate through each item of the source array, and we add the current item if the result of the callback is the boolean value «YES».
        </p>
        <p>
            Here's a complete example of a program using such a method.<br />
            We are using the callback to create an array that contains only the items of type «NSString» from the source array:
        </p>
        <pre class="code-block language-objc">
#import &lt;Cocoa/Cocoa.h&gt;

@interface NSArray( BlockExample )

+ ( NSArray * )arrayByFilteringArray: ( NSArray * )source withCallback: ( BOOL ( ^ )( id ) )callback;

@end

@implementation NSArray( BlockExample )

+ ( NSArray * )arrayByFilteringArray: ( NSArray * )source withCallback: ( BOOL ( ^ )( id ) )callback
{
    NSMutableArray * result;
    id               element;

    result = [ NSMutableArray arrayWithCapacity: [ source count ] ];

    for( element in source ) {

        if( callback( element ) == YES ) {
    
            [ result addObject: element ];
        }
    }

    return result;
}

@end

int main( void )
{
    NSAutoreleasePool * pool;
    NSArray           * array1;
    NSArray           * array2;

    pool   = [ [ NSAutoreleasePool alloc ] init ];
    array1 = [ NSArray arrayWithObjects: @"hello, world", [ NSDate date ], @"hello, universe", nil ];
    array2 = [ NSArray
                    arrayByFilteringArray: array1
                    withCallback:          ^ BOOL ( id element )
                    {
                        return [ element isKindOfClass: [ NSString class ] ];
                    }
             ];

    NSLog( @"%@", array2 );

    [ pool release ];

    return EXIT_SUCCESS;
}
        </pre>
    </div>
    <div class="col-md-2 resume-menu">
        <div data-spy="affix" data-offset-top="0" data-offset-bottom="0" class="resume-menu-affix">
            <ul class="hidden-xs">
                <li>
                    <a href="#about" title="Go to this section"><strong>About</strong></a>
                    <ul>
                        <li>
                            <a href="#about-lambda" title="Go to this section">Anonymous functions</a>
                        </li>
                        <li>
                            <a href="#about-closure" title="Go to this section">Closure</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#objc" title="Go to this section"><strong>Objective-C implementation</strong></a>
                    <ul>
                        <li>
                            <a href="#objc-argument" title="Go to this section">Passing a block as an argument</a>
                        </li>
                        <li>
                            <a href="#objc-closure" title="Go to this section">Closure</a>
                        </li>
                        <li>
                            <a href="#objc-memory" title="Go to this section">Memory management</a>
                        </li>
                        <li>
                            <a href="#objc-example" title="Go to this section">Examples</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>

