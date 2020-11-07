<h2>Reference counting in ANSI-C</h2>
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
<h3>About</h3>
<p>
    Memory management can be a hard task when coding a C program.<br />
    Some higher level programming languages provide other ways to manage memory.<br />
    Main variants are garbage collection, and reference counting.<br />
    This article will teach you how to implement a reference counting memory management system in C.
</p>
<p>
    Personally, as a C and Objective-C developer, I love the reference counting way.<br />
    It implies the notion of ownership on objects.
</p>
<h3>Objective-C example</h3>
<p>
    For instance, in Objective-C, when you creates an object using the alloc, or copy methods, you own the object. It means you'll have to release your object, so the memory can be reclaimed.
</p>
<p>
    Objects can also be retained. In such a case they must be released too.
</p>
<p>
    Objects get by convenience methods are not owned by the caller, so there's no need to release them, as it will be done by someone else.
</p>
<p>
    For instance, in Objective-C:
</p>
<pre class="code-block language-objc">
NSArray * object1 = [ NSArray array ];
NSArray * object2 = [ [ NSArray alloc ] init ];
NSArray * object3 = [ [ [ NSArray array ] retain ] retain ];
</pre>
<p>
    Here, the object2 variable will need to be released, as we allocated it explicitly.<br />
    The object3 variable will need to be released twice, since we retained it twice.
</p>
<pre class="code-block language-objc">
[ object2 release ];
[ [ object3 release ] release ];
</pre>
<h3>C implementation</h3>
<p>
    As a C coder, I've implemented this with ANSi-C.<br />
    Here are some explanations.
</p>
<p>
    First of all, we are going to define a structure for our memory records.<br />
    The structure will look like this:
</p>
<pre class="code-block language-c">
typedef struct
{
    unsigned int retainCount
    void       * data;
}
MemoryObject;
</pre>
<p>
    Here, we are storing the retain count of the memory object. A retain will increment it, and a release decrement it. When it reaches 0, the object will be freed.
</p>
<p>
    We'll also need a custom allocation function:
</p>
<pre class="code-block language-c">
void * Alloc( size_t size )
{
    MemoryObject * o;
    
    o = ( MemoryObject * )calloc( sizeof( MemoryObject ) + size, 1 );
</pre>
<p>
    Here, allocate space for our memory object structure, plus the user requested size.<br />
    We are not going to return the memory object, so we need some calculation here.
</p>
<p>
    First of all, let's declare a char pointer, that will point to our allocated memory object structure:
</p>
<pre class="code-block language-c">
char * ptr = ( char * )o;
</pre>
<p>
    Then, we can get the location of the user defined data by adding the size of the memory object structure:
</p>
<pre class="code-block language-c">
ptr += sizeof( MemoryObject );
</pre>
<p>
    Then, we can set our data pointer, et set the retain count to 1.
</p>
<pre class="code-block language-c">
o->data        = ptr;
o->retainCount = 1;
</pre>
<p>
    Now we'll return to pointer to the user data, so it doesn't have to know about our memory object structure.
</p>
<pre class="code-block language-c">
return ptr;
</pre>
<p>
    Here's the full function:
</p>
<pre class="code-block language-c">
void * Alloc( size_t size )
{
    MemoryObject * o;
    char         * ptr;
    
    o              = ( MemoryObject * )calloc( sizeof( MemoryObject ) + size, 1 );
    ptr            = ( char * )o;
    ptr           += sizeof( MemoryObject );
    o->retainCount = 1;
    o->data        = ptr;
    
    return ( void * )ptr;
}
</pre>
<p>
    This way, we return the user defined allocated size, and we are hiding our structure before that data.
</p>
<p>
    To retrieve our data, we simply need to subtract the size of the MemoryObject structure.
</p>
<p>
    For example, here's the Retain function:
</p>
<pre class="code-block language-c">
void Retain( void * ptr )
{
    MemoryObject * o;
    char         * cptr;
    
    cptr  = ( char * )ptr;
    cptr -= sizeof( MemoryObject );
    o     = ( MemoryObject * )cptr;
    
    o->retainCount++:
}
</pre>
<p>
    We are here retrieving our MemoryObject structure, by subtracting the size of it to the user pointer. Once done, we can increase the retain count by one.
</p>
<p>
    Same thing is done for the Release function:
</p>
<pre class="code-block language-c">
void Release( void * ptr )
{
    MemoryObject * o;
    char         * cptr;
    
    cptr  = ( char * )ptr;
    cptr -= sizeof( MemoryObject );
    o     = ( MemoryObject * )cptr;
    
    o->retainCount--:
    
    if( o->retainCount == 0 )
    {
        free( o );
    }
}
</pre>
<p>
    When the retain count reaches zero, we can free the object.
</p>
<p>
    That's all. We now have a reference counting memory management in C.<br />
    All you have to do is call Alloc to create an object, Retain if you need to, and Release when you don't need the object anymore.<br />
    It may have been retained by another function, but then you don't have to care if it will be freed or not, as you don't own the object anymore.
</p>
