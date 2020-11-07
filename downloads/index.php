<?php

$app        = $_GET[ 'app' ];
$file       = $_GET[ 'file' ];
$dir        = realpath( dirname( __FILE__ ) );
$appDir     = $dir . DIRECTORY_SEPARATOR . $app;
$appFile    = $appDir . DIRECTORY_SEPARATOR . $file;

if( empty( $app ) || !is_dir( $appDir ) )
{
    header( 'Location: /' );
    exit();
}

if( !empty( $file ) )
{
    if( !file_exists( $appFile ) )
    {
        header( 'Location: /' );
        exit();
    }
    
    if( ini_get( 'zlib.output_compression' ) )
    {
        ini_set( 'zlib.output_compression', 'Off' );
    }
    
    header( 'Content-Type: application/octet-stream' );
    header( 'Content-Disposition: attachment; filename="' . $file . '"' );
    header( 'Content-Transfer-Encoding: binary' );
    header( 'Accept-Ranges: bytes' );
    header( 'Cache-Control: private' );
    header( 'Pragma: private' );
    header( 'Expires: ' . strftime( '%a, %e %b %Y %H:%M:%S GMT', 0 ) );
    header( 'Content-Length: ' . filesize( $appFile ) );
    
    ob_clean();
    flush();
    readfile( $appFile );
    
    exit();
}

header( 'Content-Type: text/plain' );

print '<?xml version="1.0" encoding="utf-8"?>' . chr( 10 );
print '<rss version="2.0" xmlns:sparkle="http://www.andymatuschak.org/xml-namespaces/sparkle" xmlns:dc="http://purl.org/dc/elements/1.1/">' . chr( 10 );
print '    <channel>' . chr( 10 );
print '        <title>' . $app . ' Changelog</title>' . chr( 10 );
print '        <link>http://www.xs-labs.com/downloads/' . $app . '</link>' . chr( 10 );
print '        <description>Latest ' . $app . ' Updates...</description>' . chr( 10 );
print '        <language>en</language>' . chr( 10 );

$dir    = new \DirectoryIterator( $appDir );
$files  = array();

foreach( $dir as $download )
{
    if( $download == '.' || $download == '..' )
    {
        continue;
    }
    
    $files[] = ( string )$download;
}

sort( $files );

$files = array_reverse( $files );

foreach( $files as $download )
{
    $dash1 = strpos( $download, '-' );
    $dash2 = strpos( $download, '-', $dash1 + 1 );
    $dot   = strpos( $download, '.', $dash2 );
    
    if( $dash1 == 0 || $dash2 == 0 || $dot == 0 )
    {
        continue;
    }
    
    $downloadName    = substr( $download, 0, $dash1 );
    $downloadVersion = substr( $download, $dash1 + 1, ( $dash2 - $dash1 ) - 1 );
    $downloadBuild   = substr( $download, $dash2 + 1, ( $dot   - $dash2 ) - 1 );
    
    if( $downloadName != $app )
    {
        continue;
    }
    
    print '        <item>' . chr( 10 );
    print '            <title>' . $app . ' ' . $downloadVersion . ' - ' . $downloadBuild . '</title>' . chr( 10 );
    print '            <description></description>' . chr( 10 );
    print '            <pubDate>Tue, 07 May 2013 00:30:00 +0000</pubDate>' . chr( 10 );
    print '            <enclosure url="' . ( isset( $_SERVER[ 'HTTPS' ] ) ? 'https' : 'http' ) . '://' . $_SERVER[ 'HTTP_HOST' ] . str_replace( '?' . $_SERVER[ 'QUERY_STRING' ], '', $_SERVER[ 'REQUEST_URI' ] ) . $app . '/' . $download . '" sparkle:version="' . $downloadBuild . '" sparkle:shortVersionString="' . $downloadVersion . '" type="application/octet-stream" />' . chr( 10 );
    print '        </item>' . chr( 10 );
}

print '    </channel>' . chr( 10 );
print '</rss>' . chr( 10 );

exit();
