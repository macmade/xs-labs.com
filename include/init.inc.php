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

if( ( double )PHP_VERSION >= 5 )
{
    error_reporting( E_ALL | E_STRICT );
}
else
{
    trigger_error( 'PHP 5 is required', E_USER_ERROR );
}

define( '__ROOTDIR__', realpath( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . '..' ) );

if( !function_exists( 'spl_autoload_register' ) )
{
    throw new \Exception
    (
        'The SPL (Standard PHP Library) is required'
    );
}

if( !class_exists( '\SimpleXMLElement' ) )
{
    throw new \Exception
    (
        'The \SimpleXMLElement class is required'
    );
}

date_default_timezone_set( 'Europe/Zurich' );

require_once( __ROOTDIR__ . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'XS' . DIRECTORY_SEPARATOR . 'ClassManager.class.php' );

spl_autoload_register( array( '\XS\ClassManager', 'autoLoad' ) );
set_include_path( __ROOTDIR__ . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR );

$LAYOUT = \XS\Layout::getInstance();

$LAYOUT->setHeader( 'header.inc.php' );
$LAYOUT->setFooter( 'footer.inc.php' );

unset( $LAYOUT );
