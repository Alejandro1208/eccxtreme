<?php
class Field
{
	private $value;
	private $strType;
	private $strName;
	private $strAlias;

	public function __construct($value, $strType, $strName)
	{
		$this->value   = $value;
		$this->strType = $strType;
		$this->strName = $strName;
	}
	
	public function Value(){return($this->value);}
	public function Type(){return($this->strType);}
	public function Name(){return($this->strName);}
}

class Rs
{
	private $strSql;
	private $intIndex;
	private $intPageSize;
	private $intAbsolutePage;
	private $intFromIndex;
	private $intToIndex;
	private $blnEOF;
	private $conn;
	private $query;
	private $row;
	private $arrAlias;
	
	public function __construct($conn, $strSql, $intPageSize, $intAbsolutePage)
	{
		$this->conn 	       = $conn;
		$this->strSql 	       = $strSql;
		$this->intIndex   	   = 0;
		$this->intPageSize 	   = $intPageSize;
		$this->intAbsolutePage = $intAbsolutePage;
		$this->intFromIndex    = 0;
		$this->intToIndex 	   = 0;		

		$this->Query();
		$this->FromIndex();
		$this->ToIndex();
		$this->MoveNext();

		# echo($this->intFromIndex . " " . $this->intToIndex . " " . $intAbsolutePage . "<br/>");
		
		$this->Alias();
	}

	private function Query()
	{
		if(!$this->query = @mysql_query($this->strSql, $this->conn))
			subError('dbcommand.class.php', 'class Rs|private function Query()|if(!$this->query = mysql_query($this->strSql, $this->conn))|strSql = ' . $this->strSql);
	}

	private function FromIndex()
	{
		if($this->intPageSize <= 1)
		{
			# Sin paginar.
			$this->intFromIndex = 1;
		}
		else
		{
			# Paginado.
			if($this->intAbsolutePage == 1)
				$this->intFromIndex = 1;
			else
			{
				$this->intFromIndex = ($this->intPageSize * ($this->intAbsolutePage - 1)) + 1;
				mysql_data_seek($this->query, $this->intFromIndex - 1);
			}
		}
	}

	private function ToIndex()
	{
		if($this->intPageSize <= 1)
		{
			# Sin paginar.
			$this->intToIndex = $this->RecordCount();
		}
		else
		{
			# Paginado.
			if($this->intAbsolutePage == 1)
				$this->intToIndex = $this->intPageSize;
			else
				$this->intToIndex = ($this->FromIndex() + $this->intPageSize) - 1;
		}
	}

	private function Alias()
	{
		$strSql		  = $this->strSql;
		$strSql	   	  = str_replace(chr(9),  "", $strSql);
		$strSql	   	  = str_replace(chr(32), "", $strSql);
		$strSql	   	  = str_replace(chr(10), "", $strSql);
		$strSql_lower = strtolower($strSql);
		$intFrom      = strpos($strSql_lower, "from");
		$str	      = substr($strSql, 6, $intFrom - 6);
		$str	      = str_replace("AS", "as", $str);
		$arrFields    = fncSplit($str, ",");

		foreach($arrFields as $strValue)
		{
			$arr = fncSplit($strValue, "as");
			if(count($arr) == 1)
				$this->arrAlias[$arr[0]] = $arr[0];
			else
				$this->arrAlias[$arr[0]] = $arr[1];
		}
	}

	private function GetIndexByValue($strValue)
	{
		$intFields = $this->FieldsCount();
		for($j = 0; $j <= $intFields - 1; $j++)
		{
			if(mysql_field_name($this->query, $j) == $strValue)
				return($j);
		}
		return(-1);
	}
	private function GetIndexByAlias($strValue)
	{	
		$j = 0;
		foreach($this->arrAlias as $key => $value)
		{
			if($key == $strValue)
				return($j);			
			$j++;
		}
		return(-1);
	}
	
