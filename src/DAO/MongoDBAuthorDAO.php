<?php

namespace MediaWiki\Extension\CollabPads\Backend\DAO;

use MediaWiki\Extension\CollabPads\Backend\IAuthorDAO;

class MongoDBAuthorDAO extends MongoDBDAOBase implements IAuthorDAO {

	/**
	 * @inheritDoc
	 */
	protected function getCollectionName(): string {
		return 'authors';
	}

	/**
	 * @inheritDoc
	 */
	public function setNewAuthor( string $authorName ) {
		$this->collection->insertOne( [
			'a_sessions' => [],
			'a_id' => $this->collection->count() + 1,
			'a_name' => $authorName
		] );
	}

	/**
	 * @inheritDoc
	 */
	public function setNewConnection( int $connectionId, int $sessionId, int $authorId ) {
		$this->collection->updateOne(
			[ 'a_id' => $authorId ],
			[ '$push' => [
				'a_sessions' => [
					'c_id' => $connectionId,
					's_id' => $sessionId
				]
			] ]
		);
	}

	/**
	 * @inheritDoc
	 */
	public function deleteConnection( int $connectionId, int $authorId ) {
		$this->collection->updateOne(
			[ 'a_id' => $authorId, 'a_sessions.c_id' => $connectionId ],
			[ '$pull' => [ 'a_sessions' => [ 'c_id' => $connectionId ] ] ]
		);
	}

	/**
	 * @inheritDoc
	 */
	public function getSessionByConnection( int $connectionId ): int {
		$result = $this->collection->find(
			[ 'a_sessions.c_id' => $connectionId ],
			[ 'projection' => [ 'a_sessions.s_id.$' => 1 ] ]
		);

		foreach ( $result as $row ) {
			return $row["a_sessions"][0]["s_id"];
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getAuthorByConnection( int $connectionId ) {
		$result = $this->collection->find(
			[ 'a_sessions.c_id' => $connectionId ]
		);

		foreach ( $result as $row ) {
			return $row;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getConnectionByName( int $sessionId, string $authorName ) {
		$result = $this->collection->find(
			[ 'a_sessions.s_id' => $sessionId, 'a_name' => $authorName ],
			[ 'projection' => [ 'a_sessions.c_id' => 1 ] ]
		);

		$output = [];
		foreach ( $result as $row ) {
			foreach ( $row['a_sessions'] as $session ) {
				$output[] = $session['c_id'];
			}
			return $output;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getAuthorByName( string $authorName ) {
		$result = $this->collection->find(
			[ 'a_name' => $authorName ]
		);

		foreach ( $result as $row ) {
			return $row;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getAuthorById( int $authorId ) {
		$result = $this->collection->find(
			[ 'a_id' => $authorId ]
		);

		foreach ( $result as $row ) {
			return $row;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function cleanConnections() {
		$this->collection->updateMany(
			[],
			[ '$set' => [ 'a_sessions' => [] ] ]
		);
	}
}
