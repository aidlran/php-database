<?php /** @noinspection PhpUnused */

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
		public static function getDb(string $host, string $username, string $password, string $db_name): Database {
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
		public static function getPdo(string $host, string $username, string $password, string $db_name): ?PDO {
			return Database::getDb($host, $username, $password, $db_name)->getConnection();
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
		public function getConnection(): ?PDO {
			return $this->active ? $this->connection : null;
		}


		/*
		 * Get an array of all possible enum values from a table column.
		 *
		 * WARNING: Untested
		 * WARNING: NOT SAFE FROM SQL INJECTION
		 *
		 * FIXME
		 *
		 * @param string $table Name of the table.
		 * @param string $column Name of the column.
		 * @return string[]|null
		 /
		public function getEnumValues(string $table, string $column): ?array {

			// Ensure connection was initialised
			if (!$this->get_connection())
				return null;

			// Query
			try {
				if (($a = $this->connection->prepare("SHOW COLUMNS FROM `$table` LIKE '$column'"))
					&& $a->execute()
					&& ($a = $a->fetch())
					&& isset($a['Type']))
				{
					$a = $a['Type'];
					preg_match('/enum\((.*)\)$/', $a, $a);
					$a = explode(',', $a[1]);
					foreach ($a as &$b)
						$b = substr($b, 1, -1);
					return $a;
				}
			} catch (Exception) {return null;}
			return null;
		}

		private static function generate_select_clause(array $columns): ?string
		{
			$string = 'SELECT';
			$once = false;
			$increment = 0;

			foreach ($columns as $key => $value)
			{
				$substring = $once ? ', ' : ' ';

				if ($key == $increment)
					$increment++;
				else if (preg_match(self::REGEX_COLUMN, $key))
					$substring .= "$key AS ";
				else continue;

				if (preg_match(self::REGEX_COLUMN, $value)) {
					$string .= $substring . $value;
					$once = true;
				}
			}

			return $once ? $string : null;
		}

		/**
		 * TODO: joins
		 * TODO: where clause
		 * TODO: order by
		 * @param string[] $select_columns
		 * @param string|string[] $table
		 * @param array|null $other_parameters Optional parameters e.g. ['limit' => int]
		 * @return PDOStatement|false
		 /
		public function select(array $select_columns, string|array $table, array $other_parameters = null): PDOStatement|false
		{
			if (!$this->get_connection() || !($select_clause = self::generate_select_clause($select_columns)) || (is_string($table) && !preg_match(self::REGEX_TABLE, $table)))
				return false;

			if (is_array($table))
				foreach ($table as $key => $value) {
					if (preg_match(self::REGEX_TABLE, "$key$value"))
						$table = "$key AS $value";
					else return false;
					break;
				}

			$query = "$select_clause FROM $table";

			if (preg_match('/^\d+$/', $other_parameters['limit']) && $other_parameters['limit'] > 0)
				$query .= ' LIMIT ' . $other_parameters['limit'];

			return $this->get_connection()->query($query);
		}
		*/
	}
