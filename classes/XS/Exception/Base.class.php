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

namespace XS\Exception;

abstract class Base extends \Exception
{
    protected static $_debug        = true;
    protected static $_commonStyles = 'font-family: Verdana, sans-serif; font-size: 10px; color: #898989;';
    
    public function __construct( $message, $code = 0 )
    {
        parent::__construct( $message, $code );
    }
    
    public function __toString()
    {
        if( self::$_debug )
        {
            print $this->getInfos();
        }
        
        return get_class( $this ) . ' exception with message "' . $this->message . '"';
    }
    
    protected function _traceTitle( $title )
    {
        return '<h1 style="' . self::$_commonStyles . ' color: #0062A0;">' . $title . '</h1>';
    }
    
    protected function _traceInfo( $label, $value )
    {
        $labelDiv = '<div style="' . self::$_commonStyles . ' font-weight: bold;">' . $label . '</div>';
        $valueDiv = '<div style="' . self::$_commonStyles . '">' . $value . '</div>';
        
        return '<div style="margin-bottom: 5px;">' . $labelDiv . $valueDiv . '</div>';
    }
    
    protected function _traceHistory()
    {
        $traceArray = $this->getTrace();
        $str        = '';
        
        if( is_array( $traceArray ) )
        {
            foreach( $traceArray as $key => $value  )
            {
                $file     = ( isset( $value[ 'file' ] ) )                              ? $this->_traceInfo( 'File:', $value[ 'file' ] )                                            : '';
                $line     = ( isset( $value[ 'line' ] ) )                              ? $this->_traceInfo( 'Line:', $value[ 'line' ] )                                            : '';
                $function = ( isset( $value[ 'function' ] ) )                          ? $this->_traceInfo( 'Function:', $value[ 'function' ] )                                    : '';
                $class    = ( isset( $value[ 'class' ] ) )                             ? $this->_traceInfo( 'Class:', $value[ 'class' ] )                                          : '';
                $type     = ( isset( $value[ 'type' ] ) )                              ? $this->_traceInfo( 'Call type:', ( ( $value[ 'type' ] == '::' ) ? 'static' : 'member' ) ) : '';
                $code     = ( isset( $value[ 'file' ] ) && isset( $value[ 'line' ] ) ) ? $this->_traceInfo( 'Code:', $this->_getCode( $value[ 'file' ], $value[ 'line' ] ) )       : '';
                
                if( isset( $value[ 'args' ] ) && is_array( $value[ 'args' ] ) && count( $value[ 'args' ] ) )
                {
                    $args = $this->_traceInfo( 'Arguments:', $this->_getArgs( $value[ 'args' ] ) );
                }
                else
                {
                    $args = '';
                }
                
                $str .= '<div style="margin-top: 5px; padding: 5px; border: solid 1px #D3E7F4; background-color: #FFFFFF;">' . $class . $function . $type . $file . $line . $args . $code . '</div>';
            }
        }
        
        return $str;
    }
    
    protected function _getArgs( array $args )
    {
        $argsList = '';
        
        foreach( $args as $argNum => $argValue )
        {
            if( is_object( $argValue ) )
            {
                $argType = 'Object: ' . get_class( $argValue );
            }
            elseif( is_array( $argValue ) )
            {
                $argType = 'Array: ' . count( $argValue );
            }
            elseif( is_bool( $argValue ) )
            {
                $argType = 'Boolean: ' . ( ( $argValue ) ? 'true' : 'false' );
            }
            elseif( is_int( $argValue ) )
            {
                $argType = 'Integer: ' . $argValue;
            }
            elseif( is_float( $argValue ) )
            {
                $argType = 'Floating point: ' . $argValue;
            }
            elseif( is_resource( $argValue ) )
            {
                $argType = 'Ressource: ' . get_resource_type( $argValue );
            }
            elseif( is_null( $argValue ) )
            {
                $argType = 'Null';
            }
            elseif( is_string( $argValue ) )
            {
                $argType = ( strlen( $argValue ) > 128 ) ? 'String: ' . htmlspecialchars( substr( $argValue, 0, 128 ) ) . '[...]' : 'String: ' . htmlspecialchars( $argValue );
            }
            else
            {
                $argType = 'Other';
            }
            
            $argsList .= $argNum . ') ' . $argType . '<br />';
        }
        
        return $argsList;
    }
    
    protected function _getCode( $file, $line )
    {
        $lines = file( $file );
        
        if( is_array( $lines ) )
        {
            $lineLength = strlen( $line + 3 );
            $line3      = ( isset( $lines[ $line - 1 ] ) ) ? '<strong style="color: #0062A0;">' . str_pad( $line, $lineLength, 0, STR_PAD_LEFT ) . ': ' . $lines[ $line -1 ] . '</strong>' : '';
            $line0      = ( isset( $lines[ $line - 4 ] ) ) ? str_pad( $line - 3, $lineLength, 0, STR_PAD_LEFT ) . ': ' . htmlspecialchars( $lines[ $line - 4 ] ) : '';
            $line1      = ( isset( $lines[ $line - 3 ] ) ) ? str_pad( $line - 2, $lineLength, 0, STR_PAD_LEFT ) . ': ' . htmlspecialchars( $lines[ $line - 3 ] ) : '';
            $line2      = ( isset( $lines[ $line - 2 ] ) ) ? str_pad( $line - 1, $lineLength, 0, STR_PAD_LEFT ) . ': ' . htmlspecialchars( $lines[ $line - 2 ] ) : '';
            $line4      = ( isset( $lines[ $line ] ) )     ? str_pad( $line + 1, $lineLength, 0, STR_PAD_LEFT ) . ': ' . htmlspecialchars( $lines[ $line ] )     : '';
            $line5      = ( isset( $lines[ $line + 1 ] ) ) ? str_pad( $line + 2, $lineLength, 0, STR_PAD_LEFT ) . ': ' . htmlspecialchars( $lines[ $line + 1 ] ) : '';
            $line6      = ( isset( $lines[ $line + 2 ] ) ) ? str_pad( $line + 3, $lineLength, 0, STR_PAD_LEFT ) . ': ' . htmlspecialchars( $lines[ $line + 2 ] ) : '';
            
            return '<div style="' . self::$_commonStyles . ' white-space: pre; font-family: monospace; border: solid 1px #D3E7F4; background-color: #EDF5FA; padding: 5px; margin-top: 5px;">' . $line0 . $line1 . $line2 . $line3 . $line4 . $line5 . $line6 . '</div>';
        }
        
        return '';
    }
    
    public static function setDebugState( $value )
    {
        $oldState     = self::$_debug;
        self::$_debug = ( boolean )$value;
        
        return $oldState;
    }
    
    public function getInfos()
    {
        $trace = '<div style="' . self::$_commonStyles . ' background-color: #EDF5FA; border: solid 1px #D3E7F4; margin: 10px; padding: 10px;">'
               . $this->_traceTitle( 'Exception of type \'' . get_class( $this ) . '\'' )
               . $this->_traceInfo( 'Message:', $this->message )
               . $this->_traceInfo( 'Code:',    $this->code )
               . $this->_traceInfo( 'File:',    $this->file )
               . $this->_traceInfo( 'Line:',    $this->line )
               . $this->_traceTitle( 'Debug backtrace:' )
               . $this->_traceHistory()
               . '</div>';
        
        return $trace;
    }
}
