<?php

if( isset( $_GET[ 'download' ] ) )
{
    header( 'Location: https://s3.amazonaws.com/xs-labs/'. str_replace( ' ', '%2B', $_GET[ 'download' ] ) );
}

exit();
