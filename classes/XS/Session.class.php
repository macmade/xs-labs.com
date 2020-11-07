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

final class Session
{
    const SESSION_ID = 'XSLABS-SESSION';
    
    private static $_instance = NULL;
    private $_started         = '';
    
    private function __construct()
    {}
    
    public function __clone()
    {
        throw new \XS\Singleton\Exception
        (
            'Class ' . __CLASS__ . ' cannot be cloned',
            \XS\Singleton\Exception::EXCEPTION_CLONE
        );
    }
    
    public function __get( $name )
    {
        return $this->getData( $name );
    }
    
    public function __set( $name, $value )
    {
        $this->setData( $name, $value );
    }
    
    public function __isset( $name )
    {
        $this->start();
        
        $isset = isset( $_SESSION[ $name ] );
        
        $this->close();
        
        return $isset;
    }
    
    public function __unset( $name )
    {
        $this->start();
        
        unset( $_SESSION[ $name ] );
        
        $this->close();
    }
    
    public static function getInstance()
    {
        if( !is_object( self::$_instance ) )
        {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    public function start()
    {
        if( !$this->_started )
        {
            session_set_cookie_params( 0, '/' );
            session_name( self::SESSION_ID );
            session_start();
            
            $this->_started = true;
        }
    }
    
    public function close()
    {
        if( $this->_started )
        {
            session_write_close();
            
            $this->_started = false;
            $_SESSION       = array();
        }
    }
    
    public function destroy()
    {
        if( $this->_started )
        {
            session_destroy();
            
            $this->_started = false;
            $_SESSION       = array();
        }
    }
    
    public function getData( $key )
    {
        $this->start();
        
        if( isset( $_SESSION[ $key ] ) )
        {
            return $_SESSION[ $key ];
        }
        else
        {
            return false;
        }
        
        $this->close();
    }
    
    public function setData( $key, $value )
    {
        $this->start();
        
        $_SESSION[ $key ] = $value;
        
        $this->close();
    }
    
    public function getId()
    {
        $this->start();
        
        $id = session_id();
        
        $this->close();
        
        return $id;
    }
}
