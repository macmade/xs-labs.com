<div>
    <img src="/uploads/image/xeos-software-license/icon-circle.png" alt="XEOS Software License" width="140" height="140" class="pull-right" />
</div>
<p>
    The XEOS Software License is an OpenSource/Free software license attempting to address issues found in other licenses of the same kind.
</p>
<p>
    It was originally created for the XEOS operating system, and its foundation libraries like XSFoundation.
</p>
<p>
    Basically, it's a kind of mix between the BSD(s), MIT and BOOST software licenses, attempting to bring the good parts of each one into a single license, with a few additional clauses (read the «Goals» section for more details).
</p>
<p>
It has not been yet recognised as a valid software license by the FSF, as I wasn't able to get in touch.<br />
But I hope it will be in a short time.
</p>
<h2>Goals</h2>
<p>
    The main goals of the XEOS Software License are:
</p>
<ol>
    <li><strong>To impose minimal restrictions on redistribution.</strong></li>
    <li><strong>To allow use in proprietary software.</strong></li>
    <li><strong>To differentiate library and executables derivative works.</strong></li>
    <li><strong>To prevent issues with GNU/GPL licensed software/libraries.</strong></li>
</ol>
<h3>1 - Minimal restrictions on redistribution</h3>
<p>
    As the BSD(s) and MIT software licenses, the XEOS Software License aims to be simple, straightforward, and unrestrictive.
</p>
<p>
    I personally don't want to live in a world where a software engineer has to be a lawyer in order to work.<br />
    A software license should be simple to use and to read. Seriously, who actually read the full terms of GNU/GPL, honestly?.
</p>
<p>
    It should also minimise the side effects on redistribution/derivation with third-party code, licensed with different terms (read «4 - GNU/GPL issues» for more details).
</p>
<h3>2 - Proprietary software</h3>
<p>
    I would love living in a world with unicorns and rainbows everywhere, but lets be serious - Proprietary software is not evil.
</p>
<p>
    When thinking about proprietary software, people often think about big companies, like Microsoft, Adobe or Apple.<br />
    But creating software is not about fighting against large corporations. It's not about being a hippie with a long beard and fighting against the establishment, the evil companies run by evil billionaires, the dark side of the computers, or whatsoever.
</p>
<p>
    The world is also made of small companies and indy software developers, trying to create great products.<br />
    Quality takes time, and time has a price.
</p>
<p>
    If you're looking for fairness, do you really think it's fair to tell an indy developer (maybe a guy just like you, who doesn't make millions every hours) that he cannot sell the software he wrote for a year, just because he used 10 lines of GNU/GPL licensed code in its code base?
</p>
<p>
    I think it's not. Small companies and indy developers needs to make money in order to continue their work.<br />
    Just like everyone, they need money to eat.
</p>
<p>
    So code licensed under the terms of the XEOS Software License can be used inside proprietary software.
</p>
<h3>3 - Library vs. Executables</h3>
<p>
    Source redistribution should retain all copyright notices, that's right.<br />
    But what about binary redistribution?
</p>
<p>
    For an executable, if you used free software, I think it's fair to recognise it, and to reproduce copyright notices in your documentation, about window, or whatsoever.  
</p>
<p>
    But I personally don't think it should be the same for a library.<br />
    Both can be considered as binary redistributions, but in fact it's a whole different story.
</p>
<p>
    When developing a software, you may end up using tons of libraries. Some provided by your OS, some used by other libraries, etc.<br />
    So at the end it may be very hard to have to full picture of all the libraries you are actually using, and which copyrights you may infringe.
</p>
<p>
    In order to address this, the XEOS Software License permits binary redistribution without attribution, as long as the derivative work is a library.
</p>
<h3>4 - GNU/GPL issues</h3>
<p>
    So what's wrong with the GNU/GPL?
</p>
<p>
    Initially nothing. It was an absolute necessity, as it brought back the OpenSource spirit in the computer engineering world.<br />
    And it did a great job, as it allowed people to take consciousness of the OpenSource/Free software necessity.
</p>
<p>
    But it has become a complete nightmare, especially with version 3.
</p>
<p>
    The main issue with the GNU/GPL is its virality.<br />
    Developers using code licensed under the terms of the GNU/GPL no longer have the choice to use a different OpenSource license; they are forced to also use the GNU/GPL for their whole project.
</p>
<p>
    This is specially catastrophic for library development, because the GNU/GPL license terms even disallow dynamic linking with a GNU/GPL library from a non GNU/GPL project.
</p>
<p>
    This has become out of control, and this is no more freedom.<br />
    How can you speak about freedom when you are trying to force people to use your very own concept of freedom?
</p>
<p>
    The OpenSource world needs to keep its original roots, and it should not become a casualty of war, in the war started by the GNU philosophers and integrists against the commercial world.
</p>
<p>
    In order to prevent issues with the GNU/GPL, the XEOS Software license explicitely disallow any form of linking with code licensed under the terms of the GNU/GPL.
</p>
