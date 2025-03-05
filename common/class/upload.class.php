<?php
class Upload
{
	public function __construct($strPath)
	{
	}
	
	private function Save($strInputFileName, $strPath, $strFileName = null)
	{
		$strUserFileName = $_FILES[$strInputFileName]["name"];
		$strType		 = $_FILES[$strInputFileName]["type"];
		$intSize		 = $_FILES[$strInputFileName]["size"];
		$strTmp			 = $_FILES[$strInputFileName]["tmp"]; 
		
		echo($strUserFileName);
		echo("<br>");
		echo($strType);
		echo("<br>");
		echo($intSize);
		echo("<br>");
		echo($strTmp);
	}
}
?>