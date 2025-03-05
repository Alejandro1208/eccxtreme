<?php

require_once("includes/common.inc.php");

# ---------------------------------------------
# DBCommand.
# ---------------------------------------------
$objDBCommand = new DBCommand(DB_HOST, DB_USER, DB_PASS, DB_NAME, PATH_DESCRIPTOR);
# ---------------------------------------------
# ---------------------------------------------
# Constantes.
# ---------------------------------------------
subAuthenticationAndSetConstants();
# ---------------------------------------------
# ---------------------------------------------
# Variables.
# ---------------------------------------------
$intId = fncRequest(REQUEST_METHOD_POST, "hdn_id", REQUEST_TYPE_INTEGER, 0);
# ---------------------------------------------
# ---------------------------------------------
# Procedimientos.
# ---------------------------------------------

function Calificacion($s) {
    switch ($s) {
        case("bas"): {
                return("Basico");
                break;
            }
        case("int"): {
                return("Intermedio");
                break;
            }
        case("ava"): {
                return("Avanzado");
                break;
            }
    }
}

//formate una fecha desde un string yyyy-mm-dd a dd-mm-yyyy
function format_date($date_string) {
    $values = explode("-", $date_string);
    if (count($values) == 3) {
        $values = array_reverse($values);
        return implode("/", $values);
    }
    return null;
}

//echo format_date("2008-01-01");
//exit();
define("TABLE_MARGIN", 1200);
define("CELL_WIDTH", 76500);
define("HR_URL", "backend/images/cvs/hr.jpg");

# ---------------------------------------------
# ---------------------------------------------
# Proceso.
# ---------------------------------------------
$PHPWord = new PHPWord();

# Fonts style.
$PHPWord->addFontStyle(
        "fStyleTitulo",
        array(
            "name" => "Verdana",
            "bold" => true,
            "italic" => false,
            "underline" => PHPWord_Style_Font::UNDERLINE_SINGLE,
            "size" => 18
        )
);
$PHPWord->addFontStyle(
        "fStyleTituloPerfil",
        array(
            "name" => "Verdana",
            "bold" => false,
            "italic" => false,
            "size" => 18
        )
);
$PHPWord->addFontStyle(
        "fStyleSubTitulo",
        array(
            "name" => "Verdana",
            "bold" => true,
            "italic" => false,
            "underline" => PHPWord_Style_Font::UNDERLINE_SINGLE,
            "size" => 15
        )
);
$PHPWord->addFontStyle(
        "fStyleSubTituloSinUnderline",
        array(
            "name" => "Verdana",
            "bold" => true,
            "italic" => false,
            "underline" => false,
            "size" => 15,
        )
);
$PHPWord->addFontStyle(
        "fStyleItem",
        array(
            "name" => "Verdana",
            "bold" => false,
            "italic" => true,
            "underline" => false,
            "size" => 10
        )
);
$PHPWord->addFontStyle(
        "fStyleItemBold",
        array(
            "name" => "Verdana",
            "bold" => true,
            "italic" => true,
            "underline" => false,
            "size" => 10
        )
);
$PHPWord->addFontStyle(
        "fStyleFooter",
        array(
            "name" => "Verdana",
            "bold" => false,
            "italic" => false,
            "underline" => false,
            "size" => 9
        )
);

# Paragraph style.
$PHPWord->addParagraphStyle(
        "pStyleTitulo",
        array(
            "align" => "center",
            "spaceAfter" => 5
        )
);
$PHPWord->addParagraphStyle(
        "pStyleSubTitulo",
        array(
            "align" => "left"
        )
);
$PHPWord->addParagraphStyle(
        "pStyleItem",
        array(
            "align" => "left"
        )
);
$PHPWord->addParagraphStyle(
        "pStyleFooter",
        array(
            "align" => "right"
        )
);

function esPerfilSAP($iPerfil) {
    return ($iPerfil == 1) || ($iPerfil == 2);
}

$aStyle = array(
    "marginLeft" => 500,
    "marginRight" => 500,
    "marginTop" => 500,
    "marginBottom" => 500);

