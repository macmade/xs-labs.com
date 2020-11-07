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

namespace XS;

class SiteMap
{
    protected $_pathInfos     = array();
    protected $_lang          = '';
    protected $_baseUrl       = '';
    protected $_menuPath      = '';
    protected $_menu          = NULL;
    protected $_level         = 0;
    protected $_displayLevels = 0;
    
    public function __construct( $displayLevels = 0 )
    {
        $this->_menu          = \XS\Menu::getInstance();
        $protocol             = ( isset( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] ) ? 'https://' : 'http://';
        $this->_baseUrl       = $protocol . $_SERVER[ 'HTTP_HOST' ] . '/';
        $this->_lang          = $this->_menu->getLanguage();
        $this->_menuPath      = __ROOTDIR__ . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'menu.' . $this->_lang . '.xml';
        $this->_displayLevels = ( int )$displayLevels;
        
        if( !file_exists( $this->_menuPath ) )
        {
            $this->_lang     = 'en';
            $this->_menuPath = __ROOTDIR__ . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'menu.' . $this->_lang . '.xml';
        }
    }
    
    public function __toString()
    {
        $sitemap            = new \XS\XHTML\Tag( 'ul' );
        $sitemap[ 'class' ] = 'sitemap';
        $iterator           = new \SimpleXMLIterator( file_get_contents( $this->_menuPath ) );
        
        $this->_getLinks( $sitemap, $iterator, $this->_baseUrl . $this->_lang . '/' );
        
        return ( string )$sitemap;
    }
    
    protected function _getLinks( \XS\XHTML\Tag $sitemap, \SimpleXMLIterator $iterator, $path )
    {
        $this->_level++;
        
        if( $this->_displayLevels && $this->_level > $this->_displayLevels )
        {
            return;
        }
        
        foreach( $iterator as $key => $value )
        {
            if( isset( $value[ 'preview' ] ) )
            {
                continue;
            }
            
            if( isset( $value[ 'sitemap' ] ) && ( string )$value[ 'sitemap' ] === 'no' )
            {
                return;
            }
            
            $pageUrl         = $path . $key . '/';
            $item            = $sitemap->li;
            $item[ 'class' ] = 'sitemap-level-' . $this->_level;
            $div             = $item->div;
            $div[ 'class' ]  = 'sitemap-leaf';
            $link            = $div->a;
            $link[ 'title' ] =  htmlentities( $value->title );
            $link[ 'href' ]  = $pageUrl;
            
            $link->addTextData(  htmlentities( $value->title ) );
            
            if( isset( $value->sub ) )
            {
                if( $this->_displayLevels && $this->_level + 1 > $this->_displayLevels )
                {
                    continue;
                }
                
                $div[ 'class' ] = 'sitemap-branch';
                $ul             = new \XS\XHTML\Tag( 'ul' );
                
                $this->_getLinks( $ul, new \SimpleXMLIterator( $value->sub->asXML() ), $pageUrl );
                
                if( $ul->valid() )
                {
                    $div->addChildNode( $ul );
                }
                
                $this->_level--;
            }
        }
    }
}
