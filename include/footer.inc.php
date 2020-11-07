        </div>
    </div>
    <div class="footer">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3>Latest blog posts</h3>
                        </div>
                        <div class="panel-body">
                            <?php print ( string )\XS\Blog::getInstance()->getLatestPosts(); ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3>Latest tweets</h3>
                        </div>
                        <div class="panel-body">
                            <?php
                            print ( string )new \XS\Twitter\Feed
                            (
                                'SGBDulUJtDMj4YqhI0Vi12vEJ',
                                'vRY4qZmzz7Nqibs0wOIUiiYj0ozIXRAJdtlRWP5Zyr4ugK28oB',
                                '66378123-hPIVzrmv4eSNu0Utc6t3W80TzYznSS6DeSRKzvF9P',
                                'JtNt0MQwnvTeUUo9HTNNtw17CWPvqyA9F1kN83ZWNSoNg',
                                'macmade',
                                4
                            ); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    XS-Labs &copy; <?php print date( 'Y', time() ); ?> - All Rights Reserved - All Wrongs Reserved
                </div>
                <div class="col-sm-6">
                    <ul class="list-inline pull-right">
                        <li><?php print \XS\Menu::getInstance()->getPageLink( '/sitemap/' ); ?></li>
                        <li><?php print \XS\Menu::getInstance()->getPageLink( '/legal/privacy/' ); ?></li>
                        <li><?php print \XS\Menu::getInstance()->getPageLink( '/legal/credits/' ); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js" type="text/javascript"></script>
    <script src="/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="/js/imagelightbox.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        // <![CDATA[
        
        $( document ).ready
        (
            function()
            {
                $( 'a.lightbox' ).imageLightbox();
            }
        );
        
        // ]]>
    </script>
    <script src="/js/highlight.pack.js" type="text/javascript"></script>
    <script type="text/javascript">
        // <![CDATA[
        
        $( document ).ready
        (
            function()
            {
                $( 'div.code-block' ).each
                (
                    function( i, block )
                    {
                        hljs.highlightBlock( block );
                    }
                );
                $( 'pre.code-block' ).each
                (
                    function( i, block )
                    {
                        hljs.highlightBlock( block );
                    }
                );
            }
        );
        
        // ]]>
    </script>
</body>
</html>