$hrImageStyle = array("align" => "center", 'width' => 650, "height" => 5);

$dataTableStyle = array("cellMarginLeft" => TABLE_MARGIN);

$oSection = $PHPWord->createSection($aStyle);
$oHeader = $oSection->createHeader();
$oHeader->addImage(PATH . "backend/images/cvs/header.jpg");

$oFooter = $oSection->createFooter();
$oFooter->addPreserveText("Pagina {PAGE} de {NUMPAGES}", "fStyleFooter", "pStyleFooter");
$oFooter->addImage(PATH . "backend/images/cvs/footer.jpg", array("marginLeft" => 0,
    "marginRight" => 1500,
    "marginTop" => 500,
    "marginBottom" => 500));

$rs = $GLOBALS["objDBCommand"]->Rs("sp_cvs_get", array("intId" => $intId));

$iPerfil = $rs->Field("fk_id_perfil")->Value();

$sPerfil = $rs->Field("perfil2")->Value();
if (esPerfilSAP($iPerfil)) {
    if ($rs->Field("tiene_certificado_sap")->Value()) {
        $sPerfil .= " Certificado";
    }
    if (strlen($rs->Field("perfil")->Value()) > 0) {
        $sPerfil .= " / " . $rs->Field("perfil")->Value();
    }
//} else {
//    $sPerfil = $rs->Field("perfil")->Value();
}
$nombre = $rs->Field("nya")->Value(); // lo uso luego para el nombre de archivo

$oSection->addText("C.V. DE " . mb_strtoupper($nombre, 'UTF-8'), "fStyleTitulo", "pStyleTitulo");
$oSection->addText($sPerfil, "fStyleTituloPerfil", "pStyleTitulo");
$oSection->addTextBreak(1);

$tablaDatosPersonales = $oSection->addTable($dataTableStyle);
$tablaDatosPersonales->addRow();
$cellDatos = $tablaDatosPersonales->addCell(CELL_WIDTH);
$cellDatos->addText("Datos personales", "fStyleSubTitulo", "pStyleSubTitulo");
$cellDatos->addText("Nombre y apellido: " . mb_strtoupper($nombre, 'UTF-8'), "fStyleItem", "pStyleItem");

$fecha_nacimiento = $rs->Field("fecha_nac")->Value();
if (strlen($fecha_nacimiento))
    $cellDatos->addText("Fecha de nacimiento: " . format_date($fecha_nacimiento), "fStyleItem", "pStyleItem");

//estado civil es obligatorio debería venir siempre con algo
$cellDatos->addText("Estado civil: " . $rs->Field("estado_civil")->Value(), "fStyleItem", "pStyleItem");

$cant_hijos = $rs->Field("hijos")->Value();
if ($cant_hijos == 0)
    $cant_hijos = "ninguno";
$cellDatos->addText("Cantidad de hijos: " . $cant_hijos, "fStyleItem", "pStyleItem");

$tipo_documento = $rs->Field("tipo_documento")->Value();
$documento = $rs->Field("numero_documento")->Value();
if (strlen($documento))
    $cellDatos->addText("Documento: " . $tipo_documento . " " . $documento, "fStyleItem", "pStyleItem");

$cuil = $rs->Field("cuil")->Value();
if (strlen($cuil))
    $cellDatos->addText("CUIL: " . $cuil, "fStyleItem", "pStyleItem");

$telefono = $rs->Field("telefono")->Value();
if (strlen($telefono))
    $cellDatos->addText("Teléfono: " . $telefono, "fStyleItem", "pStyleItem");

$calle = $rs->Field("calle")->Value();
$nro_calle = $rs->Field("numero")->Value();
$piso = $rs->Field("piso")->Value();
$depto = $rs->Field("depto")->Value();
if (strlen($calle))
    $cellDatos->addText("Domicilio: " . "calle " . $calle . " n° " . $nro_calle . " " . $piso . " " . $depto, "fStyleItem", "pStyleItem");

$codigo_postal = $rs->Field("cp")->Value();
if (strlen($codigo_postal))
    $cellDatos->addText("CP: " . $codigo_postal, "fStyleItem", "pStyleItem");

