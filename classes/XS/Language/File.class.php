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

namespace XS\Language;

final class File
{
    private static $_instances     = array();
    private static $_lang          = '';
    private static $_hasStaticVars = false;
    private $_labels               = NULL;
    
    private function __construct( $className )
    {
        if( self::$_hasStaticVars === false )
        {
            self::_setStaticVars();
        }
        
        $className = str_replace( '\\', '_', $className );
        
        $path = __ROOTDIR__
              . DIRECTORY_SEPARATOR
              . 'l10n'
              . DIRECTORY_SEPARATOR
              . self::$_lang
              . DIRECTORY_SEPARATOR
              . $className
              . '.xml';
        
        if( !file_exists( $path ) )
        {
            throw new \XS\Language\File\Exception
            (
                'The requested language file does not exist (path: ' . $path . ')',
                \XS\Language\File\Exception::EXCEPTION_NO_LANGUAGE_FILE
            );
        }
        
        $this->_labels = simplexml_load_file( $path );
    }
    
    public function __clone()
    {
        throw new \Exception( 'Class ' . __CLASS__ . ' cannot be cloned' );
    }
    
    public function __get( $name )
    {
        $name = ( string )$name;
        
        if( isset( $this->_labels->$name ) )
        {
            return $this->_labels->$name;
        }
        
        return '[L10N LABEL: ' . $name . ']';
    }
    
    public function _isset( $name )
    {
        $name = ( string )$name;
        
        return isset( $this->_labels->$name );
    }
    
    private static function _setStaticVars()
    {
        self::$_lang          = \XS\Menu::getInstance()->getLanguage();
        self::$_hasStaticVars = true;
    }
    
    public static function getInstance( $className )
    {
        $className = ( string )$className;
        
        if( !isset( self::$_instances[ $className ] ) )
        {
            self::$_instances[ $className ] = new self( $className );
        }
        
        return self::$_instances[ $className ];
    }
}
