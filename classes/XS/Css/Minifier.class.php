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

namespace XS\CSS;

class Minifier
{
    protected $_css          = '';
    protected $_path         = '';
    protected $_basePath     = '';
    protected $_comment      = '';
    protected $_colors       = array();
    protected $_colorsSimple = array();
    
    public function __construct()
    {
        for( $i = 0; $i < 16; $i++ )
        {
            $c                     = dechex( $i );
            $this->_colors[]       = '/#' . str_repeat( $c, 6 ) . '/i';
            $this->_colorsSimple[] = '#'  . str_repeat( $c, 3 );
        }
    }
    
    public function __toString()
    {
        $css = '/* <![CDATA[ */'
             . chr( 10 )
             . chr( 10 )
             . ( ( $this->_comment ) ? $this->_comment . chr( 10 ) . chr( 10 ) : '' )
             . $this->_css
             . chr( 10 )
             . chr( 10 )
             . '/* ]]> */';
        
        return $css;
    }
    
    public function setBaseDirectory( $path )
    {
        $this->_path = ( string )$path;
        
        if( substr( $this->_path, -1, 1 ) !== '/' )
        {
            $this->_path .= '/';
        }
        
        $this->_basePath = dirname( $_SERVER[ 'SCRIPT_NAME' ] );
        
        if( substr( $this->_basePath, -1, 1 ) !== '/' )
        {
            $this->_basePath .= '/';
        }
    }
    
    public function import( $path )
    {
        $path = ( string )$path;
        $path = $this->_path . $path;
        
        if( !file_exists( $path ) )
        {
            return;
        }
        
        $css = file_get_contents( $path );
        
        $css = preg_replace( '|url\(\s?(["\'])([^ )]+) ?\)([; ])|', 'url(\1' . $this->_basePath . '\2)\3', $css );
        $css = preg_replace( $this->_colors, $this->_colorsSimple, $css );
        
        $css = preg_replace( '#\s+#', ' ', $css );
        $css = preg_replace( '#/\*.*?\*/#s', '', $css );
        
        $css = str_replace( '; ', ';', $css );
        $css = str_replace( ': ', ':', $css );
        $css = str_replace( ' {', '{', $css );
        $css = str_replace( '{ ', '{', $css );
        $css = str_replace( ', ', ',', $css );
        $css = str_replace( '} ', '}', $css );
        $css = str_replace( ';}', '}', $css );
        
        $this->_css .= trim( $css );
    }
    
    public function output()
    {
        header( 'Content-type: text/css' );
        print $this;
        exit();
    }
    
    public function setComment( $text )
    {
        $lines  = explode( chr( 10 ), $text );
        $length = 0;
        
        foreach( $lines as $pos => $line )
        {
            $lines[ $pos ] = utf8_decode( $line );
            $lineLength    = strlen( utf8_decode( $line ) );
            $length        = ( $lineLength > $length ) ? $lineLength : $length;
        }
        
        $sep     = str_repeat( '#', $length + 4 );
        $comment = $sep . chr( 10 );
        
        foreach( $lines as $line )
        {
            $lineLength = strlen( $line );
            $comment   .= '# '
                       .  str_repeat( ' ', floor( ( $length - $lineLength ) / 2 ) )
                       .  $line
                       .  str_repeat( ' ', ceil( ( $length - $lineLength ) / 2 ) )
                       .  ' #'
                       .  chr( 10 );
        }
        
        $comment .= $sep;
        
        $this->_comment = '/*'
                        . chr( 10 )
                        . chr( 10 )
                        . $comment
                        . chr( 10 )
                        . chr( 10 )
                        . '*/';
    }
}
