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

final class Layout
{
    private static $_instance = NULL;
    private $_header          = '';
    private $_footer          = '';
    private $_disableHeader   = false;
    private $_disableFooter   = false;
    private $_headerParts     = array();
    
    private function __construct()
    {}
    
    public function __clone()
    {
        throw new \Exception( 'Class ' . __CLASS__ . ' cannot be cloned' );
    }
    
    public static function getInstance()
    {
        if( !is_object( self::$_instance ) )
        {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    public function getContent( $path )
    {
        if( !file_exists( $path ) )
        {
            throw new \XS\Layout\Exception
            (
                'The requested include file does not exist (path: ' . $path . ')',
                \XS\Layout\Exception::EXCEPTION_NO_INCLUDE_FILE
            );
        }
        
        if( !is_readable( $path ) )
        {
            throw new \XS\Layout\Exception
            (
                'The requested include file is not readable (path: ' . $path . ')',
                \XS\Layout\Exception::EXCEPTION_INCLUDE_FILE_NOT_READABLE
            );
        }
        
        ob_start();
        
        try
        {
            require( $path );
            
            $out = ob_get_contents();
            
        }
        catch( Exception $e )
        {
            ob_end_clean();
            
            throw $e;
        }
        
        ob_end_clean();
        
        return $out;
    }
    
    public function setHeader( $file )
    {
        $this->_header = __ROOTDIR__ . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . $file;
    }
    
    public function setFooter( $file )
    {
        $this->_footer = __ROOTDIR__ . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . $file;
    }
    
    public function getHeader()
    {
        return ( $this->_disableHeader ) ? '' : $this->getContent( $this->_header );
    }
    
    public function getFooter()
    {
        return ( $this->_disableFooter ) ? '' : $this->getContent( $this->_footer );
    }
    
    public function disableHeader()
    {
        $this->_disableHeader = true;
    }
    
    public function disableFooter()
    {
        $this->_disableFooter = true;
    }
    
    public function enableHeader()
    {
        $this->_disableHeader = false;
    }
    
    public function enableFooter()
    {
        $this->_disableFooter = false;
    }
    
    public function getHeaderParts()
    {
        return $this->_headerParts;
    }
    
    public function addHeaderPart( $id, $part )
    {
        $this->_headerParts[ $id ] = ( string )$part;
    }
}
