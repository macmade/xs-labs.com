<h2>Using boolean data-types with ANSI-C</h2>
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
    Boolean data types are certainly the most often used data-type in any programming language.<br />
    They are the root of any programming logic.<br />
    Nowadays, few people remember that the boolean data type wasn't defined with the ANSI (C89) C programming language.
</p>
<p>
    It was added as part of the ISO-C99 standard, with the «stdbool.h» header file.
</p>
<p>
    Before this, it was up to each programmer to define its own boolean type, usually an enum, like the following one:
</p>
<pre class="code-block language-c">
typedef enum { false = 0, true  = 1 } bool;
</pre>
<p>
    Of course, unless using prefixes, such declarations may cause many problems, especially when using libraries, in which each programmer defined a boolean datatype.
</p>
<p>
    The ISO-C99 specification defined a «bool» datatype, defined in the «stdbool.h» header file.<br />
    That's great, but how can we be sure that we are coding for C99, what about code portability with old systems?
</p>
<p>
    The best way to ensure backward compatibility is to declare the boolean data-type exactly the same way C99 does.<br />
    A macro, named «__bool_true_false_are_defined» is specified, so you can know if the boolean data-type is actually declared and available.<br />
    No surprise, the «true» value must be defined to 1, and «false» to 0.
</p>
<p>
    In C99, the «bool» data-type must expand to «_Bool». If it's not defined, you can rely on on other data-type, like «int» or «char».
</p>
<p>
    The final declaration may look like this, to ensure a maximum portability and compatibility:
</p>
<pre class="code-block language-c">
#ifndef __bool_true_false_are_defined
    #ifdef _Bool
        #define bool                        _Bool
    #else
        #define bool                        int
    #endif
    #define true                            1
    #define false                           0
    #define __bool_true_false_are_defined   1
#endif
</pre>
