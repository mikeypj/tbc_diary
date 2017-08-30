<?php
//////////////////////////////////////////////////////////////////////////
//
//  File:		db_mysql.php
//  Desc:		Database access object db_mysql
//  Author:		Rob Curle
//  Date:		20 Nov 2000
//  Notes:		Updated 10 Oct 2002, Rob Curle:
//				added query recordset functions.
//
///////////////////////////////////////////////////////////////////////////

if(!defined("DB_MYSQL")) {
	define("DB_MYSQL",1);
			
	class db_mysql {
		var $lid;           	// Link ID for database connection
		var $qid;				// Query ID for current query
		var $row;				// Current row in query result set
		var $record;			// Current row record data
		var $error;				// Error Message
		var $errno;				// Error Number
		var $row_vars;	
		
		// Make a persistent connection to the DB, and returns DB lid //
		function connect() { 
			if ($this->lid == 0) {
					$this->lid = mysql_pconnect(DB_HOST, DB_USER, DB_PWD); 
				if (!$this->lid) {
					$this->mysql_error("connect(" . DB_HOST . "," . DB_USER . ",PASSWORD)  failed.");
				} 
      
	  			if (!@mysql_select_db(DB_NAME,$this->lid)) {
					$this->mysql_error("Cannot connect to database ".DB_NAME);
					return 0;
      			}
				
				if ($this->lid) {
					$this->query("SET SQL_BIG_SELECTS = 1");
				}
    		}
    		return $this->lid;
  		}
	
		// Connects to DB, runs query, and sets up the query id for the class. //
 		function query($q) {
			if (empty($q)) {
				return 0;
			}
    
			if (!$this->connect()) {
				return 0; 
			}
    
			if ($this->qid) {
				@mysql_free_result($this->qid);
				$this->qid = 0;
			}
    
			$this->qid = @mysql_query($q, $this->lid);
			$this->row   = 0;
			$this->errno = mysql_errno();
			$this->error = mysql_error();
			
			if (!$this->qid) {
				$this->mysql_error("Invalid SQL: ".$q);
			}
			
			return $this->qid;
		}

		// Return next record in result set //
		function next_record() {
			if (!$this->qid) {
				$this->mysql_error("next_record called with no query pending.");
				return 0;
			}
			
			$this->record = @mysql_fetch_array($this->qid, MYSQL_ASSOC);
			$this->row   += 1;
			$this->errno  = mysql_errno();
			$this->error  = mysql_error();
			
			$stat = is_array($this->record);
			return $stat;
		}	
		
		// A function to return to the start of the recordset //
		function rewind_rs() {
			$this->row = 0;
			$res = @mysql_data_seek ($this->qid, $this->row);
			return $res;
		}
  
  		// Return field Value //
 		function f($field_name) {
			return stripslashes($this->record[$field_name]);
		}
		
		// Return field value with " ' & < > substituted for html entities //
		function f_ext($field_name) {
			$ext_chars = array("\"" => "&rdquo;", "'" => "&rsquo;", "&" => "&amp;", "<" => "&lt;", ">" => "&gt;");
			
			$data = stripslashes($this->record[$field_name]);
			return strtr ($data, $ext_chars);
		}

		// Print field value //
		function p($field_name) {
			print stripslashes($this->record[$field_name]);
		}                      

		// Returns the number of rows in query //
		function num_rows() {
			if ($this->lid) {
				return @mysql_numrows($this->qid);
			}
			else {
				return 0;
			}
		}
		
		// Returns an entire row of data, with the slashes removed //
		function row() {
			foreach ($this->record as $key => $value) {
				$this->record[$key] = stripslashes($value);
			}
			
			return $this->record;
		}
		
		// Retrieves the last insert ID from an AUTO_INCREMENTED insert 
		function insert_id() {
			return mysql_insert_id($this->lid);
		}
		
		// A function that takes a query, and returns a complete recordset as a 2 dimensional array //
		function query_recordset($q) {
			$this->query($q);

			while ($this->next_record()) {
				foreach ($this->record as $key => $value) {
					$recordset[$this->row][$key] = stripslashes($value);
				}
			}
			
			@mysql_free_result($this->qid);
			$this->qid = 0;
			
			return $recordset;
		}
		
		// A function that takes a query (which expects 1 row as a result), and returns a single row. //
		function query_row($q) {
			$this->query($q);
			$this->next_record();
			
			foreach ($this->record as $key => $value) {
				$recordset[$key] = stripslashes($value);
			}
			
			@mysql_free_result($this->qid);
			$this->qid = 0;
			
			return $recordset;
		}
		
		// mySQL error message display, depending on if DEBUG define is set //
		function mysql_error($msg) {
			if (DB_DEBUG) {
				$this->error = @mysql_error($this->lid);
				$this->errno = @mysql_errno($this->lid);
				print("<h1>MySQL Error</h1><br>\n");
				printf("%s:<br>%s (%s)<br>\n", $msg, $this->errno, $this->error);
			
			}
			else {
				echo "<h1>Sorry</h1>\n";
				echo "There has been a problem on the database, please try later.\n";
			}
			exit;
		}
	}
}
?>
