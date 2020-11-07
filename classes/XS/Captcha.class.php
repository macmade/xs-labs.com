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

final class Captcha
{
    const RECAPTCHA_API_SERVER          = 'http://www.google.com/recaptcha/api';
    const RECAPTCHA_API_SECURE_SERVER   = 'https://www.google.com/recaptcha/api';
    const RECAPTCHA_VERIFY_SERVER       = 'www.google.com';
    
    private static  $_instance      = NULL;
    private         $_publicKey     = '6LeXgPsSAAAAAM55inPekSBO0-zwo88JX6ZprgSi';
    private         $_privateKey    = '6LeXgPsSAAAAAA4rltqyN6xmZJs__ewCCKH6oIhS';
    
    public static function getInstance()
    {
        if( !is_object( self::$_instance ) )
        {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    private function __construct()
    {
        \XS\Layout::getInstance()->addHeaderPart
        (
            __CLASS__,
            '<script src="https://www.google.com/recaptcha/api.js" type="text/javascript"></script>'
        );
    }
    
    public function __clone()
    {
        throw new \Exception( 'Class ' . __CLASS__ . ' cannot be cloned' );
    }
    
    public function __toString()
    {
        return ( string )( $this->getCapchta() );
    }
    
    public function getCapchta()
    {
        $div            = new \XS\XHTML\Tag( 'div' );
        $div[ 'class' ] = 'xs-captcha';
        
        if( empty( $this->_publicKey ) )
        {
            return $div;
        }
        
        $captcha                    = $div->div;
        $captcha[ 'class' ]         = 'g-recaptcha';
        $captcha[ 'data-sitekey' ]  = $this->_publicKey;
        
        return $div;
    }
    
    public function verifyCaptcha()
    {
        if( empty( $this->_privateKey ) )
        {
            return false;
        }
        
        $url = self::RECAPTCHA_API_SECURE_SERVER
             . '/siteverify?secret='
             . $this->_privateKey
             . '&response='
             . $_POST[ 'g-recaptcha-response' ]
             . '&remoteip='
             . $_SERVER[ 'REMOTE_ADDR' ];
        
        if( empty( $url ) )
        {
            return false;
        }
        
        $json = json_decode( file_get_contents( $url ) );
        
        if( !is_object( $json ) || !isset( $json->success ) )
        {
            return false;
        }
        
        return $json->success == true;
    }
}
