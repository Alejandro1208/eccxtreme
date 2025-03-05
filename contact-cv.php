<?php

include("translator.php");
require_once("includes/begin-page.inc.php");
include("./backend/updateform/form_functions.php");
include("./fileUpload.php");


//idioma default
define("DEFAULT_LANG","es");

//cantidad de registros experiencia
define("CANTIDAD", 10);

// archivo de traducciones
define("LANG_FILE","contact-cv");

// prefijo para string de traduccion
define("LANG_PREFIX",LANG_FILE.".");

//cargo traducciones
$lang = new Language();
$lang->load(LANG_FILE);

//lookup para combos selectores de fecha
$stringDates = array(
    'd'=> $lang->line(LANG_PREFIX."dia"),
    'm'=> $lang->line(LANG_PREFIX."mes"),
    'a'=> $lang->line(LANG_PREFIX."anio"),
);

# Submit.
$sSubmit = fncRequest(REQUEST_METHOD_POST, "hdnSubmit", REQUEST_TYPE_STRING, "");
$sConfirm = fncRequest(REQUEST_METHOD_GET, "sConfirm", REQUEST_TYPE_STRING, "");

$datos = array();
$datos['ruta'] = "";
getPostData($datos);
//var_dump($datos);
$errores = array();
$exito = FALSE;

function getPostData(&$datos) {
    # Datos personales.
    $datos['sNyA'] = fncRequest(REQUEST_METHOD_POST, "txtNyA", REQUEST_TYPE_STRING, "");
    $datos['sFechaNac'] = getCanonicalStringDateFromDate("cmbFechasNacimiento");
    $datos['sFechaNacD'] = fncRequest(REQUEST_METHOD_POST, "cmbFechasNacimientoD", REQUEST_TYPE_STRING, "");
    $datos['sFechaNacM'] = fncRequest(REQUEST_METHOD_POST, "cmbFechasNacimientoM", REQUEST_TYPE_STRING, "");
    $datos['sFechaNacA'] = fncRequest(REQUEST_METHOD_POST, "cmbFechasNacimientoA", REQUEST_TYPE_STRING, "");
    $datos['iEstadoCivil'] = fncRequest(REQUEST_METHOD_POST, "rdoEstadoCivil", REQUEST_TYPE_INTEGER, null);
    $datos['iHijos'] = fncRequest(REQUEST_METHOD_POST, "cmbHijos", REQUEST_TYPE_INTEGER, null);
    $datos['iTipoDocumento'] = fncRequest(REQUEST_METHOD_POST, "cmbTiposDocumento", REQUEST_TYPE_INTEGER, null);
    $datos['iDocumento'] = fncRequest(REQUEST_METHOD_POST, "txtDocumento", REQUEST_TYPE_STRING, null);
    $datos['iCuil1'] = fncRequest(REQUEST_METHOD_POST, "cmbCuils1", REQUEST_TYPE_STRING, null);
    $datos['iCuil2'] = fncRequest(REQUEST_METHOD_POST, "txtCuil2", REQUEST_TYPE_STRING, null);
    $datos['iCuil3'] = fncRequest(REQUEST_METHOD_POST, "cmbCuils3", REQUEST_TYPE_STRING, null);
    $datos['iCuil'] = getCuil($datos['iCuil1'], $datos['iCuil2'], $datos['iCuil3']);
    $datos['iTelefono1'] = fncRequest(REQUEST_METHOD_POST, "txtTelefono1", REQUEST_TYPE_STRING, null);
    $datos['iTelefono2'] = fncRequest(REQUEST_METHOD_POST, "txtTelefono2", REQUEST_TYPE_STRING, null);
    $datos['iTelefono'] = getTelefono($datos['iTelefono1'], $datos['iTelefono2']);
    $datos['sCalle'] = fncRequest(REQUEST_METHOD_POST, "txtCalle", REQUEST_TYPE_STRING, "");
    $datos['iNro'] = fncRequest(REQUEST_METHOD_POST, "txtNro", REQUEST_TYPE_INTEGER, null);
    $datos['sPiso'] = fncRequest(REQUEST_METHOD_POST, "txtPiso", REQUEST_TYPE_INTEGER, null);
    $datos['sDepto'] = fncRequest(REQUEST_METHOD_POST, "txtDepto", REQUEST_TYPE_STRING, "");
    $datos['sCp'] = fncRequest(REQUEST_METHOD_POST, "txtCp", REQUEST_TYPE_STRING, "");
    $datos['sBarrio'] = fncRequest(REQUEST_METHOD_POST, "txtBarrio", REQUEST_TYPE_STRING, "");
    $datos['iProvincia'] = fncRequest(REQUEST_METHOD_POST, "cmbProvincias", REQUEST_TYPE_INTEGER, null);
    $datos['sEmail'] = fncRequest(REQUEST_METHOD_POST, "txtEmail", REQUEST_TYPE_STRING, "");

    # Perfil.
    $datos['sPerfil'] = fncRequest(REQUEST_METHOD_POST, "rdoPerfiles", REQUEST_TYPE_STRING, "");
    $datos['iPerfil'] = fncRequest(REQUEST_METHOD_POST, "cmbPerfiles", REQUEST_TYPE_INTEGER, null);
    $datos['aPerfiles'] = isset($_POST["chkPerfiles"]) ? $_POST["chkPerfiles"] : array();
    $datos['sPerfilSapOtro'] = fncRequest(REQUEST_METHOD_POST, "perfilSap1Otro", REQUEST_TYPE_STRING, "");
    $datos['bTieneConocimientosSap'] = fncRequest(REQUEST_METHOD_POST, "chkConocimientosSap", REQUEST_TYPE_BOOLEAN, false);
    $datos['bTieneCertificadoSap'] = fncRequest(REQUEST_METHOD_POST, "rdoCertificadoSap", REQUEST_TYPE_BOOLEAN, false);
    $datos['sConocimientos'] = fncRequest(REQUEST_METHOD_POST, "taConocimientos", REQUEST_TYPE_STRING, "");

    $datos['cRemuneracion'] = fncRequest(REQUEST_METHOD_POST, "txtRemuneracion", REQUEST_TYPE_INTEGER, 0);
    $datos['sModalidad'] = fncRequest(REQUEST_METHOD_POST, "txtModalidad", REQUEST_TYPE_STRING, "");
}


//validacion del lado del servidor
function validar(&$datos){
    
    global $errores;
    global $lang;
    
    $resultado_validacion = TRUE;
    $errores = array();
    
    //validación de upload de archivo
    if($_FILES['archivo']['error']!=UPLOAD_ERR_NO_FILE){ //el archivos excede el tamaño max permitido por el server?
        $res = guardar_imagen("archivo","./upload/");
     //   var_dump($res);
        if($res[0] == TRUE){
                $datos['ruta'] = $res[1];
        } else {
            array_push($errores,$res[1]);
            //var_dump($errores);
            $resultado_validacion = FALSE;
        }
    }

    // validación de unicidad de nombre del postulante
    if(existeNombreEnDB($datos['sNyA'])){
        array_push($errores,$lang->line(LANG_PREFIX."ya_existe_cv"));
        $resultado_validacion = FALSE;
    }
    
    return $resultado_validacion;
}

function existeNombreEnDB($nombre){
    $oDBCommand = $GLOBALS['objDBCommand'];
    $rs = $oDBCommand->Rs("sp_cvs_name_count", array("nya" => mb_strtoupper(trim($nombre),'UTF-8')));
    $resultado = $rs->Field("conteo")->Value();
    return $resultado != 0;
}