$barrio = $rs->Field("barrio")->Value();
if (strlen($barrio))
    $cellDatos->addText("Barrio: " . $barrio, "fStyleItem", "pStyleItem");

$provincia = $rs->Field("provincia")->Value();
if (strlen($provincia))
    $cellDatos->addText("Provincia: " . $provincia, "fStyleItem", "pStyleItem");

$email = $rs->Field("email")->Value();
if (strlen($email))
    $cellDatos->addText("Email: " . $email, "fStyleItem", "pStyleItem");


$foto = $rs->Field("ruta_foto")->Value();

if ($foto != "") {
    $cellImage = $tablaDatosPersonales->addCell(3000);
    $imageStyle = array('width' => 150, 'height' => 150, 'align' => 'right');
    $cellImage->addImage(PATH . "/upload/" . $foto, $imageStyle);
}

$cellDatos->addImage(PATH . HR_URL, $hrImageStyle);
$oSection->addTextBreak(1);


# Perfiles.
$tablaPerfil = $oSection->addTable($dataTableStyle);
$tablaPerfil->addRow();
$cellDatos = $tablaPerfil->addCell(CELL_WIDTH);
$cellDatos->addText("Perfil", "fStyleSubTitulo", "pStyleSubTitulo");
$cellDatos->addText($sPerfil, "fStyleItem", "pStyleItem");

if (esPerfilSAP($iPerfil))
    if ($rs->Field("tiene_certificado_sap")->Value() == 1) {
        $cellDatos->addText("Certificado", "fStyleItemBold", "pStyleItem");
    }

if (!esPerfilSAP($iPerfil)) {
    $cellDatos->addText("Tiene conocimientos SAP: " . (($rs->Field("tiene_conocimientos_sap")->Value() == 1) ? "Si" : "No"), "fStyleItem", "pStyleItem");
}

$conocimParagraph = split("\n", $rs->Field("conocimientos")->Value());
$cellDatos->addText("Conocimientos: " . $conocimParagraph[0], "fStyleItem", "pStyleItem");
foreach (array_slice($conocimParagraph, 1) as $p) {
    $p = trim(str_replace("", "", $p));
    $cellDatos->addText($p, "fStyleItem", "pStyleItem");
}

$cellDatos->addImage(PATH . HR_URL, $hrImageStyle);
$oSection->addTextBreak(1);

# Estudios.
$rs = $GLOBALS["objDBCommand"]->Rs("sp_estudios_xCv", array("intFk_id_cv" => $intId));

if (!$rs->EOF()) {

    $tablaEstudios = $oSection->addTable($dataTableStyle);
    $tablaEstudios->addRow();
    $cellDatos = $tablaEstudios->addCell(CELL_WIDTH);
    $cellDatos->addText("Estudios", "fStyleSubTitulo", "pStyleSubTitulo");

    while (!$rs->EOF()) {
        $periodo_desde = format_date($rs->Field("fecha_desde")->Value());
        $periodo_hasta = format_date($rs->Field("fecha_hasta")->Value());
        if (strlen($periodo_desde) || strlen($periodo_hasta))
            $cellDatos->addText("Período: " . $periodo_desde . " - " . $periodo_hasta, "fStyleItem", "pStyleItem");

        $nivel = $rs->Field("nivel")->Value();
        if (strlen($nivel))
            $cellDatos->addText("Nivel: " . $nivel, "fStyleItem", "pStyleItem");

        $titulo = $rs->Field("titulo")->Value();
        if (strlen($titulo))
            $cellDatos->addText("Titulo: " . $titulo, "fStyleItem", "pStyleItem");

        $area = $rs->Field("area")->Value();
        if (strlen($area))
            $cellDatos->addText("Área de estudio: " . $area, "fStyleItem", "pStyleItem");

        $institucion = $rs->Field("institucion")->Value();
        if (strlen($institucion))
            $cellDatos->addText("Institución: " . $institucion, "fStyleItem", "pStyleItem");

        $descripcion = $rs->Field("descripcion")->Value();
        if (strlen($descripcion))
            $cellDatos->addText("Descripción: " . $descripcion, "fStyleItem", "pStyleItem");

        $cellDatos->addTextBreak(1);
        $rs->MoveNext();
    }
    $cellDatos->addImage(PATH . HR_URL, $hrImageStyle);
    $oSection->addTextBreak(1);
}
unset($rs);

