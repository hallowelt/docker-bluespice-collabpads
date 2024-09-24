<?php

require_once dirname( __DIR__ ) . '/vendor/autoload.php';
$config = require_once dirname( __DIR__ ) . '/config.php';

use GuzzleHttp\Client;
use MediaWiki\Extension\CollabPads\Backend\DAO\DAOFactory;
use MediaWiki\Extension\CollabPads\Backend\Socket;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

switch ( strtolower( $config['log-level'] ) ) {
	case 'debug':
		$logLevel = Logger::DEBUG;
		break;
	case 'notice':
		$logLevel = Logger::NOTICE;
		break;
	case 'info':
		$logLevel = Logger::INFO;
		break;
	case 'warn':
		$logLevel = Logger::WARNING;
		break;
	case 'error':
		$logLevel = Logger::ERROR;
		break;
	default:
		$logLevel = Logger::WARNING;
}
$logger = new Logger( 'collabpads-backend' );
$logger->setHandlers( [ new StreamHandler( 'php://stdout', $logLevel ) ] );
$logger->info( "Log level: $logLevel" );

$port = $config['port'] ?: 8081;
$logger->info( "Port: $port" );
$logger->debug( 'MongoDB name:' . $config['db-name'] );

$clientOptions = $config['http-client-options'] ?: [];
$logger->debug( 'HTTP Client Options: ' . json_encode( $clientOptions ) );
$httpClient = new Client( $clientOptions );

$retryTime = 5;
for ( $x = 0; ; $x++ ) {
	try {
		$authorDAO = DAOFactory::createAuthorDAO( $config );
		$sessionDAO = DAOFactory::createSessionDAO( $config );

		$server = IoServer::factory(
			new HttpServer(
				new WsServer(
					new Socket( $config, $httpClient, $authorDAO, $sessionDAO, $logger )
				)
			),
			$port,
			$config['request-ip']
		);

		$logger->info( "System: starting server... count = $x" );
		$server->run();
	} catch ( Throwable $e ) {
		$logger->error( 'Error: ' . $e->getMessage() . " Retrying in $retryTime seconds..." );
		sleep( $retryTime );
	}
}