if ($sSubmit=="true") {
    if(validar($datos)) {
        $sPerfiles = implode(",", $datos['aPerfiles']);
        $aParameters = array(
        # Datos personales.
        "sNyA" => mb_strtoupper($datos['sNyA'],'UTF-8'),
        "dFechaNac" => $datos['sFechaNac'],
        "iFk_id_estado_civil" => $datos['iEstadoCivil'],
        "iHijos" => $datos['iHijos'],
        "iFk_id_tipo_documento" => $datos['iTipoDocumento'],
        "iNumeroDocumento" => $datos['iDocumento'],
        "iCuil" => $datos['iCuil'],
        "iTelefono" => $datos['iTelefono'],
        "sCalle" => $datos['sCalle'],
        "iNumero" => $datos['iNro'],
        "sPiso" => $datos['sPiso'],
        "sDepto" => $datos['sDepto'],
        "sCp" => $datos['sCp'],
        "sBarrio" => $datos['sBarrio'],
        "iFk_id_provincia" => $datos['iProvincia'],
        "sEmail" => $datos['sEmail'],
        # Perfil.
        "iFk_id_perfil" => $datos['iPerfil'],
        "sPerfil" => ($datos['sPerfil'] == "Otro") ? $datos['sPerfilSapOtro'] : $sPerfiles,
        "bTieneConocimientosSap" => $datos['bTieneConocimientosSap'],
        "bTieneCertificadoSap" => $datos['bTieneCertificadoSap'],
        "sConocimientos" => $datos['sConocimientos'],
        "cRemuneracion" => $datos['cRemuneracion'],
        "sModalidad" => $datos['sModalidad'],
        "rutaFoto" => $datos['ruta']
            );

    $oDBCommand->Execute("sp_cvs_a", $aParameters);
    $rs = $oDBCommand->Rs("sp_cvs_LastId", null);
    $iId = $rs->Field("id_cv")->Value();

    for ($j = 1; $j <= CANTIDAD; $j++) {
        # Experiencias laborales.
        $dDesde = getCanonicalStringDateFromDate("cmbPeriodosDesdeExpLaboral_$j");
        $dHasta = getCanonicalStringDateFromDate("cmbPeriodosHastaExpLaboral_$j");
        $bActualidad = fncRequest(REQUEST_METHOD_POST, "chkActualidad_$j", REQUEST_TYPE_BOOLEAN, 0 /* DB val for false */);
        $sCargo = fncRequest(REQUEST_METHOD_POST, "txtCargo_$j", REQUEST_TYPE_STRING, "");
        $sCompania = fncRequest(REQUEST_METHOD_POST, "txtCompania_$j", REQUEST_TYPE_STRING, "");
        $sCliente = fncRequest(REQUEST_METHOD_POST, "txtCliente_$j", REQUEST_TYPE_STRING, "");
        $iPais = fncRequest(REQUEST_METHOD_POST, "cmbPaises_$j", REQUEST_TYPE_INTEGER, null);
        $sPais = fncRequest(REQUEST_METHOD_POST, "txtPais_$j", REQUEST_TYPE_STRING, "");
        $sContexto = fncRequest(REQUEST_METHOD_POST, "txtContextoProyecto_$j", REQUEST_TYPE_STRING, "");
        $sActividades = fncRequest(REQUEST_METHOD_POST, "taActividades_$j", REQUEST_TYPE_STRING, "");

        # echo($dDesde . " - " . $dHasta . " - " . $sCargo . " - " . $sCompania . "<br>");

        if (
                !(empty($dDesde)) ||
                !(empty($dHasta)) ||
                !(empty($bActualidad)) ||
                !(empty($sCargo)) ||
                !(empty($sCompania)) ||
                !(empty($sCliente)) ||
                !(empty($iPais)) ||
                !(empty($sPais)) ||
                !(empty($sContexto)) ||
                !(empty($sActividades))
        ) {
            $aParameters = array(
                "iFk_id_cv" => $iId,
                "dFechaDesde" => $dDesde,
                "dFechaHasta" => $dHasta,
                "bActualidad" => $bActualidad,
                "sCargo" => $sCargo,
                "sCompania" => $sCompania,
                "sCliente" => $sCliente,
                "iFk_id_pais" => $iPais,
                "sPais" => $sPais,
                "sContextoProyecto" => $sContexto,
                "sActividades" => $sActividades,
            );
            $oDBCommand->Execute("sp_experiencias_laboral_a", $aParameters);
        }

        # Estudios.
        $iNivel = fncRequest(REQUEST_METHOD_POST, "cmbNiveles_$j", REQUEST_TYPE_INTEGER, null);
        $sTitulo = fncRequest(REQUEST_METHOD_POST, "txtTitulo_$j", REQUEST_TYPE_STRING, "");
        $sArea = fncRequest(REQUEST_METHOD_POST, "txtArea_$j", REQUEST_TYPE_STRING, "");
        $sInstitucion = fncRequest(REQUEST_METHOD_POST, "txtInstitucion_$j", REQUEST_TYPE_STRING, "");
        $dDesde = getCanonicalStringDateFromDate("cmbPeriodosDesdeEstudios_$j");
        $dHasta = getCanonicalStringDateFromDate("cmbPeriodosHastaEstudios_$j");
        $bActualidadE = fncRequest(REQUEST_METHOD_POST, "chkActualidadE_$j", REQUEST_TYPE_BOOLEAN, 0/* DB val for false */);
        $sDescripcion = fncRequest(REQUEST_METHOD_POST, "taEstudiosDescripcion_$j", REQUEST_TYPE_STRING, "");

        # echo($iNivel . " - " . $sTitulo . " - " . $sArea . " - " . $sInstitucion . "<br>");

        if (
                !(empty($iNivel)) ||
                !(empty($sTitulo)) ||
                !(empty($sArea)) ||
                !(empty($sInstitucion)) ||
                !(empty($dDesde)) ||
                !(empty($dHasta)) ||
                !(empty($bActualidadE)) ||
                !(empty($sDescripcion))
        ) {
            $aParameters = array(
                "iFk_id_cv" => $iId,
                "iFk_id_nivel" => $iNivel,
                "sTitulo" => $sTitulo,
                "sArea" => $sArea,
                "sInstitucion" => $sInstitucion,
                "dFechaDesde" => $dDesde,
                "dFechaHasta" => $dHasta,
                "sDescripcion" => $sDescripcion,
                "bActualidadE" => $bActualidadE
            );
            $oDBCommand->Execute("sp_estudios_a", $aParameters);
        }

        # Idiomas.
        $rs = $oDBCommand->Rs("sp_niveles_idioma_getall", null);
        while (!$rs->EOF()) {
            $iNivel = $rs->Field("id_nivel_idioma")->Value();
            $iIdioma = fncRequest(REQUEST_METHOD_POST, "cmbIdiomas_$j", REQUEST_TYPE_INTEGER, 0);
            $sIdioma = fncRequest(REQUEST_METHOD_POST, "txtIdioma_$j", REQUEST_TYPE_STRING, "");
            $sInstitucion = fncRequest(REQUEST_METHOD_POST, "txtInstitucion_$j", REQUEST_TYPE_STRING, "");
            $sCalificacion = fncRequest(REQUEST_METHOD_POST, "rdoIdioma_" . $j . "_" . $iNivel, REQUEST_TYPE_STRING, "");

            # echo($iIdioma . " - " . $sCalificacion . "<br>");

            if (($iIdioma > 0) && (!empty($sCalificacion))) {
                $aParameters = array(
                    "iFk_id_cv" => $iId,
                    "iFk_id_idioma" => $iIdioma,
                    "sIdioma" => $sIdioma,
                    "sInstitucion" => $sInstitucion,
                    "iFk_id_nivel" => $iNivel,
                    "sCalificacion" => $sCalificacion
                );
                $oDBCommand->Execute("sp_cvs_idiomas_niveles_a", $aParameters);
            }

            $rs->MoveNext();
        }
        unset($rs);

        # Cursos.
        $sNombre = fncRequest(REQUEST_METHOD_POST, "txtCurso_nombre_$j", REQUEST_TYPE_STRING, "");
        $sDescripcion = fncRequest(REQUEST_METHOD_POST, "taCurso_descripcion_$j", REQUEST_TYPE_STRING, "");

        # echo($sNombre . " - " . $sDescripcion . "<br>");

        if (
                !(empty($sNombre)) ||
                !(empty($sNombre))
        ) {
            $aParameters = array(
                "iFk_id_cv" => $iId,
                "sNombre" => $sNombre,
                "sDescripcion" => $sDescripcion
            );
            $oDBCommand->Execute("sp_cursos_a", $aParameters);
        }
    }

    $exito = TRUE;

    //limpio el form
    unset($_POST);
    getPostData($datos);

    }
}

function getTelefono($iTelefono1,$iTelefono2) {
    return((int) $iTelefono1 . $iTelefono2);
}

function getCuil($iCuil1,$iCuil2,$iCuil3) {
    if (($iCuil1 == "") || ($iCuil2 == "") || ($iCuil3 == "")) {
        return(null);
    } else {
        return((int) $iCuil1 . $iCuil2 . $iCuil3);
    }
}

function getCanonicalStringDateFromDate($sName) {
    $iD = fncRequest(REQUEST_METHOD_POST, $sName . "D", REQUEST_TYPE_INTEGER, 0);
    $iM = fncRequest(REQUEST_METHOD_POST, $sName . "M", REQUEST_TYPE_INTEGER, 0);
    $iY = fncRequest(REQUEST_METHOD_POST, $sName . "A", REQUEST_TYPE_INTEGER, 0);

    if (($iD == 0) || ($iM == 0) || ($iY == 0)) {
        return(null);
    } else {
        return("$iY-$iM-$iD");
    }
}

function getMonthArray($lang) {
    return array(
        "1" => $lang->line(LANG_PREFIX."enero"),
        "2" => $lang->line(LANG_PREFIX."febrero"),
        "3" => $lang->line(LANG_PREFIX."marzo"),
        "4" => $lang->line(LANG_PREFIX."abril"),
        "5" => $lang->line(LANG_PREFIX."mayo"),
        "6" => $lang->line(LANG_PREFIX."junio"),
        "7" => $lang->line(LANG_PREFIX."julio"),
        "8" => $lang->line(LANG_PREFIX."agosto"),
        "9" => $lang->line(LANG_PREFIX."septiembre"),
        "10" => $lang->line(LANG_PREFIX."octubre"),
        "11" => $lang->line(LANG_PREFIX."noviembre"),
        "12" => $lang->line(LANG_PREFIX."diciembre")
    );
}


