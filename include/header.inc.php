<?php
if( isset( $_GET[ 'download_file' ] ) )
{
    $download = __ROOTDIR__
              . DIRECTORY_SEPARATOR
              . 'downloads'
              . DIRECTORY_SEPARATOR
              . $_GET[ 'download_file' ];

    if( file_exists( $download ) )
    {
        header( 'Pragma: public' );
        header( 'Content-type: ' );
        header(' Expires: 0' );
        header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
        header( 'Content-Type: application/octet-stream' );
        header( 'Content-Disposition: attachment; filename="' .  basename( $download ) . '"' );
        header( 'Content-Transfer-Encoding: binary' );
        header( 'Content-Length: '. filesize( $download ) );
        readfile( $download );
        exit();
    }
}
?>
<?php
if( isset( $_SERVER[ 'HTTP_USER_AGENT' ] ) && ( strpos( $_SERVER[ 'HTTP_USER_AGENT' ], 'MSIE' ) !== false ) )
{
    header( 'X-UA-Compatible: IE=edge' );
}
?>
<!DOCTYPE html>
<html lang="<?php print \XS\Menu::getInstance()->getLanguage(); ?>">
<head>
    <!--

    ##################################################
    #                                                #
    #       Blood Sweat & Code (& Rock'N'Roll)       #
    #      Thanx for looking at the source code      #
    #                                                #
    #                 XS-Labs © 2013                 #
    #                                                #
    ##################################################

    -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php print \XS\Menu::getInstance()->getRootLine(); ?></title>
    <link rel="stylesheet" href="/css/styles.php" type="text/css" media="all" />
    <meta name="author" content="XS-Labs" />
    <meta name="description" content="<?php print \XS\Menu::getInstance()->getDescription(); ?>" />
    <meta name="keywords" content="<?php print \XS\Menu::getInstance()->getKeywords(); ?>" />
    <meta name="rating" content="General" />
    <meta name="robots" content="all" />
    <meta name="generator" content="BBEdit 10.5" />
    <!--[if IE]><link rel="shortcut icon" href="/favicon.ico"><![endif]-->
    <link rel="icon" href="/favicon.png">
    <link rel="apple-touch-icon-precomposed" href="/favicon-apple-touch.png">
    <link href="/feed/atom.php" type="application/atom+xml" rel="alternate" title="XS-Labs ATOM Feed" />
    <link href="/feed/rss.php" type="application/rss+xml" rel="alternate" title="XS-Labs RSS Feed" />
    <script type="text/javascript">
        // <![CDATA[
        
        if( ( ( window.devicePixelRatio === undefined ) ? 1 : window.devicePixelRatio ) > 1 )
        {
            document.cookie = 'X_XSLABS_CLIENT_IS_RETINA=1;path=/';
        }
        
        // ]]>
    </script>
    <?php if( $_SERVER[ 'SERVER_NAME' ] !== 'xs-labs.localhost' && $_SERVER[ 'SERVER_NAME' ] !== 'dev.xs-labs.com' ): ?>
    <script type="text/javascript">
        // <![CDATA[
        
        (
            function( i, s, o, g, r, a, m )
            {
                i[ 'GoogleAnalyticsObject' ] = r;
                i[ r ]                       = i[ r ] || function()
                {
                    ( i[ r ].q = i[ r ].q || [] ).push( arguments )
                },
                i[ r ].l = 1 * new Date();
                a        = s.createElement( o ),
                m        = s.getElementsByTagName( o )[ 0 ];
                a.async  = 1;
                a.src    = g;
            
                m.parentNode.insertBefore( a, m )
            }
        )
        (
            window,
            document,
            'script',
            '//www.google-analytics.com/analytics.js',
            'ga'
        );
        
        ga( 'create', 'UA-51035898-1', 'xs-labs.com' );
        ga( 'require', 'displayfeatures' );
        ga( 'send', 'pageview' );
        
        // ]]>
    </script>
    <?php
        foreach( \XS\Layout::getInstance()->getHeaderParts() as $LAYOUT_HEADER )
        {
            print $LAYOUT_HEADER;
        }
        
        unset( $LAYOUT_HEADER );
    ?>
    <?php endif; ?>
</head>
<body>
    <div class="xs-page-header">
        <div class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/">
                        <img src="/css/image/xs-logo.png" width="120" height="40" alt="XS-Labs" />
                    </a>
                </div>
                <div class="navbar-collapse collapse" id="navbar">
                    <ul class="nav navbar-nav">
                    <?php
                        $MENU = \XS\Menu::getInstance()->getMenuLevel( 1 );
                        
                        foreach( $MENU as $ITEM )
                        {
                            print $ITEM . chr( 10 );
                        }
                        
                        unset( $MENU );
                    ?>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="https://twitter.com/macmade"><img src="/css/image/xs-menu-bar-item-twitter.png" alt="Twitter" width="30" height="30" /></a></li>
                        <li><a href="https://github.com/macmade"><img src="/css/image/xs-menu-bar-item-github.png" alt="GitHub" width="30" height="30" /></a></li>
                        <li><a href="http://stackoverflow.com/users/182676/macmade"><img src="/css/image/xs-menu-bar-item-stackoverflow.png" alt="StackOverflow" width="30" height="30" /></a></li>
                        <li><a href="http://www.linkedin.com/in/macmade/"><img src="/css/image/xs-menu-bar-item-linkedin.png" alt="LinkedIn" width="30" height="30" /></a></li>
                    </ul>
                </div>
            </div>
        </div>    
        <div class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators"></ol>
            <div class="carousel-inner">
                <div class="item active">
                    <?php print \XS\Menu::getInstance()->getPageBanner(); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <?php
            print \XS\Menu::getInstance()->getRootlineMenu() . chr( 10 );
        ?>
        <?php
            $MENU       = \XS\Menu::getInstance()->getMenuLevel( 3 );
            $SUB        = \XS\Menu::getInstance()->getPageSubtitle();
            $HAS_SUB    = strlen( $SUB ) > 0;
            $HAS_MENU   = ( $MENU != NULL && $MENU->isEmpty() == false );
            
            if( $HAS_SUB || $HAS_MENU )
            {
        ?>
        <div class="xs-page-sub-header">
            <?php
                if( $HAS_SUB && $HAS_MENU )
                {
            ?>
            <div class="row">
                <div class="col-xs-6">
            <?php
                }
                
                print \XS\Menu::getInstance()->getPageSubtitleHeader() . chr( 10 );
                
                if( $HAS_SUB && $HAS_MENU )
                {
            ?>
                </div>
                <div class="col-xs-6">
            <?php
                }
                
                if( $HAS_MENU )
                {
            ?>
                    <div role="navigation">
                        <ul class="pull-right">
                        <?php
    
                            foreach( $MENU as $ITEM )
                            {
                                print $ITEM . chr( 10 );
                            }
                        ?>
                        </ul>
                    </div>
            <?php
                }
                
                if( $HAS_SUB && $HAS_MENU )
                {
            ?>
                </div>
            </div>
            <?php
                }
            ?>
        </div>
        <?php
            }
            
            unset( $ITEM );
            unset( $MENU );
            unset( $SUB );
            unset( $HAS_MENU );
            unset( $HAS_SUB );
        ?>
        <div class="xs-content">
