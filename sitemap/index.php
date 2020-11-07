<?php
    
    \XS\Layout::getInstance()->disableFooter();
    \XS\Layout::getInstance()->disableHeader();
    
    $SITEMAP = new \XS\Google\SiteMap();
    
    header( 'Content-type: text/xml' );
    
    print $SITEMAP;