function generateComboDate($idDia,$idMes,$idAnio,$defaults,$lang){

   $control = "";
   $dias = getDaysOptions(); $dias[""]=$lang->line(LANG_PREFIX.'dia');
   $meses = getMonthArray($lang); $meses[""]=$lang->line(LANG_PREFIX.'mes');
   $anios = getYearsOptions(); $anios[""]=$lang->line(LANG_PREFIX.'anio');

   //select Dias
   $control.= generateCombo($idDia, array("class"=>"dia"),$defaults["dia"],$dias);
   //select Meses
   $control.= generateCombo($idMes, array("class"=>"mes"),$defaults["mes"],$meses);
   //select Años
   $control.= generateCombo($idAnio, array("class"=>"ano"),$defaults["anio"],$anios);

   return $control;
}

function generateCombo($idSelect,$props,$default,$options){
   $control = "";
   $control.='<select id="'.$idSelect.'" name="'.$idSelect.'" ';
   foreach($props as $k=>$v)
       $control.= ' '.$k.'="'.$v.'" ';
   $control.='>';
   foreach ($options as $k => $v){
       //$control.="<option value='lala'>asdfsf</option>";
       $isDefault = ($k == $default)?"selected":"";
       $control.='<option value="'.$k.'" '.$isDefault.'>'.$v.'</option>';
   }
   $control.='</select>';
   return $control;
}


function selectFechas($sName,$strDays) {

?>
    <select id="<?php echo($sName) ?>D" name="<?php echo($sName) ?>D" class="dia">
        <option value=""><?php echo $strDays["d"];?>:</option>
<?php for ($j = 1; $j <= 31; $j++) { ?>
            <option value="<?php echo($j) ?>"><?php echo(fncDateFillWith0($j)) ?></option>
<?php } ?>
    </select>
    <select id="<?php echo($sName) ?>M" name="<?php echo($sName) ?>M" class="mes">
    <option value=""><?php echo $strDays["m"];?>:</option>
    <?php for ($j = 1; $j <= 12; $j++) {
 ?>
        <option value="<?php echo($j) ?>"><?php echo(fncMonthName($j, "esp")) ?></option>
<?php } ?>
</select>
<select id="<?php echo($sName) ?>A" name="<?php echo($sName) ?>A" class="mes" style="width: 60px;">
    <option value=""><?php echo $strDays["a"];?>:</option>
<?php
    $iFrom = date("Y");
    $iTo = date("Y") - 50;
    for ($j = $iFrom; $j >= $iTo; $j--) {
?>
        <option value="<?php echo($j) ?>"><?php echo($j) ?></option>
    <?php } ?>
</select>
<?php
}