# Remuneracion y modalidad de contrato.
$rs = $GLOBALS["objDBCommand"]->Rs("sp_remuneracion_cvs", array("id_cv" => $intId));
$modalidad = $rs->Field("nombre")->Value();
$remuneracion = $rs->Field("remuneracion")->Value();

if ($remuneracion != 0) {
    $tablaRemuneracion = $oSection->addTable($dataTableStyle);
    $tablaRemuneracion->addRow();
    $cellDatos = $tablaRemuneracion->addCell(CELL_WIDTH);

    $cellDatos->addText("Remuneración bruta", "fStyleSubTitulo", "pStyleSubTitulo");
    $cellDatos->addText("$" . $remuneracion, "fStyleItem", "pStyleItem");

    $cellDatos->addImage(PATH . HR_URL, $hrImageStyle);
    $oSection->addTextBreak(1);
}
unset($rs);

// Modalidad de contratación es sólo visible para XTREME
//if ($modalidad!=null){
//    $oSection->addText("Modalidad de contratacion", "fStyleSubTitulo", "pStyleSubTitulo");
//    $oSection->addText($modalidad, "fStyleItem", "pStyleItem");
//    $oSection->addTextBreak(1);
//}
# Experiencia laboral.

$rs = $GLOBALS["objDBCommand"]->Rs("sp_experiencias_laboral_xCv", array("intFk_id_cv" => $intId));

if (!$rs->EOF()) {

    $tablaExperiencia = $oSection->addTable($dataTableStyle);
    $tablaExperiencia->addRow();
    $cellDatos = $tablaExperiencia->addCell(CELL_WIDTH);
    $cellDatos->addText("Experiencia laboral", "fStyleSubTitulo", "pStyleSubTitulo");

    while (!$rs->EOF()) {

        $periodo_desde = format_date($rs->Field("fecha_desde")->Value());
        $periodo_hasta = format_date($rs->Field("fecha_hasta")->Value());
        if (strlen($periodo_desde) || strlen($periodo_hasta))
            $cellDatos->addText("Periodo: " . $periodo_desde . " - " . $periodo_hasta, "fStyleItem", "pStyleItem");

        $cargo = $rs->Field("cargo")->Value();
        if (strlen($cargo))
            $cellDatos->addText("Cargo: " . $cargo, "fStyleItem", "pStyleItem");

        $compania = $rs->Field("compania")->Value();
        if (strlen($compania))
            $cellDatos->addText("Compañía: " . $compania, "fStyleItemBold", "pStyleItem");

        $cliente = $rs->Field("cliente")->Value();
        if (strlen($cliente))
            $cellDatos->addText("Cliente: " . $cliente, "fStyleItemBold", "pStyleItem");

        $pais = $rs->Field("pais")->Value();
        if (strlen($pais))
            $cellDatos->addText("Ubicación: " . $pais, "fStyleItem", "pStyleItem");

        $contexto = $rs->Field("contexto_proyecto")->Value();
        if (strlen($contexto))
            $cellDatos->addText("Contexto del proyecto: " . $contexto, "fStyleItem", "pStyleItem");

        $actividades = $rs->Field("actividades")->Value(); //var_dump(split("\n",$actividades));exit();
        if (strlen($actividades)) {
            $paragraphs = split("\n", $actividades);
            $cellDatos->addText("Actividades: " . $paragraphs[0], "fStyleItem", "pStyleItem");
            $paragraphs = array_slice($paragraphs, 1);
            foreach ($paragraphs as $p){
                $p = trim(str_replace("", "", $p));
                $cellDatos->addText($p, "fStyleItem", "pStyleItem");
            }
                }
        $cellDatos->addTextBreak(2);
        $rs->MoveNext();
    }
    $cellDatos->addImage(PATH . HR_URL, $hrImageStyle);
    $oSection->addTextBreak(1);
}
unset($rs);

