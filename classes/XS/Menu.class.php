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

final class Menu
{
    private static $_instance   = NULL;
    private $_menu              = NULL;
    private $_pathInfos         = array();
    private $_lang              = '';
    private $_currentPath       = '';
    private $_pageTitle         = '';
    private $_rootlineExtraName = '';
    private $_rootlineExtraUrl  = '';
    
    private function __construct()
    {
        $this->_pathInfos = explode( '/', $_SERVER[ 'REQUEST_URI' ] );
        
        array_shift( $this->_pathInfos );
        array_pop( $this->_pathInfos );
        
        $this->_lang = ( isset( $this->_pathInfos[ 0 ] ) ) ? $this->_pathInfos[ 0 ] : 'en';
        $menuPath    = __ROOTDIR__ . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'menu.' . $this->_lang . '.xml';
        
        if( !file_exists( $menuPath ) )
        {
            $this->_lang = 'en';
            $menuPath    = __ROOTDIR__ . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'menu.' . $this->_lang . '.xml';
        }
        
        $this->_menu = simplexml_load_file( $menuPath );
        
        $relPathInfos = $this->_pathInfos;
        
        array_shift( $relPathInfos );
        
        $this->_currentPath = implode( '/', $relPathInfos );
        
    }
    
    public function __clone()
    {
        throw new \XS\Singleton\Exception
        (
            'Class ' . __CLASS__ . ' cannot be cloned',
            \XS\Singleton\Exception::EXCEPTION_CLONE
        );
    }
    