$aPerfilesTodos = array(
    "FI" => "FI",
    "SD" => "SD",
    "CO" => "CO",
    "FI-CO" => "FI-CO",
    "MM" => "MM",
    "SD" => "SD",
    "Basis" => "Basis",
    "Seguridad" => "Seguridad"
        )
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ECC Extreme Cloud  Computing SA</title>
<link href="estilo.css" rel="stylesheet" type="text/css" />
<link href="esp.css" rel="stylesheet" type="text/css" />

  <!-- PNG FIX -->
        <script src="iepngfix_tilebg.js" type="text/javascript"></script>
        <script src="jquery.js"></script>
        <script src="jquery.validate.js"></script>
        <script src="check.js"></script>
        <style type="text/css">
            h3
            { _behavior: url("js/iepngfix/iepngfix.htc") }
            /* hover/active/focus fix */
            body { _behavior: url("js/iepngfix/csshover3.htc"); }

            #content .inside .contenedor-cv .envio p {
                color: #f16202;
                font-size: 1.3em;
                background: url(images/fondo-msg-envio.jpg) left no-repeat;
                border: 1px solid #cecece;
                width:664px;
                padding-top:10px;
                text-align:center;
                height:45px;
            }
            #content .inside .contenedor-cv .errores p {
                color: #176160;
                font-size: 1.4em;
                background: none;

            }
            #content .inside .contenedor-cv .errores ul {
                margin-bottom: 30px;
            }
            #content .inside .contenedor-cv .errores li {
                color: #f16202;
                font-size: 1.3em;
                background: url(images/bullet-errores.png) left no-repeat;

            }
            form.cmxform .checkbox {
                float: left;
                color: #626262;
                margin-right: 10px;
                line-height: 20px;
            }
        </style>
        <script type="text/javascript">
            // Expandibles.
            $(function() {
                $("form.cmxform p:even").addClass("striped");

<?php for ($j = 1; $j <= CANTIDAD - 1; $j++) { ?>
                                    $("#mas-experiencia-<?php echo($j); ?>").click(function(){
                                        $("#more-experiencia-<?php echo($j + 1); ?>").slideToggle("slow");
                                        return false;
                                    });

                                    $("#mas-estudio-<?php echo($j); ?>").click(function(){
                                        $("#more-estudio-<?php echo($j + 1); ?>").slideToggle("slow");
                                        return false;
                                    });

                                    $("#mas-idioma-<?php echo($j); ?>").click(function(){
                                        $("#more-idioma-<?php echo($j + 1); ?>").slideToggle("slow");
                                        return false;
                                    });

                                    $("#mas-curso-<?php echo($j); ?>").click(function(){
                                        $("#more-curso-<?php echo($j + 1); ?>").slideToggle("slow");
                                        return false;
                                    });
<?php } ?>
                            });
        </script>
        <script type="text/javascript">
            // Perfiles
            $(function() {
                $("#cmbPerfiles").change(function(){
                    if($("#cmbPerfiles").val() != ""){
                    if(($("#cmbPerfiles").val() == "1") || ($("#cmbPerfiles").val() == "2")){
                        $("#perfilSap1").show();
                        $("#perfilSap2").hide();
                    }
                    else{
                        $("#perfilSap1").hide();
                        $("#perfilSap2").show();
                    }
                    }
                });
            });

            $(function() {
                $("input[name=rdoPerfiles]:radio").click(function(){
                    if($("input[name=rdoPerfiles]:checked").val() == "Otro"){
                        $("#perfilSap1Otro").show();
                    }
                    else{
                        $("#perfilSap1Otro").hide();
                    }
                });
            });
        </script>
        <script type="text/javascript">
            // Validaciones.
            $(function() {
                $("#signupForm").validate({
                    rules:{
                        txtNyA:{
                            required: true
                        },
                        cmbFechasNacimientoD:{
                            //required: false,
                            //fechaDeNacimientoIsEmpty: false,
                            fechaDeNacimientoIsValid: true
                        },
                        rdoEstadoCivil:{
                            required: true
                        },
                        cmbTiposDocumento:{
                            required: false
                        },
                        txtDocumento:{
                            required: false,
                            digits: true,
                            rangelength: [8, 9]
                        },
                        cmbCuils1:{
                            required: false,
                            //cuilIsEmpty: false,
                            cuilIsValid: true
                        },
                        txtEmail:{
                            required: true,
                            email: true
                        },
                        cmbPerfiles:{
                            required: true
                        },
                        taConocimientos:{
                            required: true
                        },

                        cmbPeriodosDesdeExpLaboral_1D:{
                            dateIsValid: true
                        },
                        cmbPeriodosHastaExpLaboral_1D:{
                            dateIsValid: true
                        },

                        cmbPeriodosDesdeEstudios_1D:{
                            dateIsValid: true
                        },
                        cmbPeriodosHastaEstudios_1D:{
                            dateIsValid: true
                        }
                    },
                    messages:{
                        txtNyA:{
                            required: "<?php echo $lang->line(LANG_PREFIX."ingrese_nombre")?>"
                        },
                        cmbFechasNacimientoD:{
                            fechaDeNacimientoIsEmpty: "Debe selecionar la fecha de nacimiento",
                            fechaDeNacimientoIsValid: "La fecha de nacimiento no es una fecha valida"
                        },
                        rdoEstadoCivil:{
                            required: "<?php echo $lang->line(LANG_PREFIX."ingrese_estado_civil")?>"
                        },
                        cmbTiposDocumento:{
                            required: "Debe seleccionar el tipo de documento"
                        },
                        txtDocumento:{
                            required: "<?php echo $lang->line(LANG_PREFIX."ingrese_nro_dni")?>",
                            digits: "<?php echo $lang->line(LANG_PREFIX."ingrese_nro_dni_entero")?>",
                            rangelength: "<?php echo $lang->line(LANG_PREFIX."ingrese_nro_dni_digitos")?>"
                        },
                        cmbCuils1:{
                            cuilIsEmpty: "Debe ingresar su cuil",
                            cuilIsValid: "<?php echo $lang->line(LANG_PREFIX."ingrese_nro_cuil_digitos")?>"
                        },
                        txtEmail:{
                            required: "<?php echo $lang->line(LANG_PREFIX."ingrese_email")?>",
                            email: "<?php echo $lang->line(LANG_PREFIX."ingrese_email_valido")?>"
                        },
                        cmbPerfiles:{
                            required: "<?php echo $lang->line(LANG_PREFIX."ingrese_perfil")?>"
                        },
                        taConocimientos:{
                            required: "<?php echo $lang->line(LANG_PREFIX."ingrese_conocimientos")?>"
                        },

                        cmbPeriodosDesdeExpLaboral_1D:{
                            dateIsValid: "La fecha desde del periodo de la experiencia laboral es incorrecta"
                        },
                        cmbPeriodosHastaExpLaboral_1D:{
                            dateIsValid: "La fecha hasta del periodo de la experiencia laboral es incorrecta"
                        },

                        cmbPeriodosDesdeEstudios_1D:{
                            dateIsValid: "La fecha desde del periodo de los estudios es incorrecta"
                        },
                        cmbPeriodosHastaEstudios_1D:{
                            dateIsValid: "La fecha hasta del periodo de los estudios es incorrecta"
                        }
                    },
                    errorContainer:      "#errors",
                    errorLabelContainer: "#errors ul",
                    wrapper: 			 "li",
                    onfocusout: 		 false,
                    onkeyup:	 		 false,
                    onclick:			 false
                });
            });

            jQuery.validator.addMethod("fechaDeNacimientoIsEmpty", function(value, element){
                return(!(
                ($("#cmbFechasNacimientoD").val() == "") &&
                    ($("#cmbFechasNacimientoM").val() == "") &&
                    ($("#cmbFechasNacimientoA").val() == "")
            ));
            });

            jQuery.validator.addMethod("fechaDeNacimientoIsValid", function(value, element){
                if(	($("#cmbFechasNacimientoA").val()=="")&&($("#cmbFechasNacimientoM").val()=="")&&($("#cmbFechasNacimientoD").val()=="") )
                    return true;
                else
                    return(isDate(
                $("#cmbFechasNacimientoA").val(),
                $("#cmbFechasNacimientoM").val(),
                $("#cmbFechasNacimientoD").val()
            ));
            });

            jQuery.validator.addMethod("cuilIsEmpty", function(value, element){
                return(!(
                ($("#cmbCuils1").val() == "") &&
                    ($("#txtCuil2").val()  == "") &&
                    ($("#txtCuil3").val()  == "")
            ));
            });
            jQuery.validator.addMethod("cuilIsValid", function(value, element){
                if($("#txtCuil2").val()=="") return true;
                else return(
                (isInteger($("#txtCuil2").val()) &&
                    ($("#txtCuil2").val().length == 8)
            ));
            });

            jQuery.validator.addMethod("dateIsEmpty", function(value, element){
                return(
                ($("#" + sPrefix + "D").val() == "") &&
                    ($("#" + sPrefix + "M").val() == "") &&
                    ($("#" + sPrefix + "A").val() == "")
            );
            });

            jQuery.validator.addMethod("dateIsValid", function(value, element){
                var sId     = element.id;
                var sPrefix = sId.substr(0, sId.length - 1);

                if(
                ($("#" + sPrefix + "D").val() == "") &&
                    ($("#" + sPrefix + "M").val() == "") &&
                    ($("#" + sPrefix + "A").val() == "")
            ){
                    return(true);
                }

                return(isDate(
                $("#" + sPrefix + "A").val(),
                $("#" + sPrefix + "M").val(),
                $("#" + sPrefix + "D").val()
            ));
            });
        </script>
        <script type="text/javascript">
            // Pais
            function Pais(iIndice){
                var oCmb = document.getElementById ("cmbPaises_" + iIndice);
                if(oCmb.value == '4'){
                    $("#paisOtro_" + iIndice).show();
                }
                else{
                    $("#paisOtro_" + iIndice).hide();
                }
            }
        </script>
        <script type="text/javascript">
            // Idioma
            function Idioma(iIndice){
                var oCmb = document.getElementById ("cmbIdiomas_" + iIndice);
                if(oCmb.value == '4'){
                    $("#idiomaOtro_" + iIndice).show();
                }
                else{
                    $("#idiomaOtro_" + iIndice).hide();
                }
            }
        </script>

        <script type="text/javascript">

            var qTipX = 0; //This is qTip's X offset//
            var qTipY = 15; //This is qTip's Y offset//

            //There's No need to edit anything below this line//
            tooltip = {
                name : "qTip",
                offsetX : qTipX,
                offsetY : qTipY,
                tip : null
            }

            tooltip.init = function () {
                var tipNameSpaceURI = "http://www.w3.org/1999/xhtml";
                if(!tipContainerID){ var tipContainerID = "qTip";}
                var tipContainer = document.getElementById(tipContainerID);

                if(!tipContainer) {
                    tipContainer = document.createElementNS ? document.createElementNS(tipNameSpaceURI, "div") : document.createElement("div");
                    tipContainer.setAttribute("id", tipContainerID);
                    document.getElementsByTagName("body").item(0).appendChild(tipContainer);
                }

                if (!document.getElementById) return;
                this.tip = document.getElementById (this.name);
                if (this.tip) document.onmousemove = function (evt) {tooltip.move (evt)};
            }

            tooltip.move = function (evt) {
                var x=0, y=0;
                if (document.all) {//IE
                    x = (document.documentElement && document.documentElement.scrollLeft) ? document.documentElement.scrollLeft : document.body.scrollLeft;
                    y = (document.documentElement && document.documentElement.scrollTop) ? document.documentElement.scrollTop : document.body.scrollTop;
                    x += window.event.clientX;
                    y += window.event.clientY;

                } else {//Good Browsers
                    x = evt.pageX;
                    y = evt.pageY;
                }
                this.tip.style.left = (x + this.offsetX) + "px";
                this.tip.style.top = (y + this.offsetY) + "px";
            }

            tooltip.show = function (text) {
                if (!this.tip) return;
                this.tip.innerHTML = text;
                this.tip.style.display = "block";
            }

            tooltip.hide = function () {
                if (!this.tip) return;
                this.tip.innerHTML = "";
                this.tip.style.display = "none";
            }
            window.onload = function () {
                tooltip.init ();
            }
        </script>
        
        <script type="text/javascript">

        <!--//
        function validateFileExtension(fld) {
                if(!/(\.gif|\.jpg|\.jpeg|\.png)$/i.test(fld.value)) {
                        alert("<?php echo $lang->line(LANG_PREFIX."tipo_imagen")?>");
                        fld.value = "";
                        fld.focus();
                        return false;
                }
                return true;
        }
        //-->
        </script>

        <script type="text/javascript">
            function initialize() {
                tooltip.init();
                $("#cmbPerfiles").change();
            }
        window.onload = initialize;
        </script>

        <style type="text/css">
            div#qTip {
                padding: 7px;
                border: 0px solid #000;
                border-right-width: 0px;
                border-bottom-width: 0px;
                display: none;
                background: #ccc;
                color: #f00;
                font:  10px Verdana, Arial, sans-serif;
                text-align: left;
                position: absolute;
                z-index: 1000;
            }
        </style>

    <link type="text/css" rel="stylesheet" href="chrome-extension://cpngackimfmofbokmjmljamhdncknpmg/style.css"><script type="text/javascript" charset="utf-8" src="chrome-extension://cpngackimfmofbokmjmljamhdncknpmg/page_context.js"></script><script type="text/javascript" src="./CV XTREME editable_files/hosts.js"></script>

</head>

<!--TOOLTIP -->
<span id="toolTipBox" width="200" style="color:red;"></span>
<body bgcolor="#F5F5F5" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
        <div id="sector1"> 
    
        <div id="header" class="caja"> 
            <div id="logo"> </div>

            <!--COMENIZO MENU-->
             
                <div id="menu_contact">
                            
                <ul id="navBar">
                    <li id="navBarhome"><a href="index.html"><?php echo $lang->line(LANG_PREFIX."home") ?></a></li>
                    <li id="navBarservicios"><a href="servicios_esp.html"><?php echo $lang->line(LANG_PREFIX."servicios") ?></a></li>
                    <li id="navBarproyectos"><a href="proyectos_esp.html"><?php echo $lang->line(LANG_PREFIX."proyectos") ?></a></li>
                    <li id="navBarconsultoria"><a href="consultoria_esp.html"><?php echo $lang->line(LANG_PREFIX."consultoria") ?></a></li>                            
                    <li id="navBarcontactoCurrent"><a href="contacto_esp.html"><?php echo $lang->line(LANG_PREFIX."contacto") ?></a></li>
                            
                </ul>         
            </div>
            <!--FIN MENU-->
        </div>
    </div>
    
    <div id="sector2" class="sap"> 
    
        <div  class="caja" ></div>
            
            
    </div>
    
    <div id="sector3"> 
    
            <div id="contenido_contact_cv" class="caja" >
      
            <div class="central">
                <div class="contenedor-cv">
                    <div id="<?php echo $lang->line(LANG_PREFIX."class-contenedor-titulo")?>"> </div>