# Otros conocimientos.
$tablaOtrosCoconimientos = $oSection->addTable($dataTableStyle);
$tablaOtrosCoconimientos->addRow();
$cellDatos = $tablaOtrosCoconimientos->addCell(CELL_WIDTH);
$cellDatos->addText("Otros conocimientos", "fStyleSubTitulo", "pStyleSubTitulo");
$oSection->addTextBreak(1);

# Idiomas.
$rs = $GLOBALS["objDBCommand"]->Rs("sp_cvs_idiomas_niveles_xCv", array("intFk_id_cv" => $intId));
if (!$rs->EOF()) {

    $tablaIdiomas = $oSection->addTable($dataTableStyle);
    $tablaIdiomas->addRow();
    $cellDatos = $tablaIdiomas->addCell(CELL_WIDTH);
    $cellDatos->addText("Idiomas", "fStyleSubTituloSinUnderline", "pStyleSubTitulo");

    while (!$rs->EOF()) {
        $sIdioma = $rs->Field("idioma")->Value();
        $cellDatos->addText($sIdioma, "fStyleItemBold", "pStyleItem");
        $institucion = $rs->Field("institucion")->Value();
        if (strlen($institucion))
            $cellDatos->addText("Institución: " . $institucion, "fStyleItem", "pStyleItem");
        while ((!$rs->EOF()) && ($rs->Field("idioma")->Value() == $sIdioma)) {
            $cellDatos->addText($rs->Field("nivel")->Value() . ": " . Calificacion($rs->Field("calificacion")->Value()), "fStyleItem", "pStyleItem");
            $rs->MoveNext();
        }
        $cellDatos->addTextBreak(1);
    }

    $oSection->addTextBreak(1);
}
unset($rs);

# Cursos.
$rs = $GLOBALS["objDBCommand"]->Rs("sp_cursos_xCv", array("intFk_id_cv" => $intId));
if (!$rs->EOF()) {

    $tablaCursos = $oSection->addTable($dataTableStyle);
    $tablaCursos->addRow();
    $cellDatos = $tablaCursos->addCell(CELL_WIDTH);
    $cellDatos->addText("Cursos", "fStyleSubTituloSinUnderline", "pStyleSubTitulo");

    while (!$rs->EOF()) {
        $cellDatos->addText(trim(str_replace("", "", $rs->Field("nombre")->Value())), "fStyleItemBold", "pStyleItem");
        $descripcion = $rs->Field("descripcion")->Value();
        if (strlen($descripcion)) {
            $cellDatos->addText("Descripción:", "fStyleItem", "pStyleItem");
            $descripcionParagraphs = split("\n", $descripcion);
            foreach ($descripcionParagraphs as $p){
                $p = trim(str_replace("", "", $p));
                $cellDatos->addText($p, "fStyleItem", "pStyleItem");

            }            $cellDatos->addTextBreak(1);
        } else {
            $cellDatos->addTextBreak(1);
        }
        $rs->MoveNext();
    }
    $oSection->addTextBreak(1);
}
unset($rs);

# ---------------------------------------------
# Salvo.
# ---------------------------------------------
$sFile = PATH . "backend/cvs/" . $nombre . ".docx";
$oWriter = PHPWord_IOFactory::createWriter($PHPWord, "Word2007");
$oWriter->save($sFile);
# ---------------------------------------------
# ---------------------------------------------
# Header.
# ---------------------------------------------
$nombre = replace_accents($nombre);
header('Content-Type: application/docx');
header('Content-Disposition: attachment; filename="' . $nombre . '.docx"');
header('Content-Length: ' . filesize($sFile));
readfile($sFile);
# ---------------------------------------------

subFileDelete($sFile);

# ---------------------------------------------
# ---------------------------------------------
# DBCommand.
# ---------------------------------------------
unset($objDBCommand);
# ---------------------------------------------

function replace_accents($string) {
    return str_replace(array('à', 'á', 'â', 'ã', 'ä', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý'), array('a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y'), $string);
}
?>

