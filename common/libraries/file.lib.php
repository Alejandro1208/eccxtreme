<?php
# ------------------------------------------------------------------------------------------------
function fncIsImage($strFile)
{
	$strExt = strtolower(fncFileGetExtension($strFile));
	$arrExt = fncSplit(IMG_EXTENCIONS, ",");
	foreach($arrExt as $sExtension)
	{
		if($sExtension == $strExt)
			return(true);
	}
	return(false);
}
# ------------------------------------------------------------------------------------------------

# ------------------------------------------------------------------------------------------------
# Nombre:		 fncFileGetImageInfo.
# Funcionalidad: Devuelve array con datos de la imagen(Ancho, alto, tipo).
# Parametros:	 $strImage - String - Path de la imagen.
# ------------------------------------------------------------------------------------------------
function fncFileGetImageInfo($strImage)
{
	$arrRes = array();
	if(file_exists($strImage))
	{
		$arr 	     = getimagesize($strImage);
		$arrRes["w"] = $arr[0];
		$arrRes["h"] = $arr[1];
		$arrRes["m"] = $arr["mime"];
	}
	else
	{
		$arrRes["w"] = null;
		$arrRes["h"] = null;
		$arrRes["m"] = null;		
	}
	return($arrRes);
} 
# ------------------------------------------------------------------------------------------------

# ------------------------------------------------------------------------------------------------
# Nombre:		 fncFilePath.
# Funcionalidad: Devuelve un path agregandole la "\" al final si es que no la tiene.
# Parametros:	 $strPath - String - Path.
# ------------------------------------------------------------------------------------------------
function fncFilePath($strPath)
{
	if(substr($strPath, strlen($strPath) - 1, 1) == "/")
		return($strPath);
	else
		return($strPath . "/");
}
# ------------------------------------------------------------------------------------------------

/*
# ------------------------------------------------------------------------------------------------
# Nombre:		 fncFileExist.
# Funcionalidad: Devuelve si existe o no un archivo.
# Parametros:	 $strFile - String - Path del archivo + nombre del archivo (ej: c:\windows\explorer.exe).
# ------------------------------------------------------------------------------------------------
function fncFileExist($strFile)
{
	return(file_exists($strFile));
}
# ------------------------------------------------------------------------------------------------
*/

# ------------------------------------------------------------------------------------------------
# Nombre:		 fncFileGetExtension.
# Funcionalidad: Devuelve la extension de un archivo.
# Parametros:	 $strFile - String - Path del archivo + nombre del archivo (ej: c:\windows\explorer.exe).
# ------------------------------------------------------------------------------------------------
function fncFileGetExtension($strFile)
{
	return(fncSubString($strFile, ".", false, 1, "", false, 0));
}
# ------------------------------------------------------------------------------------------------

# ------------------------------------------------------------------------------------------------
# Nombre:		 fncFileGetName.
# Funcionalidad: Devuelve el nombre de un archivo (Sin la extensión).
# Parametros:	 $strFile - String - Path del archivo + nombre del archivo (ej: c:\windows\explorer.exe).
# ------------------------------------------------------------------------------------------------
function fncFileGetName($strFile)
{
	return(fncSubString($strFile, "\\", false, 1, ".", false, 0));
}
# ------------------------------------------------------------------------------------------------

# ------------------------------------------------------------------------------------------------
# Nombre:		 fncUploadFile.
# Funcionalidad: Upload de archivo.
# Parametros:	 $strInputFileName - String - Nombre del input file.
#				 $strPath - 		 String - Path.
#				 $strFileName - 	 String - Nombre del archivo sin la extensión.
#				 $strExt - 			 String - Extención.
# ------------------------------------------------------------------------------------------------
function fncFileUpload($strInputFileName, $strPath, $strFileName = "", $strExt = "")
{
	$strTmp	  = $_FILES[$strInputFileName]["tmp_name"];
	$strName  = $_FILES[$strInputFileName]["name"];
	/*
	$strType  = $_FILES[$strInputFileName]["type"];
	$intSize  = $_FILES[$strInputFileName]["size"]; 
	*/

	if($strFileName == "")
	{
		if($strExt == "")
			$strFileName_new = $strName;
		else
			$strFileName_new = fncFileGetName($strName) . "." . $strExt;
	}
	else
	{
		if($strExt == "")
			$strFileName_new = $strFileName . "." . fncFileGetExtension($strName);
		else
			$strFileName_new = $strFileName . "." . $strExt;
	}

	$strFolder = fncFilePath($strPath);
	if(is_dir($strFolder))	
		move_uploaded_file($strTmp, $strFolder . $strFileName_new);
	else
	{
		subError('file.lib.php','fncFileUpload($strInputFileName, $strPath, $strFileName = "", $strExt = "")"|$strFolder: ' . $strFolder . '|No existe la carpeta');
	}
	
	return($strFileName_new);
}
# ------------------------------------------------------------------------------------------------

# ------------------------------------------------------------------------------------------------
# Nombre:		 subFileDelete.
# Funcionalidad: Elimina un archivo.
# Parametros:	 $strFile - String - Path del archivo + nombre del archivo (ej: c:\windows\explorer.exe).
# ------------------------------------------------------------------------------------------------
function subFileDelete($strFile)
{
	@unlink($strFile); 
}
#  ------------------------------------------------------------------------------------------------

# ------------------------------------------------------------------------------------------------
# Nombre:		 subFolderCopy.
# Funcionalidad: Copiar carpeta (recursivamente).
# Parametros:	 $src - String - Origen.
#				 $dst - String - Destino.
# http://php.net/manual/es/function.copy.php.
# ------------------------------------------------------------------------------------------------
function subFolderCopy($src, $dst) {
	flush();
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                subFolderCopy($src . '/' . $file,$dst . '/' . $file);
            }
            else {
                copy($src . '/' . $file,$dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}
# ------------------------------------------------------------------------------------------------

# ------------------------------------------------------------------------------------------------
# Nombre:		 subFolderDelete.
# Funcionalidad: Eliminar una carpeta (recursivamente).
# Parametros:	 $current_dir - String - Carpeta.
# http://php.net/manual/en/function.rmdir.php.
# ------------------------------------------------------------------------------------------------
function subFolderDelete($current_dir) {   
	if($dir = @opendir($current_dir)) {
       	while (($f = readdir($dir)) !== false) {
           	if($f > '0' and filetype($current_dir.$f) == "file") {
            	unlink($current_dir.$f);
            } elseif($f > '0' and filetype($current_dir.$f) == "dir") {
            	subFolderDelete($current_dir.$f."\\");
        	}
		}
		closedir($dir);
		rmdir($current_dir);
	}
} 
# ------------------------------------------------------------------------------------------------
?>