<?php if ($exito == TRUE) { ?>
                   <div class="envio">
                        <p>
                            <?php echo $lang->line(LANG_PREFIX."msg_submit_ok_1")?><br />
                            <?php echo $lang->line(LANG_PREFIX."msg_submit_ok_2")?>
                        </p>
                   </div>
    <?php } ?>
                        <div id="errors" class="errores" style="display: <?php  echo ((count($errores)>0)?"block":"none");?>;">
                            <p><?php echo $lang->line(LANG_PREFIX."errores_encontrados")?>:</p>
                            <ul>
                                <?php foreach ($errores as $e) { echo '<li><label class="error">'.$e."</label></li>";} ?>
                            </ul>
                            
                        </div>

                        <p>
			  		<?php echo $lang->line(LANG_PREFIX."nos_interesa")?> <br>
			  		<?php echo $lang->line(LANG_PREFIX."ingresa_datos")?>
                        </p>

                        <form class="cmxform" id="signupForm" method="post" action="" enctype="multipart/form-data">
                            <input type="hidden" id="hdnSubmit" name="hdnSubmit" value="true" />
                            <fieldset>
                                <h3><?php echo $lang->line(LANG_PREFIX."datos_personales")?></h3>
                                <div class="form-top">
                                    <div class="form-bottom">
                                        <div class="form-middle">

                                            <p class="striped"><label  class="bullet" style="color:red;"
                                                       onmouseover="javascript:tooltip.show('Campo Obligatorio');"
                                                       onmouseout="javascript:tooltip.hide();"><?php echo $lang->line(LANG_PREFIX."nombre_apellido")?>: *</label>
                                                <input type="text" id="txtNyA" name="txtNyA" maxlength="50" class="w-295" value ="<?php echo $datos['sNyA']?>"/>
                                            </p>
                                            <p class="striped">
                                                <label for="lastname" class="bullet"><?php echo $lang->line(LANG_PREFIX."fecha_nacimiento")?>: </label>
                                                <?php echo generateComboDate("cmbFechasNacimientoD", "cmbFechasNacimientoM","cmbFechasNacimientoA", array("mes"=>$datos['sFechaNacM'],"dia"=>$datos['sFechaNacD'],"anio"=>$datos['sFechaNacA']),$lang);?>


                                             </p>

                                             <p>
                                                <label  class="bullet w-80" style="color:red;" style="color:red;"
                                                        onmouseover="javascript:tooltip.show('Campo Obligatorio');"
                                                        onmouseout="javascript:tooltip.hide();"><?php echo $lang->line(LANG_PREFIX."estado_civil")?>: *</label>
                                                        <?php
                                                        $rs = $oDBCommand->Rs("sp_estados_civil_combo", null);
                                                        while (!$rs->EOF()) {
                                                        ?>
                                                <span><?php echo $lang->line(LANG_PREFIX.$rs->Field("i18n_string")->Value()); ?></span><input type="radio" value="<?php echo(utf8_encode($rs->Field("id_estado_civil")->Value())); ?>" class="radio" name="rdoEstadoCivil" <?php if($rs->Field("id_estado_civil")->Value()==$datos['iEstadoCivil']) echo "checked"?>/>
                                                        <?php
                                                        $rs->MoveNext();
                                                    }
                                                    unset($rs);
                                                        ?>
                                             </p>

                                                <p class="striped">
                                                    <label class="bullet"><?php echo $lang->line(LANG_PREFIX."cantidad_hijos")?>: </label>
                                                    <?php $opts = getOptionsRange(0, 10); $opts[0]=$lang->line(LANG_PREFIX."ninguno"); echo generateCombo("cmbHijos", array(), $datos['iHijos'], $opts)?>
                                                </p>

                                                <p>
                                                    <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."documento")?>: </label>
                                                    <select tabindex="8" id="cmbTiposDocumento" name="cmbTiposDocumento" class="dni" <?php if ($lang->getLoadedLanguage() !=  DEFAULT_LANG) echo 'style="display:none"'?> >
<?php
                                                    $rs = $oDBCommand->Rs("sp_tipos_documento_combo", null);
                                                    while (!$rs->EOF()) {
?>
                                                        <option value="<?php echo($rs->Field("id_tipo_documento")->Value()) ?>" <?php if ($rs->Field("id_tipo_documento")->Value() == $datos['iTipoDocumento']) {
                                                            echo(" selected");
                                                        } else {if(($rs->Field("id_tipo_documento")->Value()==1)&&($datos['iTipoDocumento']=="")) echo "selected"; } ?> >
                                                        <?php echo(utf8_encode($rs->Field("nombre")->Value())) ?></option>
<?php
                                                        $rs->MoveNext();
                                                    }
                                                    unset($rs);
?>
                                                </select>
                                                <label  class="w-60"><?php echo $lang->line(LANG_PREFIX."documento_nro")?>: </label>
                                                <input type="text" id="txtDocumento" name="txtDocumento" maxlength="8" class="w-140" style="width: 80px;" value="<?php echo $datos['iDocumento']?>" />
                                                <?php if ($lang->getLoadedLanguage() == DEFAULT_LANG) {?>
                                                    <label  class="bullet w-45">CUIL: </label>
                                                    <?php echo generateCombo("cmbCuils1", array("class"=>"dni","style"=>"width:40px;"), $datos['iCuil1'], array(20=>"20",23=>"23",27=>"27",30=>"30"))?>
                                                    <input type="text" id="txtCuil2" name="txtCuil2" maxlength="8" class="w-140" style="width: 80px;" value="<?php echo $datos['iCuil2']?>"/>
                                                    <?php echo generateCombo("cmbCuils3", array("class"=>"dni","style"=>"width:40px;"), $datos['iCuil3'], getOptionsRange(0, 9))?>
                                                <?php }?>
                                            </p>
                                            <p class="striped">
                                                <label class="bullet"><?php echo $lang->line(LANG_PREFIX."telefono")?>:</label>
                                                <input type="text" id="txtTelefono1" name="txtTelefono1" maxlength="10" class="w-60" value="<?php echo $datos['iTelefono1']?>" />
                                                <input type="text" id="txtTelefono2" name="txtTelefono2" maxlength="40" class="w-155" value="<?php echo $datos['iTelefono2']?>" />
                                            </p>

                                            <p>
                                                <label class="bullet"><?php echo $lang->line(LANG_PREFIX."direccion")?>:</label>
                                                <span class="cero"><?php echo $lang->line(LANG_PREFIX."calle")?>:</span>
                                                <input type="text" id="txtCalle" name="txtCalle" maxlength="50" class="w-295" value="<?php echo $datos['sCalle'] ?>" />
                                                <span><?php echo $lang->line(LANG_PREFIX."numero_calle")?>:</span>
                                                <input type="text" id="txtNro" name="txtNro" maxlength="10" class="w-40" value="<?php echo $datos['iNro'] ?>"/>
                                            </p>
                                            <p class="striped">
                                                <span style="margin-left:165px;"><?php echo $lang->line(LANG_PREFIX."piso")?>:</span>
                                                <input type="text" id="txtPiso" name="txtPiso" maxlength="3" class="w-60" value="<?php echo $datos['sPiso'] ?>"/>
                                                <span><?php echo $lang->line(LANG_PREFIX."depto")?>:</span>
                                                <input type="text" id="txtDepto" name="txtDepto" maxlength="3" class="w-40" value="<?php echo $datos['sDepto'] ?>"/>
                                                <span><?php echo $lang->line(LANG_PREFIX."codigo_postal")?>:</span>
                                                <input type="text" id="txtCp" name="txtCp" maxlength="10" class="w-60" value="<?php echo $datos['sCp'] ?>"/>
                                            </p>
                                            <p class="striped">
                                                <label for="email" class="bullet"><?php echo $lang->line(LANG_PREFIX."barrio")?>:</label>
                                                <input type="text" id="txtBarrio" name="txtBarrio" maxlength="50" class="w-140" value="<?php echo $datos['sBarrio'] ?>"/>
                                                <span><?php echo $lang->line(LANG_PREFIX."provincia")?>:</span>
                                                <select tabindex="8" id="cmbProvincias" name="cmbProvincias" class="w-200">
                                                    <option value=""><?php echo $lang->line(LANG_PREFIX."provincia")?>:</option>
<?php
                                                    $rs = $oDBCommand->Rs("sp_provincias_combo", null);
                                                    while (!$rs->EOF()) {
?>
                                                        <option value="<?php echo($rs->Field("id_provincia")->Value()) ?>" <?php if($rs->Field("id_provincia")->Value()==$datos['iProvincia']) echo "selected" ?> > <?php echo $lang->line(LANG_PREFIX.$rs->Field("i18n_string")->Value()) ?></option>
<?php
                                                        $rs->MoveNext();
                                                    }
                                                    unset($rs);
