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

class Mail
{
    protected $_to      = '';
    protected $_from    = '';
    protected $_subject = '';
    protected $_body    = '';
    
    public function __construct( $to = '', $subject = '', $body = '', $from = '' )
    {
        $this->_to      = ( string )$to;
        $this->_subject = ( string )$subject;
        $this->_body    = ( string )$body;
        $this->_from    = ( string )$from;
    }
    
    public function send()
    {
        mail( $this->_to, $this->_subject, $this->_body, 'From: ' . $this->_from );
    }
    
    public function getTo()
    {
        return $this->_to;
    }
    
    public function getFrom()
    {
        return $this->_from;
    }
    
    public function getSubject()
    {
        return $this->_subject;
    }
    
    public function getBody()
    {
        return $this->_body;
    }
    
    public function setTo( $email )
    {
        $this->_to = ( string )$email;
    }
    
    public function setFrom( $email )
    {
        $this->_from = ( string )$email;
    }
    
    public function setSubject( $subject )
    {
        $this->_subject = ( string )$subject;
    }
    
    public function setBody( $body )
    {
        $this->_body = ( string )$body;
    }
}
