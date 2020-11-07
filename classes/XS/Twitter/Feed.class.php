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

namespace XS\Twitter;

class Feed
{
    const CACHE_FILE        = 'tmp/twitter-cache.xml';
    const CACHE_TTL         = 3600;
    
    protected $_screenName      = '';
    protected $_consumerKey     = '';
    protected $_consumerSecret  = '';
    protected $_accessToken     = '';
    protected $_accessSecret    = '';
    protected $_data            = '';
    protected $_json            = array();
    protected $_error           = false;
    protected $_limit           = 0;
    protected $_lang            = NULL;
    
    public function __construct( $consumerKey, $consumerSecret, $accessToken, $accessSecret, $screenName, $limit = 10 )
    {
        $this->_lang            = \XS\Language\File::getInstance( __CLASS__ );
        $this->_consumerKey     = ( string )$consumerKey;
        $this->_consumerSecret  = ( string )$consumerSecret;
        $this->_accessToken     = ( string )$accessToken;
        $this->_accessSecret    = ( string )$accessSecret;
        $this->_screenName      = ( string )$screenName;
        $this->_limit           = ( int )$limit;
        
        $this->_getJSONData();
        
        if( $this->_data === '' )
        {
            $this->_error = true;
            return;
        }
        
        try
        {
            $this->_json = json_decode( $this->_data );
        }
        catch( Exception $e )
        {
            $this->_error = true;
            return;
        }
    }
    
    public function __toString()
    {
        if( $this->_error === true )
        {
            $link             = new \XS\XHTML\Tag( 'a' );
            $link[ 'href' ]   = 'https://twitter.com/' . $this->_screenName;
            $link[ 'title' ]  = $this->_lang->twitter . ': ' . $this->_screenName;
            
            $link->addTextData( $link[ 'href' ] );
            
            $error            = new \XS\XHTML\Tag( 'div' );
            $error[ 'class' ] = 'tweet-error';
            $error->div       = sprintf( $this->_lang->notAvailable, $this->_screenName );
            $error->div       = sprintf( $this->_lang->tryLater, $this->_screenName ) . '<br />' . $link;
            $error->div       = $this->_lang->sorry;
            
            return ( string )$error;
        }
        
        $i       = 0;
        $content = new \XS\XHTML\Tag( 'div' );
        
        foreach( $this->_json as $status )
        {
            if( $i === $this->_limit )
            {
                break;
            }
            
            $tweet    = $content->div;
            $text     = $tweet->div;
            $infos    = $tweet->div;
            $user     = $infos->span;
            $sep      = $infos->span;
            $date     = $infos->span;
            $userLink = $user->a;
            
            $tweet[ 'class' ]    = ( $i % 2 ) ? 'tweet-alt' : 'tweet';
            $text[ 'class'  ]    = 'tweet-text';
            $infos[ 'class' ]    = 'tweet-infos';
            $user[ 'class' ]     = 'tweet-infos-user';
            $date[ 'class' ]     = 'tweet-infos-date';
            $userLink[ 'href' ]  = 'http://twitter.com/' . $status->user->screen_name;
            $userLink[ 'title' ] = $this->_lang->twitter . ': ' . $status->user->screen_name;
            
            $statusText = $status->text;
            $statusText = preg_replace_callback( '/(http|ftp)+(s?)?:(\/\/)((\w|\.)+)(\/)?(\S+)?/i', array( $this, '_replaceLinks' ), $statusText );
            $statusText = preg_replace_callback( '/@([^ ]+)/', array( $this, '_replaceTwitterNames' ), $statusText );
            $statusText = preg_replace_callback( '/#([^ ]+)/', array( $this, '_replaceTwitterTags' ),  $statusText );
            
            $text->addTextData( $statusText );
            $userLink->addTextData( $status->user->screen_name );
            $sep->addTextData( ' - ' );
            $date->addTextData( date( 'd.m.Y / H:i', strtotime( $status->created_at ) ) );
            
            $i++;
        }
        
        return ( string )$content;
    }
    
    protected function _replaceLinks( array $matches )
    {
        $link            = new \XS\XHTML\Tag( 'a' );
        $link[ 'href' ]  = $matches[ 0 ];
        $link[ 'title' ] = $matches[ 0 ];
        
        $link->addTextData( $matches[ 0 ] );
        
        return ( string )$link;
    }
    
