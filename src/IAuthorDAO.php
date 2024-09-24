<?php

namespace MediaWiki\Extension\CollabPads\Backend;

interface IAuthorDAO {

	/**
	 * @param string $authorName
	 */
	public function setNewAuthor( string $authorName );

	/**
	 * @param int $connectionId
	 * @param int $sessionId
	 * @param int $authorId
	 */
	public function setNewConnection( int $connectionId, int $sessionId, int $authorId );

	/**
	 * @param int $connectionId
	 * @param int $authorId
	 */
	public function deleteConnection( int $connectionId, int $authorId );

	/**
	 * @param int $connectionId
	 * @return int
	 */
	public function getSessionByConnection( int $connectionId ): int;

	/**
	 * @param int $connectionId
	 * @return array
	 */
	public function getAuthorByConnection( int $connectionId );

	/**
	 * @param int $sessionId
	 * @param string $authorName
	 * @return array
	 */
	public function getConnectionByName( int $sessionId, string $authorName );

	/**
	 * @param string $authorName
	 * @return array
	 */
	public function getAuthorByName( string $authorName );

	/**
	 * @param int $authorId
	 * @return array
	 */
	public function getAuthorById( int $authorId );

	/**
	 * @return void
	 */
	public function cleanConnections();
}