?>
                                                </select>
                                            </p>

                                            <p>
                                                <label class="bullet" style="color:red;"
                                                       onmouseover="javascript:tooltip.show('Campo Obligatorio');"
                                                       onmouseout="javascript:tooltip.hide();"><?php echo $lang->line(LANG_PREFIX."email")?>: *</label>
                                                <input type="text" id="txtEmail" name="txtEmail" maxlength="50" class="w-295" value="<?php echo $datos['sEmail'] ?>" />
                                            </p>
                                            <p class="striped">
                                                <label class="bullet"><?php echo $lang->line(LANG_PREFIX."foto")?>:</label>
                                                <input type="file" style="height:22px;width:350px;" name="archivo" id="archivo" onChange="validateFileExtension(this)"></input>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <h3><?php echo $lang->line(LANG_PREFIX."perfil")?></h3>


                                <div class="form-top">
                                    <div class="form-bottom">
                                        <div class="form-middle">


                                            <p>
                                                <label  class="bullet" style="color:red;"
                                                        onmouseover="javascript:tooltip.show('Campo Obligatorio');"
                                                        onmouseout="javascript:tooltip.hide();"><?php echo $lang->line(LANG_PREFIX."perfil")?>: *</label>
                                                <select tabindex="8" id="cmbPerfiles" name="cmbPerfiles" class="w-200">
                                                    <option value=""><?php echo $lang->line(LANG_PREFIX."perfil")?></option>
<?php
                                                    $rs = $oDBCommand->Rs("sp_perfiles_combo", null);
                                                    while (!$rs->EOF()) {
?>
                                                        <option value="<?php echo($rs->Field("id_perfil")->Value()) ?>" <?php if($rs->Field("id_perfil")->Value()==$datos['iPerfil']) echo "selected"?> ><?php echo $lang->line(LANG_PREFIX.$rs->Field("i18n_string")->Value()) ?></option>
<?php
                                                        $rs->MoveNext();
                                                    }
                                                    unset($rs);
?>
                                                </select>
                                            </p>

                                            <p id="perfilSap1" style="display:none;" class="striped">
<?php foreach ($aPerfilesTodos as $datos['sPerfil']) { ?>
                                                <input type="checkbox" value="<?php echo($datos['sPerfil']); ?>" class="radio" name="chkPerfiles[]" <?php if(in_array($datos['sPerfil'], $datos['aPerfiles'])) echo "checked";?>/>
                                                        <span><?php echo($datos['sPerfil']); ?></span>
<?php } ?>
                                                    <input type="radio" value="Otro" class="radio" name="rdoPerfiles"/>
                                                    <span>Otro</span>
                                                    <br/>
                                                    <span id="perfilSap1Otro" style="display:none;"><input type="text" id="txtPerfilSapOtro" name="txtPerfilSapOtro" maxlength="50" class="w-200" value="<?php echo $datos['sPerfilSapOtro']?>"/></span>
                                                    <br/>
                                                    <span><?php echo $lang->line(LANG_PREFIX."es_certificado")?>:</span>
                                                    <span><input type="radio"  value="1" id="rdoCertificadoSapSi" name="rdoCertificadoSap" class="radio"  <?php echo ($datos['bTieneCertificadoSap'])?"checked":""?> />Si</span>
                                                    <span><input type="radio" value="0" id="rdoCertificadoSapNo" name="rdoCertificadoSap" class="radio" <?php echo ($datos['bTieneCertificadoSap'])?"":"checked"?> />No</span>
                                                </p>

                                                <p id="perfilSap2" style="display:none;">
                                                    <label class="bullet"><?php echo $lang->line("tiene_conocimientos_sap");?>:</label>
                                                    <input type="radio" value="1" class="radio" name="chkConocimientosSap" <?php echo ($datos['bTieneConocimientosSap'])?"checked":""?>/><span><?php echo $lang->line(LANG_PREFIX."si")?></span>
                                                    <input type="radio" value="0" class="radio" name="chkConocimientosSap" <?php echo (!$datos['bTieneConocimientosSap'])?"checked":""?>/><span><?php echo $lang->line(LANG_PREFIX."no")?></span>
                                                </p>

                                                <p class="last striped">
                                                    <label class="bullet" style="color:red;"
                                                           onmouseover="javascript:tooltip.show('Campo Obligatorio');"
                                                           onmouseout="javascript:tooltip.hide();"><?php echo $lang->line(LANG_PREFIX."conocimientos")?>: *</label>
                                                    <textarea id="taConocimientos" name="taConocimientos"><?php echo $datos['sConocimientos'] ?></textarea>
                                                </p>




                                            </div>
                                        </div>
                                    </div>


                                    <h3><?php echo $lang->line(LANG_PREFIX."experiencia_laboral")?></h3>


                                    <div class="form-top">
                                        <div class="form-bottom">
                                            <div class="form-middle">
                                                <p>
                                                    <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."periodo")?>:</label>
<?php selectFechas("cmbPeriodosDesdeExpLaboral_1",$stringDates); ?>
                                                <?php selectFechas("cmbPeriodosHastaExpLaboral_1",$stringDates); ?>
                                                </p>
                                                <p class="checkbox striped"><?php echo $lang->line(LANG_PREFIX."actualidad")?> <input type="checkbox" name="chkActualidad_1" value="1"  class="radio margen" /></p>


                                                <p>
                                                    <label class="bullet"><?php echo $lang->line(LANG_PREFIX."cargo")?>:</label>
                                                    <input type="text" id="txtCargo_1" name="txtCargo_1" maxlength="50" class="w-195" />

                                                    <label class="bullet w-100"><?php echo $lang->line(LANG_PREFIX."compania")?>:</label>
                                                    <input type="text" id="txtCompania_1" name="txtCompania_1" maxlength="50" class="w-160" />
                                                </p>

                                                <p>
                                                    <label class="bullet striped"><?php echo $lang->line(LANG_PREFIX."cliente")?>:</label>
                                                    <input type="text" id="txtCliente_1" name="txtCliente_1" maxlength="50" class="w-195" />
                                                </p>

                                                <p>
                                                    <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."ubicacion")?>:</label>
                                                    <select tabindex="8" id="cmbPaises_1" name="cmbPaises_1" class="w-200" onChange="Pais(1);">
                                                        <option value=""><?php echo $lang->line(LANG_PREFIX."ubicacion")?></option>
<?php
                                                    $rs = $oDBCommand->Rs("sp_paises_combo", null);
                                                    while (!$rs->EOF()) {
?>
                                                        <option value="<?php echo($rs->Field("id_pais")->Value()) ?>"><?php echo $lang->line(LANG_PREFIX.$rs->Field("i18n_string")->Value()) ?></option>
<?php
                                                        $rs->MoveNext();
                                                    }
                                                    unset($rs);
?>
                                                </select>
                                                <span id="paisOtro_1" style="display:none;"><input type="text" name="txtPais_1" /></span>
                                            </p>

                                            <p>
                                                <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."contexto_proyecto")?>:</label>
                                                <input type="text" id="txtContextoProyecto_1" name="txtContextoProyecto_1" maxlength="50" class="w-295" />
                                            </p>

                                            <p class="last">
                                                <label class="bullet"><?php echo $lang->line(LANG_PREFIX."actividades")?>:</label>
                                                <textarea id="taActividades_1" name="taActividades_1"></textarea>
                                            </p>

                                            <p class="estudio last striped">
                                                <label  class="bullet exp"><?php echo $lang->line(LANG_PREFIX."agregar_experiencia")?></label>
                                                <a id="mas-experiencia-1" href="javascript:void(0)" class="mas-experiencia"><img src="images/plus.png" alt="m&aacute;s" /></a>
                                            </p>
                                        </div>
                                    </div>
                                </div>

<?php for ($j = 2; $j <= CANTIDAD; $j++) { ?>
                                    <div id="more-experiencia-<?php echo($j); ?>" class="form-top more-experiencia" style="display: none;">
                                                            <div class="form-bottom">
                                                                <div class="form-middle">
                                                                    <p>
                                                                        <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."periodo")?>:</label>
<?php selectFechas("cmbPeriodosDesdeExpLaboral_$j",$stringDates); ?>
                                                <?php selectFechas("cmbPeriodosHastaExpLaboral_$j",$stringDates); ?>
                                                        <br/>
                                                    </p>
                                                    <p class="checkbox striped"><?php echo $lang->line(LANG_PREFIX."actualidad")?> <input type="checkbox" name="chkActualidad_<?php echo($j); ?>" value="1" class="radio margen"/></p>                                                  </p>
                                                    <p class="striped">
                                                        <label class="bullet"><?php echo $lang->line(LANG_PREFIX."cargo")?>:</label>
                                                        <input type="text" id="txtCargo_<?php echo($j); ?>" name="txtCargo_<?php echo($j); ?>" maxlength="50" class="w-195" />

                                                        <label class="bullet w-100"><?php echo $lang->line(LANG_PREFIX."compania")?>:</label>
                                                        <input type="text" id="txtCompania_<?php echo($j); ?>" name="txtCompania_<?php echo($j); ?>" maxlength="50" class="w-160" />
                                                    </p>

                                                    <p>
                                                        <label class="bullet"><?php echo $lang->line(LANG_PREFIX."cliente")?>:</label>
                                                        <input type="text" id="txtCliente_<?php echo($j); ?>" name="txtCliente_<?php echo($j); ?>" maxlength="50" class="w-195" />
                                                    </p>

                                                    <p class="striped">
                                                        <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."ubicacion")?>:</label>
                                                        <select tabindex="8" id="cmbPaises_<?php echo($j); ?>" name="cmbPaises_<?php echo($j); ?>" class="w-200" onChange="Pais(<?php echo($j); ?>);">
                                                            <option value=""><?php echo $lang->line(LANG_PREFIX."ubicacion")?></option>
