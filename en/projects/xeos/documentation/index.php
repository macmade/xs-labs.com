<h3>Requirements</h3>
<p>
    In order to be built, XEOS needs a custom version of the <a href="http://llvm.org">LLVM</a> compiler.
</p>
<p>
    The reason for this is that the compilers available by default on standard systems may only compile executables of a specific format.<br />
    For instance, you won't be able to build ELF on Mac OS X, nor Mach-O executables on Linux, using the default compilers.
</p>
<p>
    Additionally, the default compilers will usually automatically link with the standard C library that is available on the running system.<br />
    As XEOS is itself an operating system, it won't be able to use such a library.
</p>
<p>
    So the first step, in order to compile XEOS, is to compile the compiler that will be able to compile it...<br />
    And of course, a compiler needs to be available on your system in order to compile a version of LLVM.<br />
    It may seems funny, but don't worry: everything has been prepared for you.
</p>
<h3>Building the compiler</h3>
<p>
    From a terminal window, you need to cd to XEOS trunk's directory.<br />
    Then, type the following command:
</p>
<pre class="code-block nohighlight">
make toolchain
</pre>
<p>
    It will download the LLVM sources and build everything that's needed to compile XEOS.<br />
    Everything will be installed in the «/usr/local/xeos/» directory, so you can easily clean everything up when needed.
</p>
<p>
    The build process may take some time. But once it's done, you are ready to compile and use XEOS.
</p>
<h3>Compiling</h3>
<p>
    Compiling XEOS is a very simple task.<br />
    From the trunk's directory, simple type the following command:
</p>

<pre class="code-block nohighlight">
make
</pre>
<p>
    It will generate a FAT-12 floppy disk image containing the full OS, that you can run on any x86 or x86_64 machine or emulator.
</p>
<h3>Running XEOS</h3>
<p>
    XEOS can be run with emulators, like <a href="https://www.virtualbox.org">VirtualBox</a>.
</p>
