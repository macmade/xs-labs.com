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

final class ClassManager
{
    private static $_instance = NULL;
    private $_loadedClasses   = array();
    private $_classDir        = '';
    
    private function __construct()
    {
        $this->_classDir = realpath( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR;
        $dirIterator     = new \DirectoryIterator( $this->_classDir );
        
        foreach( $dirIterator as $file )
        {
            if( substr( $file, strlen( $file ) - 10 ) === '.class.php' )
            {
                continue;
            }
            
            if( !$file->isDir() )
            {
                continue;
            }
            
            if( substr( $file, 0, 1 ) === '.' )
            {
                continue;
            }
        }
    }
    
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
    
    public static function autoLoad( $className )
    {
        static $instance = NULL;
        
        if( !is_object( $instance ) )
        {
            $instance = self::getInstance();
        }
        
        if( substr( $className, 0, 3 ) === 'XS\\' )
        {
            $rootPkg = substr( $className, 3, strpos( $className, '\\', 3 ) - 3 );
            
            return $instance->_loadClass( $className );
        }
        
        return false;
    }
    
    private function _loadClass( $className )
    {
        $classPath = $this->_classDir . str_replace( '\\', DIRECTORY_SEPARATOR, substr( $className, 3 ) ) . '.class.php';
        
        if( file_exists( $classPath ) )
        {
            require_once( $classPath );
        
            if( !class_exists( $className ) )
            {
                $errorMsg = 'The class ' . $className . ' is not defined in file ' . $classPath;
                
                trigger_error( $errorMsg, E_USER_ERROR );
                
                print $errorMsg;
                exit();
            }
            
            $this->_loadedClasses[ $className ] = $classPath;
            
            return true;
        }
        
        return false;
    }
    
    public function getLoadedClasses()
    {
        return $this->_loadedClasses;
    }
}
