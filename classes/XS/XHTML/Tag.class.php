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

namespace XS\XHTML;

class Tag implements \ArrayAccess, \Iterator
{
    protected static $_formattedOutput = true;
    protected static $_hasStatic       = false;
    protected static $_emptyTags       = array(
        'area'  => true,
        'base'  => true,
        'br'    => true,
        'col'   => true,
        'img'   => true,
        'input' => true,
        'hr'    => true,
        'link'  => true,
        'meta'  => true,
        'param' => true
    );
    
    protected static $_NL              = '';
    protected static $_TAB             = '';
    protected $_tagName                = '';
    protected $_attribs                = array();
    protected $_children               = array();
    protected $_childrenByName         = array();
    protected $_childrenCountByName    = array();
    protected $_childrenCount          = 0;
    protected $_hasNodeChildren        = false;
    protected $_parents                = array();
    protected $_iteratorIndex          = 0;
    
    public function __construct( $tagName )
    {
        if( !self::$_hasStatic )
        {
            self::_setStaticVars();
        }
        
        $this->_tagName = ( string )$tagName;
    }
    
    public function __toString()
    {
        return $this->asHtml();
    }
    
    public function __set( $name, $value )
    {
        $this->_addChild( $name )->addTextData( $value );
    }
    
    public function __get( $name )
    {
        return $this->_addChild( $name );
    }
    
    public function __call( $name, array $args = array() )
    {
        switch( $name )
        {
            case 'spacer':
                
                return $this->_addSpacer( $args[ 0 ] );
                break;
            
            case 'comment':
                
                return $this->_addComment( $args[ 0 ] );
                break;
        }
    }
    
    public function offsetExists( $offset )
    {
        return isset( $this->_attribs[ $offset ] );
    }
    
    public function offsetGet( $offset )
    {
        return $this->_attribs[ $offset ];
    }
    
    public function offsetSet( $offset, $value )
    {
        $this->_attribs[ $offset ] = ( string )$value;
    }
    
    public function offsetUnset( $offset )
    {
        unset( $this->_attribs[ $offset ] );
    }
    
    public function rewind()
    {
        $this->_iteratorIndex = 0;
    }
    
    public function current()
    {
        return $this->_children[ $this->_iteratorIndex ];
    }
    
    public function key()
    {
        return $this->_children[ $this->_iteratorIndex ]->_tagName;
    }
    
    public function next()
    {
        $this->_iteratorIndex++;
    }
    
    public function valid()
    {
        return isset( $this->_children[ $this->_iteratorIndex ] );
    }
    
    private static function _setStaticVars()
    {
        self::$_NL        = chr( 10 );
        self::$_TAB       = chr( 9 );
        self::$_hasStatic = true;
    }
    
    public static function useFormattedOutput( $value )
    {
        $oldValue               = self::$_formattedOutput;
        self::$_formattedOutput = ( boolean )$value;
        
        return $oldValue;
    }
    
    protected function _addSpacer( $pixels )
    {
        $spacer            = $this->_addChild( 'div' );
        $spacer[ 'class' ] = 'spacer';
        $spacer[ 'style' ] = 'margin-top: ' . $pixels . 'px';
        return $spacer;
    }
    
    protected function _addComment( $text )
    {
        if( !isset( $this->_childrenByName[ '<!--' ] ) )
        {
            $this->_childrenByName[ '<!--' ]      = array();
            $this->_childrenCountByName[ '<!--' ] = 0;
        }
        
        $comment             = new \XS\XHTML\Comment( $text );
        $comment->_parents[] = $this;
        
        $this->_children[]                = $comment;
        $this->_childrenByName[ '<!--' ][] = $comment;
        
        $this->_childrenCountByName[ '<!--' ]++;
        $this->_childrenCount++;
        
        $this->_hasNodeChildren = true;
        
        return $comment;
    }
    
    protected function _addChild( $name )
    {
        if( !isset( $this->_childrenByName[ $name ] ) )
        {
            $this->_childrenByName[ $name ]      = array();
            $this->_childrenCountByName[ $name ] = 0;
        }
        
        $child             = new self( $name );
        $child->_parents[] = $this;
        
        $this->_children[]                = $child;
        $this->_childrenByName[ $name ][] = $child;
        
        $this->_childrenCountByName[ $name ]++;
        $this->_childrenCount++;
        
        $this->_hasNodeChildren = true;
        
        return $child;
    }
    
    protected function _output( $xmlCompliant = false, $level = 0 )
    {
        $tag = '<' . $this->_tagName;
        
        foreach( $this->_attribs as $key => &$value )
        {
            $tag .= ' ' . $key . '="' . $value . '"';
        }
        
        if( !$this->_childrenCount || ( $xmlCompliant === false && isset( self::$_emptyTags[ $this->_tagName ] ) ) )
        {
            $tag .= ( isset( self::$_emptyTags[ $this->_tagName ] ) || $xmlCompliant ) ? ' />' : '></' . $this->_tagName . '>';
        }
        else
        {
            $tag .= '>';
            
            foreach( $this->_children as $child )
            {
                if( $child instanceof self )
                {
                    if( self::$_formattedOutput )
                    {
                        $tag .= self::$_NL . str_pad( '', $level + 1, self::$_TAB );
                        $tag .= $child->_output( $xmlCompliant, $level + 1 );
                    }
                    else
                    {
                        $tag .= $child->_output( $xmlCompliant, $level + 1 );
                    }
                }
                elseif( $xmlCompliant )
                {
                    if( $this->_hasNodeChildren )
                    {
                        $tag .= '<span><![CDATA[' . $child . ']]></span>';
                    }
                    else
                    {
                        $tag .= '<![CDATA[' . $child . ']]>';
                    }
                }
                else
                {
                    $tag .= ( string )$child;
                }
            }
            
            if( self::$_formattedOutput && $this->_hasNodeChildren )
            {
                $tag .= self::$_NL . str_pad( '', $level, self::$_TAB );
            }
            
            $tag .= '</' . $this->_tagName . '>';
        }
        
        return $tag;
    }
    
    public function addChildNode( \XS\XHTML\Tag $child )
    {
        if( !isset( self::$_emptyTags[ $this->_tagName ] ) )
        {
            if( !isset( $this->_childrenByName[ $child->_tagName ] ) )
            {
                $this->_childrenByName[ $child->_tagName ]      = array();
                $this->_childrenCountByName[ $child->_tagName ] = 0;
            }
            
            $child->_parents[] = $this;
            
            $this->_children[]                           = $child;
            $this->_childrenByName[ $child->_tagName ][] = $child;
            
            $this->_childrenCountByName[ $child->_tagName ]++;
            $this->_childrenCount++;
            
            $this->_hasNodeChildren = true;
            
            return $child;
        }
        
        return NULL;
    }
    
    public function addTextData( $data )
    {
        if( $data instanceof self )
        {
            $this->addChildNode( $data );
        }
        else
        {
            $this->_children[] = ( string )$data;
            $this->_childrenCount++;
        }
    }
    
    public function asHtml()
    {
        return $this->_output( false );
    }
    
    public function asXml()
    {
        return $this->_output( true );
    }
    
    public function getParent( $parentIndex = 0 )
    {
        if( isset( $this->_parents[ $parentIndex ] ) )
        {
            return $this->_parents[ $parentIndex ];
        }
        
        return $this->_parents[ 0 ];
    }
    
    public function isEmpty()
    {
        return count( $this->_children ) == 0;
    }
}