	public function MoveNext()
	{
		if($this->intPageSize <= 1)
		{
			# Sin paginar
			if($this->row = mysql_fetch_array($this->query))			
			{
				$this->intIndex ++;
				$this->blnEOF = false;
			}
			else
				$this->blnEOF = true;
		}
		else
		{
			# Paginado.
			if(($this->intIndex <= $this->intToIndex - 1) && ($this->row = mysql_fetch_array($this->query)))
			{
				$this->intIndex ++;
				$this->blnEOF = false;				
			}
			else
				$this->blnEOF = true;
		}
	}

	public function PageCount()
	{
		$intRecordCount = $this->RecordCount();
		$intPageSize    = $this->intPageSize;

		if($this->intPageSize == 0)
			return($intRecordCount);
		else
		{
			$intResultado = (int) ($intRecordCount / $intPageSize);
			$intResto     = $intRecordCount % $intPageSize;
			if($intResto == 0)
				return($intResultado);
			else
				return($intResultado + 1); 
		}
	}

	public function FieldsCount(){return(mysql_num_fields($this->query));}
	public function RecordCount(){return(mysql_num_rows($this->query));}

	public function ExistField($sName)
	{
		$intFields = mysql_num_fields($this->query);
		for($j = 0; $j <= $intFields - 1; $j++)
		{
			if(strtolower(mysql_field_name($this->query, $j)) == strtolower($sName))
				return(true);
		}
		return(false);
	}

	public function Field($key)
	{
		if(($this->row) && (array_key_exists($key, $this->row)))
		{
			$value     = $this->row[$key];
			$blnIsNull = fncIsEmptyOrNull($value);

			if(is_numeric($key))
			{
				$intIndice = $key;
				if(($intIndice < 0) || ($intIndice > $this->FieldsCount()))
					subError('dbcommand.class.php', 'Class Rs|private function Field($key)|$intIndice = $key;|$key: "' . $key . '"'); 
			}
			else
			{
				$intIndice = $this->GetIndexByValue($key);
				if($intIndice == -1)
					subError('dbcommand.class.php', 'Class Rs|private function Field($key)|$intIndice = $this->GetIndexByValue($key);|$key: "' . $key . '"');
			}			
		}
		else
		{
			$blnAlias = true;
 
			# Puede ser que el $key sea el alias.
			if(array_key_exists($key, $this->arrAlias))
			{
				$value	   = $this->row[$this->arrAlias[$key]];
				$intIndice = $this->GetIndexByAlias($key);

				if($intIndice == -1)
					subError('dbcommand.class.php', 'Class Rs|private function Field($key)|$intIndice = $this->GetIndexByValue($key);|$key: "' . $key . '"');			
			}
			else
				subError('dbcommand.class.php', 'Class Rs|private function Field($key)|No se encontro un campo con el nombre "' . $key . '"');				
		}
	
		$strType = mysql_field_type($this->query, $intIndice);
		$intLen  = mysql_field_len($this->query,  $intIndice);
		$strName = mysql_field_name($this->query, $intIndice);

		switch($strType) 
		{
			/*
			 * @TODO: Ver Porque el text lo toma como blob.
			 */
			# TINYBLOB, MEDIUMBLOB, LONGBLOB, BLOB: blob.	
			case "blob":
			{
				settype($value, "string");
				$value = ($blnIsNull)? null : $value; 		
				break;
			}
			# String yyyy-mm-dd en MySql.			
			# TIMESTAMP: timestamp.	
			# DATE: date.
			# TIME: time.
			# DATETIME: datetime.			
			case "timestamp":
			case "date":
			case "time":
			case "datetime":
			{
				settype($value, "string");							
				break;
			}
			# TINYINT, SMALLINT, MEDIUMINT, INT, INTEGER, BIGINT: int.
			case "int":
			{
				settype($value, ($intLen == 1)? "boolean" : "integer");								
				break;
			}
			# FLOAT, DOUBLE, DECIMAL, NUMERIC: real.
			case "real":
			{
				settype($value, "double");								
				break;
			}
			# CHAR, VARCHAR, TINYTEXT, TEXT, MEDIUMTEXT, LONGTEXT, ENUM, SET: string.
			case "string":
			{
				settype($value, "string");								
				break;
			}						
			default:
			{
				if(!@settype($value, $strType))
					subError('dbcommand.class.php', 'Class Rs|private function Field($key)|No existe un tipo "' . $strType. '"|$strName = ' . $strName . '|$strValue=' . $strValue);
				break;
			}
		}		
		
		$value = ($blnIsNull)? null : $value; 
		# echo($strName . " - " . $strType . "(" . $intLen . ") - " . $value . " - " . (($blnIsNull)? "null" : "") . " - " . (($blnAlias)?  "Alias" : "") . " <br/>");
		$objField = new Field($value, $strType, $strName);
		return($objField);
	}