    public static function getInstance()
    {
        if( !is_object( self::$_instance ) )
        {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    private function _getParams( array $params )
    {
        $query = '?';
        
        foreach( $params as $key => $value )
        {
            $query .= urlencode( $key ) . '=' . urlencode( $value ) . '&';
        }
        
        return substr( $query, 0, -1 );
    }
    
    public function getRootLine( $sep = ' - ' )
    {
        $rootline = $this->_menu[ 'root' ];
        $menu     = $this->_menu;
        
        for( $i = 1; $i < count( $this->_pathInfos ); $i++ )
        {
            $page = $this->_pathInfos[ $i ];
            
            if( !isset( $menu->$page ) )
            {
                goto end;
            }
            
            $menu = $menu->$page;
            
            if( isset( $menu->navTitle ) && !empty( $menu->navTitle ) )
            {
                $rootline .= $sep . htmlentities( $menu->navTitle );
            }
            else if( isset( $menu->title ) && !empty( $menu->title ) )
            {
                $rootline .= $sep . htmlentities( $menu->title );
            }
            
            $menu = $menu->sub;
        }
        
        end:
        
        if( !empty( $this->_rootlineExtraName ) )
        {
            $rootline .= $sep . htmlentities( $this->_rootlineExtraName );
        }
        
        return $rootline;
    }
    
    public function getRootLineMenu( $class = 'rootline' )
    {
        if( count( $this->_pathInfos ) == 1 )
        {
            return '';
        }
        
        $rootline            = new \XS\XHTML\Tag( 'div' );
        $rootline[ 'class' ] = $class;
        $menu                = $this->_menu;
        $list                = $rootline->ol;
        $list[ 'class' ]     = 'breadcrumb';
        $item                = $list->li;
        $link                = $item->a;
        $path                = '/' . $this->_lang . '/';
        $link[ 'href' ]      = $path;
        $link[ 'title' ]     = $menu[ 'root' ];
        
        $link->addTextData( $menu[ 'root' ] );
        
        for( $i = 1; $i < count( $this->_pathInfos ); $i++ )
        {
            $page = $this->_pathInfos[ $i ];
            
            if( !isset( $menu->$page ) )
            {
                goto end;
            }
            
            $menu            = $menu->$page;
            $item            = $list->li;
            $link            = $item->a;
            $path           .= $this->_pathInfos[ $i ] . '/';
            $link[ 'href' ]  = $path;
            $link[ 'title' ] = htmlentities( $menu->title );
            
            if( isset( $menu->navTitle ) )
            {
                $link->addTextData( htmlentities( $menu->navTitle ) );
            }
            else
            {
                $link->addTextData( htmlentities( $menu->title ) );
            }
            
            $menu = $menu->sub;
        }
        
        end:
        
        if( !empty( $this->_rootlineExtraName ) && !empty( $this->_rootlineExtraUrl ) )
        {
            $item            = $list->li;
            $link            = $item->a;
            $link[ 'href' ]  = $this->_rootlineExtraUrl;
            $link[ 'title' ] = htmlentities( $this->_rootlineExtraName );
            
            $link->addTextData( htmlentities( $this->_rootlineExtraName ) );
        }
        
        $item[ 'class' ] = 'active';
        
        return $rootline;
    }
    
    public function getHomePageURL()
    {
        return '/' . $this->_lang . '/';
    }
    
    public function getMenuLevel( $level, $showHidden = false )
    {
        if( !isset( $this->_pathInfos[ $level - 1 ] ) && $level > 1 )
        {
            return new \XS\XHTML\Comment( 'No available menu items' );
        }
        
        $menu = $this->_menu;
        $list = new \XS\XHTML\Tag( 'ul' );
        $path = '/' . $this->_lang . '/';
        
        $list->useFormattedOutput( false );
        
        for( $i = 1; $i < count( $this->_pathInfos ); $i++ )
        {
            $page = $this->_pathInfos[ $i ];
            
            if( !isset( $menu->$page ) && $i > 1 )
            {
                return new \XS\XHTML\Comment( 'No available menu items' );
            }
            
            if( $i == $level )
            {
                break;
            }
            
            $menu  = $menu->$page;
            $menu  = $menu->sub;
            $path .= $page . '/';
        }
        
        if( !$menu )
        {
            return '';
        }
        
        if( $i > 1 )
        {
            $menu = $menu->children();
        }
        
        $j = 0;
        
        foreach( $menu as $item )
        {
            if( isset( $item[ 'preview' ] ) )
            {
                continue;
            }
            
            if( isset( $item[ 'hidden' ] ) && $showHidden === false )
            {
                continue;
            }
            
            $listItem        = $list->li;
            $link            = $listItem->a;
            $link[ 'href' ]  = $path . $item->getName() . '/';
            
            if( isset( $item->linkTitle ) )
            {
                $link[ 'title' ] =  htmlentities( $item->linkTitle );
                
            }
            else
            {
                $link[ 'title' ] =  htmlentities( $item->title );
            }
            
            if( isset( $item->navTitle ) )
            {
                $link->addTextData(  htmlentities( $item->navTitle ) );
                
            }
            else
            {
                $link->addTextData(  htmlentities( $item->title ) );
            }
            
            if( $link[ 'href' ] == $_SERVER[ 'REQUEST_URI' ] || strpos( $_SERVER[ 'REQUEST_URI' ], $link[ 'href' ] ) === 0 )
            {
                $listItem[ 'class' ] = 'active';
            }
            
            if( isset( $item->anchor ) )
            {
                $link[ 'href' ] .= '#' . $item->anchor;
            }
            
            if( isset( $item->redirect ) )
            {
                $link[ 'href' ] = $item->redirect;
            }
            
            $j++;
        }
        
        return $list;
    }
    
    public function getPageTitleHeader( $link = true, $lockOnLevel = 2 )
    {
        $header = new \XS\XHTML\Tag( 'h1' );
        $menu   = $this->_menu;
        $path   = '/';
        
        if( !empty( $this->_pageTitle ) )
        {
            $header->addTextData( $this->_pageTitle );
            
            return $header;
        }
        
        if( count( $this->_pathInfos ) > 1 )
        {
            for( $i = 1; $i < count( $this->_pathInfos ); $i++ )
            {
                if( $i == $lockOnLevel + 1 )
                {
                    break;
                }
            
                $page = $this->_pathInfos[ $i ];
            
                if( !isset( $menu->$page ) )
                {
                    return new \XS\XHTML\Comment( 'No available page title' );
                }
            
                $menu  = $menu->$page;
                $path .= $menu->getName() . '/';
                $menu  = $menu->sub;
            }
            
            if( $link === true )
            {
                $header->addChildNode( $this->getPageLink( $path ) );
            }
            else
            {
                $header->addTextData( $this->getPageTitle( $path ) );
            }
        }
        else
        {
            if( $link === true )
            {
                $header->addChildNode( $this->getPageLink( '/' ) );
            }
            else
            {
                $header->addTextData( $this->getPageTitle( '/' ) );
            }
        }
        
        return $header;
    }
    
    public function getPageSubtitleHeader()
    {
        $header     = new \XS\XHTML\Tag( 'h2' );
        $menu       = $this->_menu;
        $path       = '/';
        $subtitle   = '';
        
        if( count( $this->_pathInfos ) > 1 )
        {
            for( $i = 1; $i < count( $this->_pathInfos ); $i++ )
            {
                $page = $this->_pathInfos[ $i ];
            
                if( !isset( $menu->$page ) )
                {
                    return new \XS\XHTML\Comment( 'No available page subtitle' );
                }
            
                $menu  = $menu->$page;
                $path .= $menu->getName() . '/';
                $menu  = $menu->sub;
            }
            
            $subtitle = $this->getPageSubtitle( $path );
        }
        else
        {
            $subtitle = $this->getPageSubtitle( '/' );
        }
        
        if( empty( $subtitle ) )
        {
            return new \XS\XHTML\Comment( 'No available page subtitle' );
        }
        
        $header->addTextData( $subtitle );
        
        return $header;
    }
    
    public function getPageTitle( $path = NULL )
    {
        if( $path === NULL )
        {
            $path = $this->_currentPath;
        }
        
        $path  = ( string )$path;
        $infos = explode( '/', $path );
        $menu  = $this->_menu;
        $title = '';
        
        if( count( $infos ) && $infos[ 0 ] === '' )
        {
            array_shift( $infos );
        }
        
        if( count( $infos ) && $infos[ count( $infos ) - 1 ] === '' )
        {
            array_pop( $infos );
        }
        
        if( count( $infos ) == 0 )
        {
            return $this->_menu[ 'root' ];
        }
        
        foreach( $infos as $page )
        {
            if( !isset( $menu->$page ) )
            {
                return '';
            }
            
            $menu  = $menu->$page;
            $title =  htmlentities( $menu->title );
            $menu  = $menu->sub;
        }
        
        return $title;
    }
    
    public function getPageSubtitle( $path = NULL )
    {
        if( $path === NULL )
        {
            $path = $this->_currentPath;
        }
        
        $path  = ( string )$path;
        $infos = explode( '/', $path );
        $menu  = $this->_menu;
        $title = '';
        
        if( count( $infos ) && $infos[ 0 ] === '' )
        {
            array_shift( $infos );
        }
        
        if( count( $infos ) && $infos[ count( $infos ) - 1 ] === '' )
        {
            array_pop( $infos );
        }
        
        if( count( $infos ) == 0 )
        {
            return '';
        }
        
        foreach( $infos as $page )
        {
            if( !isset( $menu->$page ) )
            {
                return '';
            }
            
            $menu  = $menu->$page;
            $title = htmlentities( $menu->subtitle );
            $menu  = $menu->sub;
        }
        
        return $title;
    }
    
    public function isPreview( $path = '' )
    {
        $menu = $this->_menu;
        
        if( $path )
        {
            $path      = ( substr( $path, 0, 1 ) !== '/' ) ? '/' . $path : $path;
            $path      = ( substr( $path, -1   ) !== '/' ) ? $path . '/' : $path;
            $pathInfos = explode( '/', $path );
            
            array_pop( $pathInfos );
        }
        else
        {
            $pathInfos = $this->_pathInfos;
        }
        
        for( $i = 1; $i < count( $pathInfos ); $i++ )
        {
            $page = $pathInfos[ $i ];
            
            if( !isset( $menu->$page ) )
            {
                return false;
            }
            
            $menu  = $menu->$page;
            
            if( isset( $menu[ 'preview' ] ) )
            {
                return true;
            }
            
            $menu  = $menu->sub;
        }
        
        return false;
    }
    
    public function getLanguage()
    {
        return $this->_lang;
    }
    
    public function redirectOnFirstSubpage()
    {
        $path = '/' . $this->_lang . '/';
        $menu = $this->_menu;
        
        for( $i = 1; $i < count( $this->_pathInfos ); $i++ )
        {
            $page = $this->_pathInfos[ $i ];
            
            if( !isset( $menu->$page ) )
            {
                return;
            }
            
            $menu  = $menu->$page;
            $path .= $menu->getName() . '/';
            $menu  = $menu->sub;
        }
        
        $children = $menu->children();
        
        if( !isset( $children[ 0 ] ) )
        {
            return;
        }
        else
        {
            header( 'Location: ' . $path . $children[ 0 ]->getName() . '/' );
        }
    }
    
    public function getPageLink( $path, $customTitle = '', array $params = array() )
    {
        $path     = ( string )$path;
        $infos    = explode( '/', $path );
        $segments = count( $infos );
        $link     = new \XS\XHTML\Tag( 'a' );
        $menu     = $this->_menu;
        $href     = '/' . $this->_lang . '/';
        
        if( count( $infos ) > 0 && $infos[ 0 ] === '' )
        {
            array_shift( $infos );
            $segments--;
        }
        
        if( count( $infos ) > 0 && $infos[ $segments - 1 ] === '' )
        {
            array_pop( $infos );
            $segments--;
        }
        
        if( count( $infos ) > 0 )
        {
            for( $i = 0; $i < $segments; $i++ )
            {
                $page = $infos[ $i ];
            
                if( !isset( $menu->$page ) )
                {
                    return ( string )new \XS\XHTML\Comment( 'No available menu link (path: ' . $path . ')' );
                }
            
                $menu  = $menu->$page;
                $href .= $page . '/';
                
                if( isset( $menu->linkTitle ) )
                {
                    $title = htmlentities( $menu->linkTitle );
                }
                else
                {
                    $title = htmlentities( $menu->title );
                }
            
                if( $i === $segments - 1 )
                {
                    break;
                }
            
                $menu = $menu->sub;
            }
        }
        else
        {
            $title = htmlentities( $this->_menu[ 'root' ] );
        }
        
        $link[ 'href' ]  = $href;
        
        if( $customTitle )
        {
            $link->addTextData( $customTitle );
        }
        else
        {
            $link->addTextData(  $title );
        }
        
        if( isset( $menu->anchor ) )
        {
            $link[ 'href' ] .= '#' . $menu->anchor;
        }
        
        if( count( $params ) )
        {
            $link[ 'href' ] .= $this->_getParams( $params );
        }
        
        if( isset( $menu->redirect ) )
        {
            $link[ 'href' ] = $menu->redirect;
        }
        
        return $link;
    }
    
    public function getPageUrl( $path, array $params = array() )
    {
        $path     = ( string )$path;
        $infos    = explode( '/', $path );
        $segments = count( $infos );
        $link     = new \XS\XHTML\Tag( 'a' );
        $menu     = $this->_menu;
        $href     = '/' . $this->_lang . '/';
        
        if( $infos[ 0 ] === '' )
        {
            array_shift( $infos );
            $segments--;
        }
        
        if( isset( $infos[ $segments - 1 ] ) && $infos[ $segments - 1 ] === '' )
        {
            array_pop( $infos );
            $segments--;
        }
        
        for( $i = 0; $i < $segments; $i++ )
        {
            $page = $infos[ $i ];
            
            if( !isset( $menu->$page ) )
            {
                return new \XS\XHTML\Comment( 'No available menu link (path: ' . $path . ')' );
            }
            
            $menu  = $menu->$page;
            $href .= $page . '/';
            
            if( $i === $segments - 1 )
            {
                break;
            }
            
            $menu = $menu->sub;
        }
        
        if( isset( $menu->anchor ) )
        {
            $href .= '#' . $menu->anchor;
        }
        
        if( isset( $menu->redirect ) )
        {
            $href = $menu->redirect;
        }
        
        if( count( $params ) )
        {
            $href .= $this->_getParams( $params );
        }
        
        return $href;
    }
    
    public function getCurrentPageLink( $customTitle = '', array $params = array() )
    {
        return $this->getPageLink( $this->_currentPath, $customTitle, $params );
    }
    
    public function getCurrentPageUrl( array $params = array() )
    {
        return $this->getPageUrl( $this->_currentPath, $params );
    }
    
    public function getDescription()
    {
        $description = '';
        $menu        = $this->_menu;
        
        for( $i = 0; $i < count( $this->_pathInfos ); $i++ )
        {
            $page = $this->_pathInfos[ $i ];
            
            if( $i == 1 )
            {
                $menu = $menu->$page;
            }
            elseif( $i > 1 && isset( $menu->sub ) )
            {
                $menu = $menu->sub->$page;
            }
            
            if( isset( $menu[ 'description' ] ) && !isset( $menu[ 'preview' ] ) )
            {
                $description = ( string )$menu[ 'description' ];
            }
        }
        
        return $description;
    }
    
    public function getKeywords()
    {
        $keywords = array();
        $menu     = $this->_menu;
        
        for( $i = 0; $i < count( $this->_pathInfos ); $i++ )
        {
            $page = $this->_pathInfos[ $i ];
            
            if( $i == 1 )
            {
                $menu = $menu->$page;
            }
            elseif( $i > 1 && isset( $menu->sub ) )
            {
                $menu = $menu->sub->$page;
            }
            
            if( isset( $menu[ 'keywords' ] ) && !isset( $menu[ 'preview' ] ) )
            {
                $words = explode( ',', $menu[ 'keywords' ] );
                
                foreach( $words as $word )
                {
                    $keywords[ $word ] = trim( $word );
                }
            }
        }
        
        return implode( ', ', $keywords );
    }
    
    public function isHomePage()
    {
        return count( $this->_pathInfos ) < 2;
    }
    
    public function getPageImage( $width = 0, $height = 0 )
    {
        $img                = new \XS\XHTML\Tag( 'img' );
        $img[ 'src' ]       = $this->_menu[ 'image' ];
        $img[ 'alt' ]       = $this->getPageTitle();
        $menu               = $this->_menu;
        
        if( $width > 0 )
        {
            $img[ 'width' ]     = ( int )$width;
        }
        
        if( $height > 0 )
        {
            $img[ 'height' ]    = ( int )$height;
        }
        
        for( $i = 0; $i < count( $this->_pathInfos ); $i++ )
        {
            $page = $this->_pathInfos[ $i ];
            
            if( $i == 1 )
            {
                $menu = $menu->$page;
            }
            elseif( $i > 1 && isset( $menu->sub ) )
            {
                $menu = $menu->sub->$page;
            }
            
            if( isset( $menu->image ) && !isset( $menu[ 'preview' ] ) )
            {
                $img[ 'src' ] = $menu->image;
            }
        }
        
        return $img;
    }
    
    public function getPageBanner()
    {
        $banner = $this->_menu[ 'banner' ];
        $menu   = $this->_menu;
        
        for( $i = 0; $i < count( $this->_pathInfos ); $i++ )
        {
            $page = $this->_pathInfos[ $i ];
            
            if( $i == 1 )
            {
                $menu = $menu->$page;
            }
            elseif( $i > 1 && isset( $menu->sub ) )
            {
                $menu = $menu->sub->$page;
            }
            
            if( isset( $menu->banner ) && !isset( $menu[ 'preview' ] ) )
            {
                $banner = $menu->banner;
            }
        }
        
        if( !empty( $banner ) )
        {
            $root = str_replace( $_SERVER[ 'SCRIPT_NAME' ], '', $_SERVER[ 'SCRIPT_FILENAME' ] );
            $path = $root . DIRECTORY_SEPARATOR . 'banners' . DIRECTORY_SEPARATOR . $banner . DIRECTORY_SEPARATOR . 'index.php';
            
            if( !file_exists( $path ) )
            {
                return '';
            }
        
            if( !is_readable( $path ) )
            {
                return '';
            }
        
            ob_start();
        
            try
            {
                require( $path );
            
                $out = ob_get_contents();
            }
            catch( Exception $e )
            {
                $out = '';
            }
        
            ob_end_clean();
        
            return $out;
        }
        
        return '';
    }
    
    public function setPageTitle( $title )
    {
        $this->_pageTitle = ( string )$title;
    }
    
    public function addRootlineItem( $name, $url )
    {
        $this->_rootlineExtraName   = ( string )$name;
        $this->_rootlineExtraUrl    = ( string )$url;
    }
}
