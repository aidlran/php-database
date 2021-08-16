<?php

	namespace aidlo;

	use Exception, DateTime;

	/**
	 * This class can be extended to create specialised classes to represent different "objects" in the database model.
	 * The derived class can have a static method to do something like <i>new self(PDOStatement->fetch())</i>, and then
	 * use the get methods provided or write custom ones for data retrieval & manipulation.
	 */
	abstract class DatabaseObject
	{
		/**
		 * @var array The associative array of retrieved database data.
		 */
		protected array $data;

		/**
		 * A DatabaseObject is constructed with data from PDO->fetch().
		 * @param array $data Data from PDO->fetch().
		 */
		protected function __construct(array $data) {
			$this->data = $data;
		}

		/**
		 * Retrieve data. Performs no checks or conversions, simply returns the raw data so long as it is set.
		 * @param string $key The key for the data.
		 * @return mixed The data.
		 */
		protected function get(string $key): mixed {
			return $this->data[$key] ?? null;
		}

		/**
		 * Get a string. Will check if the string is empty and return NULL in such a case.
		 * @param string $key The key for the data.
		 * @return string|null Returns the string or NULL if it is not set or if it is an empty string.
		 */
		protected function get_string(string $key): ?string {
			return $this->data[$key] == '' ? null : $this->data[$key];
		}

		/**
		 * Get a date, converting it to a DateTime object.
		 * @param string $key The key for the data.
		 * @return DateTime|null Returns the date as a DateTime object or NULL if it is not set or if an exception
		 * occurs.
		 */
		protected function get_date(string $key): ?DateTime {
			try {
				return ($d = $this->data[$key]) ? new DateTime($d) : null;
			}
			catch (Exception) {
				return null;
			}
		}
	}
