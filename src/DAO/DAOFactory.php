<?php

namespace MediaWiki\Extension\CollabPads\Backend\DAO;

use Exception;
use MediaWiki\Extension\CollabPads\Backend\IAuthorDAO;
use MediaWiki\Extension\CollabPads\Backend\ICollabSessionDAO;
use UnexpectedValueException;

class DAOFactory {

	/**
	 * @param array $config
	 * @return ICollabSessionDAO
	 * @throws Exception
	 */
	public static function createSessionDAO( array $config ): ICollabSessionDAO {
		if ( $config['db-type'] === 'mongo' ) {
			return new MongoDBCollabSessionDAO( $config );
		}

		throw new UnexpectedValueException( "Invalid database type '{$config['db-type']}'" );
	}

	/**
	 * @param array $config
	 * @return IAuthorDAO
	 * @throws Exception
	 */
	public static function createAuthorDAO( array $config ): IAuthorDAO {
		if ( $config['db-type'] === 'mongo' ) {
			return new MongoDBAuthorDAO( $config );
		}

		throw new UnexpectedValueException( "Invalid database type '{$config['db-type']}'" );
	}
}