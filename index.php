<?php

################################################################################
# Copyright (c) 2009, Jean-David Gadina <macmade@xs-labs.com>                  #
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

$REQUEST = $_SERVER[ 'REQUEST_URI' ];
$QUERY   = '';

if( strpos( $REQUEST, '?' ) )
{
    $QUERY   = '?' . $_SERVER[ 'QUERY_STRING' ];
    $REQUEST = substr( $REQUEST, 0, strpos( $REQUEST, '?' ) );
}

$PATH_INFOS = explode( '/', $REQUEST );

array_shift( $PATH_INFOS );

if( $PATH_INFOS[ 0 ] === '' || $PATH_INFOS[ 0 ] === 'index.php' )
{
    header( 'Location: /en/' );
    exit();
    
}
elseif( count( $PATH_INFOS ) > 1 && $PATH_INFOS[ count( $PATH_INFOS ) - 1 ] === 'index.php' )
{
    header( 'Location: ' . substr( $REQUEST, 0, -9 ) . $QUERY );
    exit();
}
else
{
    $ROOT = str_replace( $_SERVER[ 'SCRIPT_NAME' ], '', $_SERVER[ 'SCRIPT_FILENAME' ] );
    $PATH = str_replace( '/', DIRECTORY_SEPARATOR, $REQUEST );
    
    unset( $PATH_INFOS );
    unset( $REQUEST );
    unset( $QUERY );
    
    require_once( $ROOT . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'init.inc.php' );
    
    $LAYOUT = \XS\Layout::getInstance();
    
    $INCLUDE_404 = $ROOT . DIRECTORY_SEPARATOR . 'errors' . DIRECTORY_SEPARATOR . '404' . DIRECTORY_SEPARATOR . 'index.php';
    $INCLUDE_403 = $ROOT . DIRECTORY_SEPARATOR . 'errors' . DIRECTORY_SEPARATOR . '403' . DIRECTORY_SEPARATOR . 'index.php';
    $INCLUDE_200 = $ROOT . DIRECTORY_SEPARATOR . $PATH . 'index.php';
    
    if( \XS\Menu::getInstance()->isPreview() === true && $_SERVER[ 'SERVER_NAME' ] !== 'xs-labs.localhost' )
    {
        $INCLUDE = $INCLUDE_403;
    }
    elseif( !file_exists( $ROOT . $PATH ) )
    {
        $INCLUDE = $INCLUDE_404;
    }
    elseif( !file_exists( $ROOT . $PATH . 'index.php' ) )
    {
        $INCLUDE = $INCLUDE_403;
    }
    else
    {
        $INCLUDE = $INCLUDE_200;
    }
    
    $CONTENT = $LAYOUT->getContent( $INCLUDE );
    $HEADER  = $LAYOUT->getHeader();
    $FOOTER  = $LAYOUT->getFooter();
    
    $DEBUG_STACK = \XS\Debug::getStack();
    
    if( count( $DEBUG_STACK ) )
    {
        foreach( $DEBUG_STACK as $DEBUG )
        {
            print ( string )$DEBUG;
        }
    }
    
    print $HEADER;
    print $CONTENT;
    print $FOOTER;
    
    unset( $ROOT );
    unset( $PATH );
    unset( $LAYOUT );
    unset( $INCLUDE );
    unset( $HEADER );
    unset( $CONTENT );
    unset( $FOOTER );
    unset( $DEBUG_STACK );
    unset( $DEBUG );
}