    protected function _replaceTwitterNames( array $matches )
    {
        $link            = new \XS\XHTML\Tag( 'a' );
        $link[ 'href' ]  = 'http://twitter.com/' . $matches[ 1 ];
        $link[ 'title' ] = $this->_lang->twitter . ': ' . $matches[ 1 ];
        
        $link->addTextData( $matches[ 0 ] );
        
        return ( string )$link;
    }
    
    protected function _replaceTwitterTags( array $matches )
    {
        $link            = new \XS\XHTML\Tag( 'a' );
        $link[ 'href' ]  = 'http://twitter.com/search?q=%23' . $matches[ 1 ];
        $link[ 'title' ] = $this->_lang->twitter . ': ' . $matches[ 1 ];
        
        $link->addTextData( $matches[ 0 ] );
        
        return ( string )$link;
    }
    
    protected function _getJSONData()
    {
        $time     = time();
        $cache    = __ROOTDIR__ . DIRECTORY_SEPARATOR .self::CACHE_FILE;
        $cacheDir = dirname( $cache );
        
        if( !file_exists( $cache ) && !is_writable( $cacheDir ) )
        {
            throw new \XS\Twitter\Feed\Exception
            (
                'The cache directory is not writeable (path: ' . $cacheDir . ')',
                \XS\Twitter\Feed\Exception::EXCEPTION_CACHE_DIR_NOT_WRITEABLE
            );
        }
        elseif( file_exists( $cache ) && !is_writable( $cache ) )
        {
            throw new \XS\Twitter\Feed\Exception
            (
                'The cache file is not writeable (path: ' . $cache . ')',
                \XS\Twitter\Feed\Exception::EXCEPTION_CACHE_FILE_NOT_WRITEABLE
            );
        }
        
        if( file_exists( $cache ) && $time < filemtime( $cache ) + self::CACHE_TTL )
        {
            $this->_data = file_get_contents( $cache );
            
            return;
        }
        
        $hash   = 'count=' . $this->_limit
                . '&oauth_consumer_key=' . $this->_consumerKey
                . '&oauth_nonce=' . time()
                . '&oauth_signature_method=HMAC-SHA1'
                . '&oauth_timestamp=' . time()
                . '&oauth_token=' . $this->_accessToken
                . '&oauth_version=1.0'
                . '&screen_name=' . $this->_screenName;
                
        $base   = 'GET'
                . '&'
                . rawurlencode( 'https://api.twitter.com/1.1/statuses/user_timeline.json' )
                . '&'
                . rawurlencode( $hash );
                
        $key    = rawurlencode( $this->_consumerSecret )
                . '&'
                . rawurlencode( $this->_accessSecret );
        
        $sig = rawurlencode( base64_encode( hash_hmac( 'sha1', $base, $key, true ) ) );
        
        $header = 'count="' . $this->_limit . '"'
                . ', oauth_consumer_key="' . $this->_consumerKey . '"'
                . ', oauth_nonce="' . time() . '"'
                . ', oauth_signature="' . $sig . '"'
                . ', oauth_signature_method="HMAC-SHA1"'
                . ', oauth_timestamp="' . time() . '"'
                . ', oauth_token="' . $this->_accessToken . '"'
                . ', oauth_version="1.0"'
                . ', screen_name="' . $this->_screenName . '"';
         
        $headers = array
        (
            'Authorization: Oauth {' . $header . '}',
            'Expect:'
        );
        
        $req = curl_init();
        
        curl_setopt( $req, CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $req, CURLOPT_HEADER, true );
        curl_setopt( $req, CURLOPT_URL, 'https://api.twitter.com/1.1/statuses/user_timeline.json?count=' . $this->_limit . '&screen_name=' . $this->_screenName );
        curl_setopt( $req, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $req, CURLOPT_SSL_VERIFYPEER, false );
        
        $data = curl_exec( $req );
        
        curl_close( $req );
        
        $status = substr( $data, 0, 12 );
        $json   = trim( substr( $data, strpos( $data, chr( 13 ) . chr( 10 ) . chr( 13 ) . chr( 10 ) ) ) );
        
        if( $status != 'HTTP/1.1 200' || strlen( $json ) === 0 )
        {
            if( file_exists( $cache ) )
            {
                $this->_data = file_get_contents( $cache );
            }
            
            return;
        }
        
        $this->_data = $json;
        
        file_put_contents( $cache, $this->_data );
    }
}
