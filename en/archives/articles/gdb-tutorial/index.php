<h2>GDB basic tutorial</h2>
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
    I have to admit I always felt stupid, while building XCode projects, when the GDB window comes out, or when it display a message like: «set a breakpoint in malloc to debug».<br />
    So I decided to learn a few things about GDB. This tutorial will explain you some of the basics.
</p>
<h3>Example program &amp; Compilation</h3>
<p>
    Before using GCC, we need a sample program to work with. We'll also need to add a specific compiler flag when compiling.<br />
    Let's begin with a simple C program:
</p>
<pre class="code-block language-c">
#import &lt;stdlib.h&gt;

void do_stuff( void );

unsigned long x;

int main( void )
{
    x = 10;
    
    do_stuff();
    
    return 0;
}

void do_stuff( void )
{
    char * s;
    
    s = ( char * )x;
    
    if( s != NULL )
    {
        s[ 0 ] = 0;
    }
}
</pre>
<p>
    Name the file 'gdb_test.c', then compile and run the code with the following command:
</p>
<pre class="code-block nohighlight">
gcc -Wall -o gdb_test gdb_test.c && ./gdb_test
</pre>
<p>
    No surprise, the program will end with a segmentation fault (EXC_BAD_ACCESS - SIGSEGV).
</p>
<p>
    Now compile the same file again, and add the '<strong>-g</strong>' parameter to the GCC invocation:
</p>
<pre class="code-block nohighlight">
gcc -Wall -g -o gdb_test gdb_test.c
</pre>
<p>
    That will tell GCC to generate the debug symbols file. It will be called <strong>'gdb_test.dSYM'</strong>.<br />
    Such a file contains informations about each symbol of the executable (functions, variables, line numbers, etc). Now that we have that file, we are ready to use GDB.
</p>
<h3>Using GDB</h3>
<p>
    Simply type '<strong>gdb</strong>' to enter a new GDB session. We'll the load our executable using the <strong>file</strong> command:
</p>
<pre class="code-block nohighlight">
GNU gdb 6.3.50-20050815 (Apple version gdb-1518) (Thu Jan 27 08:34:47 UTC 2011)
Copyright 2004 Free Software Foundation, Inc.
GDB is free software, covered by the GNU General Public License, and you are
welcome to change it and/or distribute copies of it under certain conditions.
Type "show copying" to see the conditions.
There is absolutely no warranty for GDB.  Type "show warranty" for details.
This GDB was configured as "x86_64-apple-darwin".
(gdb) file gdb_test
</pre>
<p>
    The executable is now loaded. We can run it with the '<strong>run</strong>' command:
</p>
<pre class="code-block nohighlight">
(gdb) run
Starting program: /Users/macmade/Desktop/gdb_test 
Reading symbols for shared libraries +. done

Program received signal EXC_BAD_ACCESS, Could not access memory.
Reason: KERN_INVALID_ADDRESS at address: 0x000000000000000a
0x0000000100000f0f in do_stuff () at gdb_test.c:24
24	        s[ 0 ] = 0;
</pre>
<p>
    We can see GDB caught the segmentation fault, and stopped the program's execution. It even display the line where the segmentation fault occurs. Very useful!
</p>
<p>
    We can also ask GDB for a backtrace, with the '<strong>bt</strong>' command:
</p>
<pre class="code-block nohighlight">
(gdb) bt
#0  0x0000000100000f0f in do_stuff () at gdb_test.c:24
#1  0x0000000100000eeb in main () at gdb_test.c:11
</pre>
<h3>Breakpoints</h3>
<p>
    We can also set breakpoints with GDB. A breakpoint can be a function's name, a specific line number, or a condition.<br />
    When GDB encounters a breakpoint, it will stop the program execution. The execution can the be continued with the <strong>'s'</strong> (step) or <strong>'n'</strong> (next) commands.
</p>
<p>
    So lets run our program again, and let's set a breakpoint in the <strong>do_stuff()</strong> function:
</p>
<pre class="code-block nohighlight">
(gdb) break do_stuff
Breakpoint 1 at 0x100000ef6: file gdb_test.c, line 20.
(gdb) run
The program being debugged has been started already.
Start it from the beginning? (y or n) y
Starting program: /Users/macmade/Desktop/gdb_test 

Breakpoint 1, do_stuff () at gdb_test.c:20
20	    s = ( char * )x;
(gdb) 
</pre>
<p>
    GDB will automatically stops the program's execution when we call the <strong>do_stuff()</strong> function.<br />
    Now we can inspect our program.
</p>
<p>
    We can start by asking the value of our '<strong>x</strong>' variable:
</p>
<pre class="code-block nohighlight">
(gdb) p x
</pre>
<p>
    That will print the value of the '<strong>x</strong>' variable:
</p>
<pre class="code-block nohighlight">
$1 = 10
</pre>
<p>
    We can now modify that variable, so it equals '0' (NULL):
</p>
<pre class="code-block nohighlight">
(gdb) p x=0
</pre>
<p>
    Now we've fixed the problem, and we can continue the program's execution, by stepping multiple times:
</p>
<pre class="code-block nohighlight">
(gdb) s
</pre>
<p>
    Till GDB prints:
</p>
<pre class="code-block nohighlight">
Program exited normally.
</pre>
