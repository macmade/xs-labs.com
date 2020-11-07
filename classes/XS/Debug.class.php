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

final class Debug
{
    protected static $_stack = array();
    
    private function __construct()
    {}
    
    private static function _getVarType( &$var )
    {
        $type = 'unknown';
        
        if( is_object( $var ) )
        {
            $type = 'object';
        }
        elseif( is_resource( $var ) )
        {
            $type = 'ressource';
        }
        elseif( is_array( $var ) )
        {
            $type = 'array';
        }
        elseif( is_string( $var ) )
        {
            $type = 'string';
        }
        elseif( is_int( $var ) )
        {
            $type = 'int';
        }
        elseif( is_link( $var ) )
        {
            $type = 'link';
        }
        elseif( is_float( $var ) )
        {
            $type = 'float';
        }
        elseif( is_null( $var ) )
        {
            $type = 'null';
        }
        elseif( is_bool( $var ) )
        {
            $type = 'boolean';
        }
        
        return $type;
    }
    
    public static function dumpArray( array $array, $return = false )
    {
        $commonStyle          = 'font-family: Verdana, sans-serif; font-size: 10px; color: #898989; ';
        
        $container            = new \XS\XHTML\Tag( 'div' );
        $container[ 'style' ] = $commonStyle;
        
        $container->comment( 'PHP array debug  - start' );
        
        $table                = $container->table;
        $table[ 'style' ]     = 'background-color: #EDF5FA; border: solid 1px #D3E7F4; margin: 2px; padding: 2px;';
        
        $container->comment( 'PHP array debug - end' );
        
        foreach( $array as $key => &$value )
        {
            $varType = self::_getVarType( $value );
            
            $row = $table->tr;
            
            $labelCol = $row->td;
            $dataCol  = $row->td;
            
            $labelCol[ 'width' ] = '20%';
            $labelCol[ 'style' ] = $commonStyle . 'background-color: #FFFFFF; border: solid 1px #D3E7F4; margin: 2px; padding: 2px;';
            $dataCol[ 'style' ]  = $commonStyle . 'background-color: #FFFFFF; border: solid 1px #D3E7F4; margin: 2px; padding: 2px;';
            
            $label            = $labelCol->strong;
            $label[ 'style' ] = 'color: #0062A0;';
            $labelCol->span   = ': ' . $varType;
            $label->addTextData( $key );
            
            if( is_array( $value ) )
            {
                $dataCol->addTextData( self::dumpArray( $value, true ) );
            }
            elseif( is_object( $value ) )
            {
                $dataCol->div->pre = print_r( $value, true );
            }
            elseif( is_bool( $var ) )
            {
                $value = ( $value ) ? 'true' : 'false';
                $dataCol->addTextData( $value );
            }
            else
            {
                $dataCol->addTextData( $value );
            }
        }
        
        if( $return )
        {
            return $container;
        }
        
        self::$_stack[] = $container;
    }
    
    public static function dump( $var, $return = false, $header = 'Debug informations' )
    {
        $commonStyle          = 'font-family: Verdana, sans-serif; font-size: 10px; color: #898989; ';
        
        $container            = new \XS\XHTML\Tag( 'div' );
        $container[ 'style' ] = $commonStyle . 'background-color: #EDF5FA; border: solid 1px #D3E7F4; margin: 2px; padding: 2px;';
        
        $container->comment( 'PHP variable debug  - start' );
        
        $headerSection            = $container->div;
        $headerSection[ 'style' ] = $commonStyle . 'text-align: center; background-color: #FFFFFF; border: solid 1px #D3E7F4; margin: 2px; padding: 2px;';
        $headerText               = $headerSection->strong;
        $headerText[ 'style' ]    = 'color: #0062A0; font-size: 15px';
        $headerText->addTextData( $header );
        
        $typeSection            = $container->div;
        $typeSection[ 'style' ] = $commonStyle . 'background-color: #FFFFFF; border: solid 1px #D3E7F4; margin: 2px; padding: 2px;';
        $typeText               = $typeSection->strong;
        $typeText[ 'style' ]    = 'color: #0062A0;';
        $typeSection->span      = self::_getVarType( $var );
        $typeText->addTextData( 'Variable type: ' );
        
        $container->comment( 'PHP variable debug - end' );
        
        $dataDiv            = $container->div;
        $dataDiv[ 'style' ] = $commonStyle . 'background-color: #FFFFFF; border: solid 1px #D3E7F4; margin: 2px; padding: 2px;';
        
        if( is_array( $var ) )
        {
            $dataDiv->addTextData( self::dumpArray( $var, true ) );
        }
        elseif( is_object( $var ) )
        {
            $dataDiv->pre = print_r( $var, true );
        }
        elseif( is_bool( $value ) )
        {
            $value = ( $var ) ? 'true' : 'false';
            $dataDiv->addTextData( $value );
        }
        else
        {
            $dataDiv->addTextData( $var );
        }
        
        if( $return )
        {
            return $container;
        }
        
        self::$_stack[] = $container;
    }
    
    public static function getStack()
    {
        return self::$_stack;
    }
}
