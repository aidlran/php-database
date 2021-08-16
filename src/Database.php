<?php

	namespace aidlo;

	use PDO, Exception;

	include_once __DIR__ . '/DatabaseObject.php';

	/**
	 * A class for managing database connections using PDO.
	 */
	final class Database {

		/**
		 * @var Database[] Associative array of instances of this class.
		 */
		private static array $databases = [];

		private PDO $connection;
		private bool $active = false;

		/**
		 * Retrieve an instance of this class for the corresponding database. Instances are managed by this class, so
		 * the connection will remain persistent between calls.
		 * @param string $host Your MySQL host.
		 * @param string $username Your MySQL username.
		 * @param string $password Your MySQL password.
		 * @param string $db_name The name of the desired database.
		 * @return Database An instance of this class.
		 */
		public static function get_db(string $host, string $username, string $password, string $db_name): Database {
			return Database::$databases[$db_name] ?? self::$databases[$db_name] = new Database($host, $username, $password, $db_name);
		}

		/**
		 * Retrieve the PDO connection for the corresponding database. Instances are managed by this class, so the
		 * connection will remain persistent between calls.
		 * @param string $host Your MySQL host.
		 * @param string $username Your MySQL username.
		 * @param string $password Your MySQL password.
		 * @param string $db_name The name of the desired database.
		 * @return PDO|null Returns the PDO object or NULL if initialisation failed.
		 */
		public static function get_pdo(string $host, string $username, string $password, string $db_name): ?PDO {
			return Database::get_db($host, $username, $password, $db_name)->get_connection();
		}

		/**
		 * Close the connection to a database, meaning that a subsequent call to get_db or get_pdo will initialise a new
		 * connection. Note that this will automatically happen upon termination of the script, however this
		 * functionality may be needed in certain use cases.
		 * @param string $db_name The name of the database of which to close the connection.
		 */
		public static function close(string $db_name) {
			unset(Database::$databases[$db_name]->connection);
			unset(Database::$databases[$db_name]);
		}

		/**
		 * @param string $host Your MySQL host.
		 * @param string $username Your MySQL username.
		 * @param string $password Your MySQL password.
		 * @param string $db_name The name of the desired database.
		 */
		private function __construct(string $host, string $username, string $password, string $db_name) {
			try {
				$this->connection = new PDO(
					"mysql:host=$host;
					dbname=$db_name;
					charset=utf8mb4",
					$username,
					$password,
					[
						PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
						PDO::ATTR_PERSISTENT => false
					]
				);
			} catch (Exception) {return;}
			$this->active = true;
		}

		/**
		 * Retrieve the PDO object for this database.
		 * @return PDO|null Returns the PDO object or NULL if initialisation failed.
		 */
		public function get_connection(): ?PDO {
			return $this->active ? $this->connection : null;
		}
	}