<?php
                                                        $rs = $oDBCommand->Rs("sp_paises_combo", null);
                                                        while (!$rs->EOF()) {
?>
                                                            <option value="<?php echo($rs->Field("id_pais")->Value()) ?>"><?php echo(utf8_encode($rs->Field("nombre")->Value())) ?></option>
<?php
                                                            $rs->MoveNext();
                                                        }
                                                        unset($rs);
?>
                                                    </select>
                                                    <span id="paisOtro_<?php echo($j); ?>" style="display:none;"><input type="text" name="txtPais_<?php echo($j); ?>" name="txtPais_<?php echo($j); ?>" /></span>
                                                </p>

                                                <p>
                                                    <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."contexto_proyecto")?>:</label>
                                                    <input type="text" id="txtContextoProyecto_<?php echo($j); ?>" name="txtContextoProyecto_<?php echo($j); ?>" maxlength="50" class="w-295" />
                                                </p>

                                                <p class="last striped">
                                                    <label class="bullet"><?php echo $lang->line(LANG_PREFIX."actividades")?>:</label>
                                                    <textarea id="taActividades_<?php echo($j); ?>" name="taActividades_<?php echo($j); ?>"></textarea>
                                                </p>

                                                <p class="estudio last">
                                                    <label  class="bullet exp"><?php echo $lang->line(LANG_PREFIX."agregar_experiencia")?></label>
                                                    <a id="mas-experiencia-<?php echo($j); ?>" href="javascript:void(0)" class="mas-experiencia"><img src="images/plus.png" alt="m&aacute;s" /></a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
<?php } ?>

                                <h3><?php echo $lang->line(LANG_PREFIX."estudios")?></h3>
                                <div class="form-top">
                                    <div class="form-bottom">
                                        <div class="form-middle">
                                            <p class="striped">
                                                <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."nivel_estudios")?>:</label>
                                                <select tabindex="8" id="cmbNiveles_1" name="cmbNiveles_1" class="w-240">
                                                    <option value=""><?php echo $lang->line(LANG_PREFIX."nivel_estudios")?></option>
<?php
                                                    $rs = $oDBCommand->Rs("sp_niveles_estudio_combo", null);
                                                    while (!$rs->EOF()) {
?>
                                                        <option value="<?php echo($rs->Field("id_nivel_estudio")->Value()) ?>"><?php echo $lang->line(LANG_PREFIX.$rs->Field("i18n_string")->Value()) ?></option>
<?php
                                                        $rs->MoveNext();
                                                    }
                                                    unset($rs);
?>
                                                </select>
                                            </p>

                                            <p>
                                                <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."titulo")?>:</label>
                                                <input type="text" id="txtTitulo_1" name="txtTitulo_1" maxlength="50" class="w-240" />
                                            </p>

                                            <p class="striped">
                                                <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."area_estudio")?>:</label>
                                                <input type="text" id="txtArea_1" name="txtArea_1" maxlength="50" class="w-240" />
                                            </p>

                                            <p>
                                                <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."institucion")?>:</label>
                                                <input type="text" id="txtInstitucion_1" name="txtInstitucion_1" maxlength="50" class="w-240" />
                                            </p>

                                            <p class="striped">
                                                <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."periodo")?>:</label>
<?php selectFechas("cmbPeriodosDesdeEstudios_1",$stringDates); ?>
                                                <?php selectFechas("cmbPeriodosHastaEstudios_1",$stringDates); ?>
                                                </p>

                                                <p class="checkbox"><?php echo $lang->line(LANG_PREFIX."actualidad")?> <input type="checkbox" name="chkActualidadE_1" value="1"  class="radio margen" /></p>

                                                <p class="striped">

                                                    <p class="last">
                                                        <label class="bullet"><?php echo $lang->line(LANG_PREFIX."descripcion")?>:</label>
                                                        <textarea id="taEstudiosDescripcion_1" name="taEstudiosDescripcion_1"></textarea>
                                                    </p>

                                                    <p class="estudio last striped">
                                                        <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."agregar_estudio")?></label>
                                                        <a id="mas-estudio-1" href="javascript:void(0)" class="mas-estudio"><img src="images/plus.png" alt="m&aacute;s" /></a>
                                                    </p>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

<?php for ($j = 2; $j <= CANTIDAD; $j++) { ?>
                                        <div id="more-estudio-<?php echo($j); ?>" class="form-top more-estudio" style="display: none;">
                                                            <div class="form-bottom">
                                                                <div class="form-middle">
                                                                    <p class="striped">
                                                                        <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."nivel_estudios")?>:</label>
                                                                        <select tabindex="8" id="cmbNiveles_<?php echo($j); ?>" name="cmbNiveles_<?php echo($j); ?>" class="w-240">
                                                                            <option value=""><?php echo $lang->line(LANG_PREFIX."nivel_estudios")?></option>
<?php
                                                        $rs = $oDBCommand->Rs("sp_niveles_estudio_combo", null);
                                                        while (!$rs->EOF()) {
?>
                                                            <option value="<?php echo($rs->Field("id_nivel_estudio")->Value()) ?>"><?php echo $lang->line(LANG_PREFIX.$rs->Field("i18n_string")->Value()) ?></option>
<?php
                                                            $rs->MoveNext();
                                                        }
                                                        unset($rs);
?>
                                                    </select>
                                                </p>

                                                <p>
                                                    <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."titulo")?>:</label>
                                                    <input type="text" id="txtTitulo_<?php echo($j); ?>" name="txtTitulo_<?php echo($j); ?>" maxlength="50" class="w-240" />
                                                </p>

                                                <p class="striped">
                                                    <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."area_estudio")?>:</label>
                                                    <input type="text" id="txtArea_<?php echo($j); ?>" name="txtArea_<?php echo($j); ?>" maxlength="50" class="w-240" />
                                                </p>

                                                <p>
                                                    <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."institucion")?>:</label>
                                                    <input type="text" id="txtInstitucion_<?php echo($j); ?>" name="txtInstitucion_<?php echo($j); ?>" maxlength="50" class="w-240" />
                                                </p>

                                                <p class="striped">
                                                    <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."periodo")?>:</label>
<?php selectFechas("cmbPeriodosDesdeEstudios_$j",$stringDates); ?>
                                                <?php selectFechas("cmbPeriodosHastaEstudios_$j",$stringDates); ?>
                                                    </p>
                                                    <p class="checkbox"><?php echo $lang->line(LANG_PREFIX."actualidad")?> <input type="checkbox" name="chkActualidadE_<?php echo($j); ?>" value="1"  class="radio margen" /></p>

                                                    <p class="striped">

                                                        <p class="last">
                                                            <label class="bullet"><?php echo $lang->line(LANG_PREFIX."descripcion")?>:</label>
                                                            <textarea id="taEstudiosDescripcion_<?php echo($j); ?>" name="taEstudiosDescripcion_<?php echo($j); ?>"></textarea>
                                                        </p>

                                                        <p class="estudio last striped">
                                                            <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."agregar_estudio")?></label>
                                                            <a id="mas-estudio-<?php echo($j); ?>" href="javascript:void(0)" class="mas-estudio"><img src="images/plus.png" alt="m&aacute;s" /></a>
                                                        </p>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
<?php } ?>

                                    <h3><?php echo $lang->line(LANG_PREFIX."otros_conocimientos")?></h3>

                                    <div class="form-top">
                                        <div class="form-bottom">
                                            <div class="form-middle">

                                                <p class="idioma-tit striped">
    										<?php echo $lang->line(LANG_PREFIX."idiomas")?>
                                                </p>
                                                <p>
                                                    <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."idioma")?>:</label>
                                                    <select tabindex="8" id="cmbIdiomas_1" name="cmbIdiomas_1" class="w-240" onChange="Idioma(1);">
                                                        <option value=""><?php echo $lang->line(LANG_PREFIX."idioma")?></option>
<?php
                                                    $rs = $oDBCommand->Rs("sp_idiomas_combo", null);
                                                    while (!$rs->EOF()) {
?>
                                                        <option value="<?php echo($rs->Field("id_idioma")->Value()) ?>"><?php echo $lang->line(LANG_PREFIX.$rs->Field("i18n_string")->Value()) ?></option>
<?php
                                                        $rs->MoveNext();
                                                    }
                                                    unset($rs);
?>
                                                </select>
                                                <span id="idiomaOtro_1" style="display:none;"><input type="text" name="txtIdioma_1" name="txtIdioma_1" /></span>
                                            </p>
                                            <p class="striped">
                                                <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."institucion")?>:</label>
                                                <input type="text" id="txtInstitucion" name="txtInstitucion" maxlength="50" class="w-295" />
                                            </p>