	public function PageSize($intPageSize){$this->intPageSize = $intPageSize;}
	
	public function AbsolutePage($intAbsolutePage)
	{
		if($this->intPageSize > 0)
		{
			# Me posiciono.
			$this->intAbsolutePage = $intAbsolutePage;			
			$this->intIndex 	   = 0;

			$this->Query();
			$this->FromIndex();
			$this->ToIndex();
			
			while(($this->intIndex <= $this->intFromIndex - 1) && ($this->row = mysql_fetch_array($this->query)))
			{
				$this->intIndex ++;
			}
		}
		else
			$this->intAbsolutePage = 0;
	}

	public function EOF(){return($this->blnEOF);}
	
	/*
	function StringToAscii($s)
	{
		$intLen = strlen($s);
		for($j = 0; $j <= $intLen - 1; $j ++)
		{
			$strChar = substr($s, $j, 1);
			echo($strChar . "(" . ord($strChar) . ")-");	
		}
	}
	*/	
}





class Command
{
	private $strPath;
	private $blnOpen;
	private $objXml;
	
	public function __construct($strPath)
	{
		$this->strPath = $strPath;
		$this->objXml  = null;
	}
	
	private function Open()
	{
		if(is_null($this->objXml))
		{
			if(!$this->objXml = @simplexml_load_file($this->strPath))
				subError('dbcommand.clas.php', 'Class Command|private function Open()|No se encontro el archivo "' . $this->strPath . '"');				
		}
	}

	public function Get($strName, $arrParameters = null)
	{
		$this->Open();
		$objCommands = $this->objXml->command;
		foreach($objCommands as $objCommand)
		{
			if($strName == $objCommand["name"])
			{
				$strSql = fncXmlEntitiesToString($objCommand);
				if(is_array($arrParameters))
				{
					foreach($arrParameters as $strKey => $val)
					{
						switch(gettype($val)) 
						{
							case "boolean":
							{
								$strValue = (fncIsEmptyOrNull($val))? 0 : 1;								
								break;
							}							
							case "string":
							{
                                                                $val = mysql_real_escape_string($val);
								$strValue = (fncIsEmptyOrNull($val))? "''" : "'" . $val . "'";								
								break;
							}					
							default:
							{
								$strValue = (fncIsEmptyOrNull($val))? "null" : $val;
								break;
							}
						}													
						
						# echo($strValue . "]");
						# echo("<br/>");
						
						if(strpos($strSql, "{" . $strKey . "}") > 0)
							$strSql = str_replace("{" . $strKey . "}", $strValue, $strSql);
						else
						{
							if(strpos($strSql, "((" . $strKey . "))") > 0)
								$strSql = str_replace("((" . $strKey . "))", $val, $strSql);
							else
								subError('dbcommand.clas.php', 'Class Command|public function Get($strName, $arrParameters = null)|$strName = ' . $strName . '|No se encontro ningun parametro con el nombre "' . $strKey . '"');
						}
					}
				}
				return(trim($strSql));	
			}
		}
		subError('dbcommand.clas.php', 'Class Command|public function Get($strName, $arrParameters = null)|No se encontro ningun comando con el nombre "' . $strName . '"');
		return(false);
	}	
}





class DBCommand
{
	private $strHost;
	private $strUser;
	private $strPass;
	private $strDb;
	private $strPathDescriptor;
	private $strSql;
	private $objConn;

	
	
	
	
