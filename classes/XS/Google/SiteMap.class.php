<?php

################################################################################
# Copyright (c) 2010, Jean-David Gadina - XS-Labs                              #
# All rights reserved.                                                         #
#                                                                              #
# Redistribution and use in source and binary forms, with or without           #
# modification, are permitted provided that the following conditions are met:  #
#                                                                              #
#  -   Redistributions of source code must retain the above copyright notice,  #
#      this list of conditions and the following disclaimer.                   #
#  -   Redistributions in binary form must reproduce the above copyright       #
#      notice, this list of conditions and the following disclaimer in the     #
#      documentation and/or other materials provided with the distribution.    #
#  -   Neither the name of 'Jean-David Gadina' nor the names of its            #
#      contributors may be used to endorse or promote products derived from    #
#      this software without specific prior written permission.                #
#                                                                              #
# THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"  #
# AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE    #
# IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE   #
# ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE    #
# LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR          #
# CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF         #
# SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS     #
# INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN      #
# CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)      #
# ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE   #
# POSSIBILITY OF SUCH DAMAGE.                                                  #
################################################################################

# $Id$

namespace XS\Google;

class SiteMap
{
    protected $_availableLanguages = array();
    protected $_baseUrl            = '';
    protected $_dateFormat         = 'Y-m-d';
    protected $_xmlns              = 'http://www.google.com/schemas/sitemap/0.84';
    protected $_xmlVersion         = '1.0';
    protected $_xmlCharset         = 'utf-8';
    
    public function __construct()
    {
        $protocol                  = ( isset( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] ) ? 'https://' : 'http://';
        $this->_baseUrl            = $protocol . $_SERVER[ 'HTTP_HOST' ] . '/';
        $menuFiles                 = __ROOTDIR__
                                   . DIRECTORY_SEPARATOR
                                   . 'include'
                                   . DIRECTORY_SEPARATOR
                                   . 'menu.*.xml';
        $this->_availableLanguages = glob( $menuFiles );
    }
    
    public function __toString()
    {
        $sitemap        = new \SimpleXMLElement( '<urlset xmlns="' .  $this->_xmlns . '"></urlset>' );
        $url            = $sitemap->addChild( 'url' );
        $url[ 'index' ] = 0;
        $url->loc       = $this->_baseUrl;
        $url->lastmod   = $this->_getModificationDate( ( string )$url->loc );
        
        foreach( $this->_availableLanguages as $key => $value )
        {
            $menu     = simplexml_load_file( $value );
            $iterator = new \SimpleXMLIterator( file_get_contents( $value ) );
            
            $index          = count( $sitemap );
            $url            = $sitemap->addChild( 'url' );
            $url[ 'index' ] = $index;
            $url->loc       = $this->_baseUrl . $menu[ 'lang' ] . '/';
            $url->lastmod   = $this->_getModificationDate( ( string )$url->loc );
            
            $this->_getLinks( $sitemap, $iterator, $this->_baseUrl . $menu[ 'lang' ] . '/' );
        }
        
        $dom               = new \DOMDocument( $this->_xmlVersion, $this->_xmlCharset );
        $dom->formatOutput = true;
        $domNode           = dom_import_simplexml($sitemap );
        $domNode           = $dom->importNode( $domNode, true );
        $domNode           = $dom->appendChild( $domNode );
        
        return $dom->saveXML();
    }
    
    protected function _getLinks( \SimpleXMLElement $sitemap, \SimpleXMLIterator $iterator, $path )
    {
        foreach( $iterator as $key => $value )
        {
            if( isset( $value[ 'preview' ] ) )
            {
                continue;
            }
            
            $index          = count( $sitemap );
            $pageUrl        = $path . $key . '/';
            $url            = $sitemap->addChild( 'url' );
            $url[ 'index' ] = $index;
            $url->loc       = $pageUrl;
            $url->lastmod   = $this->_getModificationDate( ( string )$url->loc );
            
            if( isset( $value->sub ) )
            {
                $this->_getLinks( $sitemap, new \SimpleXMLIterator( $value->sub->asXML() ), $pageUrl );
            }
        }
    }
    
    protected function _getModificationDate( $url )
    {
        $path = __ROOTDIR__
              . DIRECTORY_SEPARATOR
              . str_replace( '/', DIRECTORY_SEPARATOR, substr( $url, strlen( $this->_baseUrl ) ) )
              . 'index.php';
        
        if( file_exists( $path ) && function_exists( 'filemtime' ) )
        {
            return date( $this->_dateFormat, filemtime( $path ) );
        }
        
        return date( $this->_dateFormat, time() );
    }
}