<?php
                                                    $rs = $oDBCommand->Rs("sp_niveles_idioma_getall", null);
                                                    while (!$rs->EOF()) {
?>
                                                        <p class="idioma striped">
                                                            <label  class="bullet"><?php echo $lang->line(LANG_PREFIX.$rs->Field("i18n_string")->Value()) ?>:</label>
                                                            <input type="radio" value="bas" class="radio"  name="rdoIdioma_1_<?php echo($rs->Field("id_nivel_idioma")->Value()) ?>" />
                                                            <span><?php echo $lang->line(LANG_PREFIX."nivel_basico")?></span>
                                                            <input type="radio" value="int" class="radio"  name="rdoIdioma_1_<?php echo($rs->Field("id_nivel_idioma")->Value()) ?>" />
                                                            <span><?php echo $lang->line(LANG_PREFIX."nivel_medio")?></span>
                                                            <input type="radio" value="ava" class="radio"  name="rdoIdioma_1_<?php echo($rs->Field("id_nivel_idioma")->Value()) ?>" />
                                                            <span><?php echo $lang->line(LANG_PREFIX."nivel_avanzado")?></span>
                                                        </p>
<?php
                                                        $rs->MoveNext();
                                                    }
                                                    unset($rs);
?>

                                                    <p class="idioma last">
                                                        <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."agregar_idioma")?></label>
                                                        <a id="mas-idioma-1" href="javascript:void(0)" class="mas-idioma"><img src="images/plus.png" alt="m&aacute;s" /></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

<?php for ($j = 2; $j <= CANTIDAD; $j++) { ?>
                                            <div id="more-idioma-<?php echo($j); ?>" class="form-top more-idioma" style="display: none;">
                                                            <div class="form-bottom">
                                                                <div class="form-middle">
                                                                    <p class="striped">
                                                                        <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."idioma")?>:</label>
                                                                        <select tabindex="8" id="cmbIdiomas_<?php echo($j); ?>" name="cmbIdiomas_<?php echo($j); ?>" class="w-240" onChange="Idioma(<?php echo($j); ?>);">
                                                                            <option value=""><?php echo $lang->line(LANG_PREFIX."idioma")?></option>
<?php
                                                        $rs = $oDBCommand->Rs("sp_idiomas_combo", null);
                                                        while (!$rs->EOF()) {
?>
                                                            <option value="<?php echo($rs->Field("id_idioma")->Value()) ?>"><?php echo($rs->Field("nombre")->Value()) ?></option>
<?php
                                                            $rs->MoveNext();
                                                        }
                                                        unset($rs);
?>
                                                    </select>
                                                    <span id="idiomaOtro_<?php echo($j); ?>" style="display:none;"><input type="text" name="txtIdioma_<?php echo($j); ?>" name="txtIdioma_<?php echo($j); ?>" /></span>
                                                </p>
                                                <p class="striped">
                                                    <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."institucion")?>:</label>
                                                    <input type="text" id="txtInstitucion_<?php echo($j); ?>" name="txtInstitucion_<?php echo($j); ?>" maxlength="50" class="w-295" />
                                                </p>

<?php
                                                        $rs = $oDBCommand->Rs("sp_niveles_idioma_getall", null);
                                                        while (!$rs->EOF()) {
?>
                                                            <p class="idioma striped">
                                                                <label class="bullet"><?php echo $lang->line(LANG_PREFIX.$rs->Field("i18n_string")->Value()) ?>:</label>
                                                                <input type="radio" value="bas" class="radio"  name="rdoIdioma_<?php echo($j); ?>_<?php echo($rs->Field("id_nivel_idioma")->Value()) ?>" />
                                                                <span><?php echo $lang->line(LANG_PREFIX."nivel_basico")?></span>
                                                                <input type="radio" value="int" class="radio"  name="rdoIdioma_<?php echo($j); ?>_<?php echo($rs->Field("id_nivel_idioma")->Value()) ?>" />
                                                                <span><?php echo $lang->line(LANG_PREFIX."nivel_medio")?></span>
                                                                <input type="radio" value="ava" class="radio"  name="rdoIdioma_<?php echo($j); ?>_<?php echo($rs->Field("id_nivel_idioma")->Value()) ?>" />
                                                                <span><?php echo $lang->line(LANG_PREFIX."nivel_avanzado")?></span>
                                                            </p>
<?php
                                                            $rs->MoveNext();
                                                        }
                                                        unset($rs);
?>

                                                        <p class="idioma last striped">
                                                            <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."agregar_idioma")?></label>
                                                            <a id="mas-idioma-<?php echo($j); ?>" href="javascript:void(0)" class="mas-idioma"><img src="images/plus.png" alt="m&aacute;s" /></a>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
<?php } ?>

                                        <div class="form-top">
                                            <div class="form-bottom">
                                                <div class="form-middle">

                                                    <p class="idioma-tit">
        										<?php echo $lang->line(LANG_PREFIX."cursos")?> I
                                                    </p>

                                                    <p class="striped">
                                                        <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."nombre")?>:</label>
                                                        <input type="text" id="txtCurso_nombre_1" name="txtCurso_nombre_1" maxlength="50" class="w-295" />
                                                    </p>

                                                    <p>
                                                        <label class="bullet"><?php echo $lang->line(LANG_PREFIX."descripcion")?>:</label>
                                                        <textarea id="taCurso_descripcion_1" name="taCurso_descripcion_1"></textarea>
                                                    </p>

                                                    <p class="idioma-tit striped">
        										<?php echo $lang->line(LANG_PREFIX."cursos")?> II
                                                    </p>

                                                    <p>
                                                        <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."nombre")?>:</label>
                                                        <input type="text" id="txtCurso_nombre_2" name="txtCurso_nombre_2" maxlength="50" class="w-295" />
                                                    </p>

                                                    <p class="striped">
                                                        <label class="bullet"><?php echo $lang->line(LANG_PREFIX."descripcion")?>:</label>
                                                        <textarea id="taCurso_descripcion_2" name="taCurso_descripcion_2"></textarea>
                                                    </p>
                                                    <p class="idioma last">
                                                        <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."agregar_curso")?></label>
                                                        <a id="mas-curso-1" href="javascript:void(0)" class="mas-curso"><img src="images/plus.png" alt="m&aacute;s" /></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div><!--form-top-->

<?php for ($j = 3; $j <= CANTIDAD; $j++) { ?>
                                        <div id="more-curso-<?php echo($j - 1); ?>" class="form-top more-curso" style="display: none;">
                                                            <div class="form-bottom">
                                                                <div class="form-middle">

                                                                    <p class="striped">
                                                                        <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."nombre")?>:</label>
                                                                        <input type="text" id="txtCurso_nombre_<?php echo($j); ?>" name="txtCurso_nombre_<?php echo($j); ?>" maxlength="50" class="w-295" />
                                                                    </p>

                                                                    <p class="last striped">
                                                                        <label class="bullet"><?php echo $lang->line(LANG_PREFIX."descripcion")?>:</label>
                                                                        <textarea id="taCurso_Descripcion_<?php echo($j); ?>" name="taCurso_descripcion_<?php echo($j); ?>"></textarea>
                                                                    </p>

                                                                    <p class="idioma last striped">
                                                                        <label  class="bullet"><?php echo $lang->line(LANG_PREFIX."agregar_curso")?></label>
                                                                        <a id="mas-curso-<?php echo($j); ?>" href="javascript:void(0)" class="mas-curso"><img src="images/plus.png" alt="m&aacute;s" /></a>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
<?php } ?>

                                                    <h3><?php echo $lang->line(LANG_PREFIX."remuneracion")?></h3>

                                                    <div class="form-top">
                                                        <div class="form-bottom">
                                                            <div class="form-middle">


                                                                <p class="striped">
                                                                    <label  class="bullet remu"><?php echo $lang->line(LANG_PREFIX."cual_es_remuneracion")?></label>
                                                                    <span>$</span> <input type="text" id="txtRemuneracion" name="txtRemuneracion" class="w-140" />
                                                                </p>
                                                                <p>
                                                                    <label  class="bullet remu"><?php echo $lang->line(LANG_PREFIX."modalidad_contratacion")?></label>
                                                                    <input type="text" id="txtModalidad" name="txtModalidad" class="w-140-2" />
                                                                </p>

                                                            </div>
                                                        </div>
                                                    </div><!--form-top-->


                                                    <p class="<?php echo $lang->line(LANG_PREFIX."class-boton-enviar")?>"">
                                                        <input type="submit" id="submit" value="Submit" />
                                                    </p>
                                                    <p class="<?php echo $lang->line(LANG_PREFIX."class-boton-limpiar")?>">
                                                        <input type="reset" id="resetear" value="Reset" />
                                                    </p>
                                                </fieldset>
                                            </form>
                                        </div> <!--contenedor-cv-->
                                    </div><!--central-->
                                </div><!--inside-->
                            </div>
                          <div id="sector4">
                              <div id="footer" class="caja">
                                  <div id="copyright">  Copyright ECC Extreme Cloud Computing SA © 2015 - Buenos Aires - Argentina  </div>
                              </div>
                          </div>
                        </body>
                    </html>