	# Constructor.
	public function __construct($strHost, $strUser, $strPass, $strDb, $strPathDescriptor)
	{
		$this->strHost 			 = $strHost;
		$this->strUser 			 = $strUser;
		$this->strPass 			 = $strPass;
		$this->strDb   			 = $strDb;
		$this->strPathDescriptor = $strPathDescriptor;
		$this->strSql		     = "";
		$this->objConn 			 = null;
	}

	# Constructor.
	public function __destruct()
	{
		$this->objConn = null;
	}

	
	
	
	
	# Private.
	private function IsQueryOrStoredProcedure($strParameter1, $arrParameter2)
	{
		if(is_array($arrParameter2))
			return("s"); # SP.
		else
		{
			$sParam1 = $strParameter1;
			$sParam1 = ltrim($sParam1);
			$sParam1 = str_replace("\n", "", $sParam1);
			$sParam1 = str_replace("\t", "", $sParam1);			
			$sParam1 = substr($sParam1, 0, 6);
			$sParam1 = strtolower($sParam1);

			if
			(
				($sParam1 == "select") ||
				($sParam1 == "insert") ||
				($sParam1 == "update") ||
				($sParam1 == "delete")
			)
				return("q"); # Query.
			else
				return("s"); # SP.
		}
	}

	private function OpenConnection()
	{
		if(is_null($this->objConn))
		{
			if(@$this->objConn = mysql_connect($this->strHost, $this->strUser, $this->strPass))
			{
                            //seteo UTF8 como encoding para todas las consultas
                            mysql_query("SET NAMES 'utf8'");
                            if(!@mysql_select_db($this->strDb, $this->objConn))
					subError('dbcommand.class.php', 'Class CDBCommand|private function OpenConnection()|if(!@mysql_select_db($this->strDb, $this->objConn))');
			}
			else 
				subError('dbcommand.class.php', 'Class CDBCommand|private function OpenConnection()|if(!@$this->objConn = mysql_connect($this->strHost, $this->strUser, $this->strPass)|$this->strHost: ' . $this->strHost . '|this->strUser: ' . $this->strUser . '|$this->strPass: ' . $this->strPass);
		}
	}
	
	private function CloseConnection()
	{
		if(!is_null($this->objConn))
		{
			mysql_close($this->objConn);
			$this->objConn = null;
		}
	}	

	
	
	
	
	# Public.
	public function Command($strCommandText, $arrParameter = null)
	{
		$objCommand = new Command($this->strPathDescriptor);
		$strRes     = $objCommand->Get($strCommandText, $arrParameter);
		$objCommand = null;
		return($strRes);		
	}
	
	public function Rs($strParameter1, $arrParameter2 = null, $intPageSize = 0, $intAbsolutePage = 1)
	{
		$this->OpenConnection();

		switch($this->IsQueryOrStoredProcedure($strParameter1, $arrParameter2))
		{
			# Query.
			case "q": 
			{
				$this->strSql = trim($strParameter1);
				break;
			}
			# SP.
			case "s":
			{
				$this->strSql = $this->Command($strParameter1, $arrParameter2);
				break;
			}
		}
		
		$objRs = new Rs($this->objConn, $this->strSql, $intPageSize, $intAbsolutePage);
		$this->CloseConnection();
		return($objRs);
	}
	
	public function Execute($strParameter1, $arrParameter2)
	{
		$this->OpenConnection();

		switch($this->IsQueryOrStoredProcedure($strParameter1, $arrParameter2))
		{
			# Query.
			case "q": 
			{
				$this->strSql = $strParameter1;
				break;
			}
			# SP.
			case "s":
			{
				$this->strSql = $this->Command($strParameter1, $arrParameter2);
				break;
			}
		}

		if(!@mysql_query($this->strSql, $this->objConn))
		{   
			subError('dbcommand.class.php', 'Class CDBCommand|public function Execute($strParameter1, $arrParameter2)|if(!@mysql_query($strSql, $this->objConn))|$strSql = ' . $this->strSql);
			die();
		}

		$this->CloseConnection();
	}
	
	public function LastSql(){return($this->strSql);} 
}
?>