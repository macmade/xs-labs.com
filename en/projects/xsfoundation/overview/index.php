<div>
    <img src="/uploads/image/xsfoundation/icon-circle.png" alt="XSFoundation" width="140" height="140" class="pull-right" />
</div>
<p>
    The XEOS C Foundation library provides the base for object-oriented C style coding, reference counting memory management with auto-release capabilities, reflection, runtime environment, polymorphism, exceptions, and basic objects.<br />
    It's purpose is to be integrated in the XEOS Operating System, once its C standard library will be complete.
</p>
<p>
    For now, it's just a standalone project, that should compile on every OS with a decent C compiler.
</p>
<div class="clearer"></div>
<h3>About XEOS</h3>
<p>
    <?php print \XS\MENU::getInstance()->getPageLink( "/projects/xeos/", "XEOS" ); ?> is an experimental 32/64 bits Operating System for x86 platforms, written from scratch in Assembly and C, including a C99 Standard Library, and aiming at POSIX/SUS2 compatibility.
</p>
<h3>Supported OS</h3>
<p>
    XSFoundation can be used on POSIX compliant systems (Mac OS X, Unix, Linux) as well as on Windows.
</p>
<h2>Highlights</h2>

<div class="row">
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="project-icons project-icons-chest-of-drawers">
                    Object oriented
                </h3>
            </div>
            <div class="panel-body">
                <p>
                    While pure C code, XSFoundation is an object-oriented library.<br />
                    It provides a complete runtime to manage classes and instances, providing many core classes to ease development in C, and allowing developers to create their own classes.
                 </p>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="project-icons project-icons-ram">
                    Reference counting
                </h3>
            </div>
            <div class="panel-body">
                <p>
                    XSFoundation manages memory and resources using reference counting, allowing easier memory allocation.<br />
                    It also provides auto-release capabilities, to handle automatically the release of allocated objects.
                 </p>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="project-icons project-icons-chalkboard">
                    Reflection
                </h3>
            </div>
            <div class="panel-body">
                <p>
                    XSFoundation provides basic reflection for classes and objects, allowing runtime introspection of code components.
                 </p>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="project-icons project-icons-ui-debug">
                    Debugger
                </h3>
            </div>
            <div class="panel-body">
                <p>
                    XSFoundation includes an integrated debugger, helping developers to easily spot runtime faults, like standard crashes, memory leaks, buffer overflows, etc.
                 </p>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="project-icons project-icons-os-mac-os-x">
                    Cross-platform
                </h3>
            </div>
            <div class="panel-body">
                <p>
                    While the purpose of XSFoundation is to be used in the <?php print \XS\MENU::getInstance()->getPageLink( "/projects/xeos/", "XEOS Operating System" ); ?>, it can be used as a standalone, cross-platform library. Mac OS X, Linux, Unix and Windows are currently fully supported.
                 </p>
            </div>
        </div>
    </div>
</div>
