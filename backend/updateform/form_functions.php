<?php

$GLOBALS["objDBCommand"] = new DBCommand(DB_HOST, DB_USER, DB_PASS, DB_NAME, PATH_DESCRIPTOR);

function sanitize($data) {
// remove whitespaces (not a must though)
    $data = trim($data);

// apply stripslashes if magic_quotes_gpc is enabled
    if (get_magic_quotes_gpc ()) {
        $data = stripslashes($data);
    }

// a mySQL connection is required before using this function
    $data = mysql_escape_string($data);

    return $data;
}

//options es un aray de la forma
//value => description
function generateOptions($options, $selected = NULL, $reverse=FALSE) {
    $strOptions = "";
    if ($reverse)
        $options = array_reverse($options, TRUE);
    foreach ($options as $k => $v) {
        $sel = ($selected == NULL) ? '' : ( ($selected == $k) ? 'selected' : '' );
        $strOptions = $strOptions . '<option value="' . $k . '" ' . $sel . '>' . $v . '</option>';
    }
    return $strOptions;
}

function getYearsOptions() {
    $options = array();
    $iFrom = date("Y") - 50;
    $iTo = date("Y");
    return getOptionsRange($iFrom, $iTo, TRUE);
}

function getDaysOptions() {
    return getOptionsRange(1, 31, TRUE);
}

function getHijosOptions() {
    return getOptionsRange(1, 10, TRUE);
}

function getCmbCuils1Options() {
    return array(20 => 20, 23 => 23, 27 => 27, 30 => 30);
}

function getCmbTiposDocumentosOptions() {
    //sacar de DB #TODO
    return array(2 => "CI", 1 => "DNI");
}

function getCmbCuils3Options() {
    return getOptionsRange(0, 9);
}

function getFechasNacimientoM() {
    return array(
        "1" => "Enero",
        "2" => "Febrero",
        "3" => "Marzo",
        "4" => "Abril",
        "5" => "Mayo",
        "6" => "Junio",
        "7" => "Julio",
        "8" => "Agosto",
        "9" => "Septiembre",
        "10" => "Octubre",
        "11" => "Noviembre",
        "12" => "Diciembre"
    );
}

function deleteEntrevista($identrevista) {
    $objDB = $GLOBALS['objDBCommand'];
    $res1 = $objDB->Execute("sp_delete_entrevista", array("id_entrevista" => $identrevista));
    return $res1 && $res2;
}

function getCVDataFromDB($id) {
    $objDB = $GLOBALS['objDBCommand'];
    return $objDB->Rs("sp_cvs_get", array("intId" => $id));
}

function updateCVDataToDB($data, $cv_id) {
    $objDB = $GLOBALS['objDBCommand'];
    $params = array("sNyA" => $data['txtNyA'],
        "dFechaNac" => getStringFecha($data['cmbFechasNacimientoA'], $data['cmbFechasNacimientoM'], $data['cmbFechasNacimientoD']),
        "iFk_id_estado_civil" => $data['rdoEstadoCivil'],
        "iHijos" => $data['cmbHijos'],
        "iFk_id_tipo_documento" => $data['cmbTiposDocumento'],
        "iNumeroDocumento" => $data['txtDocumento'],
        "iCuil" => $data['txtCuil'],
        "iTelefono" => $data['txtTelefono'],
        "sCalle" => $data['txtCalle'],
        "iNumero" => $data['txtNro'],
        "sPiso" => $data['txtPiso'],
        "sDepto" => $data['txtDepto'],
        "sCp" => $data['txtCp'],
        "sBarrio" => $data['txtBarrio'],
        "iFk_id_provincia" => $data['cmbProvincias'],
        "sEmail" => $data['txtEmail'],
        "iFk_id_perfil" => $data['cmbPerfiles'],
        "sPerfil" => ($data['chkPerfiles'] != NULL) ? implode(",", $data['chkPerfiles']) : "",
        "sPerfilSapOtro" => $data['txtPerfilSapOtro'],
        "bTieneConocimientosSap" => $data['chkConocimientosSap'],
        "bTieneCertificadoSap" => $data['rdoCertificadoSap'],
        "sConocimientos" => $data['taConocimientos'],
        "cRemuneracion" => $data['txtRemuneracion'],
        "iModalidad" => $data['cmbTipoContratacion'],
        "id_cv" => $cv_id);
        $params['sPerfil'].= $data['txtPerfilSapOtro']!=""?",".$data['txtPerfilSapOtro']:"";
        if((strlen($params['sPerfil'])>0) && ($params['sPerfil'][0] == ",") )
            $params['sPerfil'] = substr($params['sPerfil'],1,strlen($params['sPerfil']));
    return $objDB->Execute("sp_cvs_update", $params);
}

function getStringFecha($sa, $sm, $sd) {
    if (($sd == 0) || ($sm == 0) || ($sa == 0)) {
        return null;
    } else {
        return("$sa-$sm-$sd");
    }
}

function getCmbOptions($sp_name, $id_field_name, $description_field_name, $select_fields=NULL) {
    $options = array();
    $objDB = $GLOBALS['objDBCommand'];
    $rs = $objDB->Rs($sp_name, $select_fields);
    while (!$rs->EOF()) {
        $options[$rs->Field($id_field_name)->Value()] = $rs->Field($description_field_name)->Value();
        $rs->MoveNext();
    }
    unset($rs);
    return $options;
}

function getDatosEntrevistas($id_cv) {

    //obtengo entrevistas
    $entrevistas = array();
    $i = 0;
    $objDB = $GLOBALS['objDBCommand'];
    $rs = $objDB->Rs("sp_entrevistas_cv", array('id_cv' => $id_cv));
    while (!$rs->EOF()) {
        $e = array();
        $e['id_entrevista'] = $rs->Field("id_entrevista")->Value();
        $e['organizacion'] = $rs->Field("organizacion")->Value();
        $e['contacto'] = $rs->Field("contacto")->Value();
        $e['fecha'] = $rs->Field("fecha")->Value();
        $e['comentario'] = $rs->Field("comentario")->Value();
        $e['nivel_tecnico'] = $rs->Field("nivel_tecnico")->Value();
        $e['comentario_tecnico'] = $rs->Field("comentario_tecnico")->Value();
        $e['nivel_idiomas'] = $rs->Field("nivel_idiomas")->Value();
        $e['comentario_idiomas'] = $rs->Field("comentario_idiomas")->Value();
        $e['nivel_presentacion'] = $rs->Field("nivel_presentacion")->Value();
        $e['comentario_presentacion'] = $rs->Field("comentario_presentacion")->Value();
        $e['aprobado'] = $rs->Field("aprobado")->Value();
        $e['observaciones'] = $rs->Field("observaciones")->Value();
        $entrevistas[$i] = $e;
        $i++;
        $rs->MoveNext();
    }

    unset($rs);
    return $entrevistas;
    //obtengo calificaciones para las entrevistas
}

function getDatosEntrevista($id_entrevista) {

    //obtengo entrevista
    $e = array();
    $objDB = $GLOBALS['objDBCommand'];
    $rs = $objDB->Rs("sp_entrevista_id", array('id_entrevista' => $id_entrevista));
    if (!$rs->EOF()) {
        $e['id_entrevista'] = $rs->Field("id_entrevista")->Value();
        $e['organizacion'] = $rs->Field("organizacion")->Value();
        $e['contacto'] = $rs->Field("contacto")->Value();
        $e['fecha'] = $rs->Field("fecha")->Value();
        $e['comentario'] = $rs->Field("comentario")->Value();
        $e['nivel_tecnico'] = $rs->Field("nivel_tecnico")->Value();
        $e['comentario_tecnico'] = $rs->Field("comentario_tecnico")->Value();
        $e['nivel_idiomas'] = $rs->Field("nivel_idiomas")->Value();
        $e['comentario_idiomas'] = $rs->Field("comentario_idiomas")->Value();
        $e['nivel_presentacion'] = $rs->Field("nivel_presentacion")->Value();
        $e['comentario_presentacion'] = $rs->Field("comentario_presentacion")->Value();
        $e['aprobado'] = $rs->Field("aprobado")->Value();
        $e['observaciones'] = $rs->Field("observaciones")->Value();

        return $e;
    }
    unset($rs);
    return NULL;
}

function getCmbNivelesEntrevista() {
    return getCmbOptions("sp_niveles_entrevista", "id_nivel_entrevista", "descripcion");
}

function getCmbProvinciasOptions() {
    return getCmbOptions("sp_provincias_combo", "id_provincia", "nombre");
}

function getCmbPaisesOptions() {
    return getCmbOptions("sp_paises_combo", "id_pais", "nombre");
}

function getCmbNivelesEstudioOptions() {
    return getCmbOptions("sp_niveles_estudio", "id_nivel_estudio", "nombre");
}

function getCmbPerfiles() {
    return getCmbOptions("sp_perfiles_combo", "id_perfil", "nombre");
}

function getCmbTiposContratacionOptions() {
    return getCmbOptions("sp_tipos_contratacion_combo", "id_tipo_contratacion", "nombre");
}

function getRdoEstadoCivil() {
    return getCmbOptions("sp_estado_civil_combo", "id_estado_civil", "nombre");
}

function getCmbIdiomasOptions() {
    return getCmbOptions("sp_idiomas", "id_idioma", "nombre");
}

function getCmbNivelesIdiomaOptions() {
    return getCmbOptions("sp_niveles_idioma_combo", "id_nivel_idioma", "nombre");
}

function getCalifNivelesIdiomas($idCv, $idIdioma) {
    return getCmbOptions('sp_niveles_idioma_cv', 'fk_id_nivel', 'calificacion', array("id_cv" => $idCv, "id_idioma" => $idIdioma));
}

function getOptionsRange($from, $to, $indexIsValue=false) {
    $options = array();
    for ($i = $from; $i <= $to; $i++) {
        $ind = $i;
        if (!$indexIsValue)
            $ind = $ind - $from;
        $options["$ind"] = $i;
    }
    return $options;
}

function generateCheckHTML($name, $options, $valuesChecked) {
    $checkStr = "";
    $checked ="";
    foreach ($options as $k => $v) {
        if ($valuesChecked != NULL)
            $checked = (in_array($k, $valuesChecked)) ? 'checked="true"' : '';
        $checkStr = $checkStr . '<span>' . $v . '</span><input type="checkbox" name="' . $name . '" value="' . $k . '" ' . $checked . '/>&nbsp;&nbsp;&nbsp;';
    }
    return $checkStr;
}

function generateChoiceHTML($name, $options, $default) {
    $choiceStr = "";
    foreach ($options as $k => $v) {
        $checked = ($default == $k) ? 'checked' : '';
        $choiceStr = $choiceStr . '<span>' . $v . '</span><input type="radio" name="' . $name . '" value="' . $k . '" ' . $checked . '/>';
    }
    return $choiceStr;
}

function getOptionsSAPModules() {
    return array(
        "FI" => "FI",
        "SD" => "SD",
        "CO" => "CO",
        "FI-CO" => "FI-CO",
        "MM" => "MM",
        "Basis" => "Basis",
        "Seguridad" => "Seguridad",
        "HR"=>"HR",
        "BW"=>"BW"
    );
}

function getOptionsYesNo() {
    return array("1" => "Si", "0" => "No");
}

function getExperienceArray($id) {
    $exp = array();
    $i = 0;
    $objDB = $GLOBALS['objDBCommand'];
    $rs = $objDB->Rs("sp_experiencias_laboral_xCv", array('intFk_id_cv' => $id));
    while (!$rs->EOF()) {
        $e = array();
        $e['id_exp'] = $rs->Field("id_experiencia_laboral")->Value();
        $e['cargo'] = $rs->Field("cargo")->Value();
        $e['compania'] = $rs->Field("compania")->Value();
        $e['cliente'] = $rs->Field("cliente")->Value();
        $e['fecha_desde'] = $rs->Field("fecha_desde")->Value();
        $e['fecha_hasta'] = $rs->Field("fecha_hasta")->Value();
        $e['fk_id_pais'] = $rs->Field("fk_id_pais")->Value();
        $e['pais'] = $rs->Field("pais")->Value();
        $e['actividades'] = $rs->Field("actividades")->Value();
        $e['actualidad'] = $rs->Field("actualidad")->Value();
        $exp[$i] = $e;
        $i++;
        $rs->MoveNext();
    }
    unset($rs);
    return $exp;
}

function getCursosArray($id) {
    $cursos = array();
    $i = 0;
    $objDB = $GLOBALS['objDBCommand'];
    $rs = $objDB->Rs("sp_cursos_xCv", array('intFk_id_cv' => $id));
    while (!$rs->EOF()) {
        $e = array();
        $e['id_curso'] = $rs->Field("id_curso")->Value();
        $e['nombre'] = $rs->Field("nombre")->Value();
        $e['descripcion'] = $rs->Field("descripcion")->Value();
        $cursos[$i] = $e;
        $i++;
        $rs->MoveNext();
    }
    unset($rs);
    return $cursos;
}

function getEstudiosArray($id) {
    $estudios = array();
    $i = 0;
    $objDB = $GLOBALS['objDBCommand'];
    $rs = $objDB->Rs("sp_estudios_xCv", array('intFk_id_cv' => $id));
    while (!$rs->EOF()) {
        $e = array();
        $e['id_estudio'] = $rs->Field("id_estudio")->Value();
        $e['fk_id_nivel_estudio'] = $rs->Field("fk_id_nivel_estudio")->Value();
        $e['titulo'] = $rs->Field("titulo")->Value();
        $e['area'] = $rs->Field("area")->Value();
        $e['institucion'] = $rs->Field("institucion")->Value();
        $e['fecha_desde'] = $rs->Field("fecha_desde")->Value();
        $e['fecha_hasta'] = $rs->Field("fecha_hasta")->Value();
        $e['descripcion'] = $rs->Field("descripcion")->Value();
        $e['actualidad'] = $rs->Field("actualidad")->Value();
        $e['nivel'] = $rs->Field("nivel")->Value();
        $estudios[$i] = $e;
        $i++;
        $rs->MoveNext();
    }
    unset($rs);
    return $estudios;
}

function getCursoFromDB($id) {
    $curso = array();
    $objDB = $GLOBALS['objDBCommand'];
    $rs = $objDB->Rs("sp_cursos", array('id_curso' => $id));
    $curso['id_curso'] = $rs->Field("id_curso")->Value();
    $curso['nombre'] = $rs->Field("nombre")->Value();
    $curso['descripcion'] = $rs->Field("descripcion")->Value();
    unset($rs);
    return $curso;
}

function getRutaFotoFromDB($id) {
    $foto = array();
    $objDB = $GLOBALS['objDBCommand'];
    $rs = $objDB->Rs("sp_ruta_foto", array('id_cv' => $id));
    $foto['ruta_foto'] = $rs->Field("ruta_foto")->Value();
    unset($rs);
    return $foto;
}

function getNombreYApellido($id) {
    $objDB = $GLOBALS['objDBCommand'];
    $rs = $objDB->Rs("sp_nya_cv", array('id_cv' => $id));
    $nya = $rs->Field("nya")->Value();
    unset($rs);
    return $nya;
}

function getIdiomasCV($id_cv) {
    $idiomas = array();
    $i = 0;
    $objDB = $GLOBALS['objDBCommand'];
    $rs = $objDB->Rs("sp_idiomas_xcv", array('id_cv' => $id_cv));
    while (!$rs->EOF()) {
        $idio = array();
        $idio['fk_id_idioma'] = $rs->Field("fk_id_idioma")->Value();
        $idio['idioma'] = $rs->Field("idioma")->Value();
        $idiomas[$i] = $idio;
        $i++;
        $rs->MoveNext();
    }
    unset($rs);
    return $idiomas;
}

function getEstudioFromDB($id) {
    $estudio = array();
    $objDB = $GLOBALS['objDBCommand'];
    $rs = $objDB->Rs("sp_estudio", array('id_estudio' => $id));
    $estudio['id_estudio'] = $rs->Field("id_estudio")->Value();
    $estudio['fk_id_nivel_estudio'] = $rs->Field("fk_id_nivel_estudio")->Value();
    $estudio['titulo'] = $rs->Field("titulo")->Value();
    $estudio['area'] = $rs->Field("area")->Value();
    $estudio['institucion'] = $rs->Field("institucion")->Value();
    $estudio['fecha_desde'] = $rs->Field("fecha_desde")->Value();
    $estudio['fecha_hasta'] = $rs->Field("fecha_hasta")->Value();
    $estudio['descripcion'] = $rs->Field("descripcion")->Value();
    $estudio['actualidad'] = $rs->Field("actualidad")->Value();
    unset($rs);
    return $estudio;
}

function getExperienciaLaboral($id) {
    $exp = array();
    $objDB = $GLOBALS['objDBCommand'];
    $rs = $objDB->Rs("sp_experiencia_laboral", array('id_experiencia_laboral' => $id));
    $exp['id_exp'] = $rs->Field("id_experiencia_laboral")->Value();
    $exp['cargo'] = $rs->Field("cargo")->Value();
    $exp['compania'] = $rs->Field("compania")->Value();
    $exp['cliente'] = $rs->Field("cliente")->Value();
    $exp['fecha_desde'] = $rs->Field("fecha_desde")->Value();
    $exp['fecha_hasta'] = $rs->Field("fecha_hasta")->Value();
    $exp['fk_id_pais'] = $rs->Field("fk_id_pais")->Value();
    $exp['pais'] = $rs->Field("pais")->Value();
    $exp['actividades'] = $rs->Field("actividades")->Value();
    $exp['contexto_proyecto'] = $rs->Field("contexto_proyecto")->Value();

    unset($rs);
    return $exp;
}

function getIdiomaEstudio($id_idioma, $id_cv) {
    $idioma = array();
    $objDB = $GLOBALS['objDBCommand'];
    $rs = $objDB->Rs("sp_idioma", array('id_idioma' => $id_idioma, 'id_cv' => $id_cv));

    $idioma['fk_id_cv'] = $id_cv;
    $idioma['fk_id_idioma'] = $id_idioma;
    $idioma['idioma'] = $rs->Field("idioma")->Value();
    $idioma['institucion'] = $rs->Field("institucion")->Value();
    $idioma['fk_id_nivel'] = $rs->Field("fk_id_nivel")->Value();

    unset($rs);
    return $idioma;
}

//Devuelve un vector con año, mes y dia a partir
// de un string con formago YYYY-MM-DD
function getFechaVector($strFechaFromDB) {
    $ano = $mes = $dia = "";
    $ano = substr($strFechaFromDB, 0, 4);
    $mes = substr($strFechaFromDB, 5, 2);
    $dia = substr($strFechaFromDB, 8, 2);
    return array($ano, $mes, $dia);
}

function updateExpDataToDB($data, $exp_id) {
    $objDB = $GLOBALS['objDBCommand'];
    $params = array(
        "cargo" => $data['cargo'],
        "cliente" => $data['cliente'],
        "compania" => $data['compania'],
        "contexto_proyecto" => $data['contexto_proyecto'],
        "fk_id_pais" => ($data['fk_id_pais'] == "") ? null : $data['fk_id_pais'],
        "actividades" => $data['actividades'],
        "pais" => $data['pais'],
        "actividades" => $data['actividades'],
        "fecha_desde" => getStringFecha($data['cmbPeriodosDesdeA'], $data['cmbPeriodosDesdeM'], $data['cmbPeriodosDesdeD']),
        "fecha_hasta" => getStringFecha($data['cmbPeriodosHastaA'], $data['cmbPeriodosHastaM'], $data['cmbPeriodosHastaD']),
        "id_experiencia_laboral" => $exp_id);
    return $objDB->Execute("sp_experiencia_update", $params);
}

function updateEstudioDataToDB($data, $estudio_id) {
    $objDB = $GLOBALS['objDBCommand'];
    $params = array(
        "titulo" => $data['titulo'],
        "area" => $data['area'],
        "institucion" => $data['institucion'],
        "descripcion" => $data['descripcion'],
        "actualidad" => $data['actualidad'],
        "fk_id_nivel_estudio" => $data['fk_id_nivel_estudio'],
        "fecha_desde" => getStringFecha($data['fecha_desdeA'], $data['fecha_desdeM'], $data['fecha_desdeD']),
        "fecha_hasta" => getStringFecha($data['fecha_hastaA'], $data['fecha_hastaM'], $data['fecha_hastaD']),
        "id_estudio" => $estudio_id);
    return $objDB->Execute("sp_estudios_update", $params);
}

function updateCursoDataToDB($data, $curso_id) {
    $objDB = $GLOBALS['objDBCommand'];
    $params = array(
        "nombre" => $data['nombre'],
        "descripcion" => $data['descripcion'],
        "id_curso" => $curso_id);
    return $objDB->Execute("sp_cursos_update", $params);
}

function updateRutaFotoToDB($data, $cv_id) {
    $objDB = $GLOBALS['objDBCommand'];
    $params = array(
        "ruta_foto" => $data,
        "id_cv" => $cv_id);
    return $objDB->Execute("sp_ruta_foto_update", $params);
}

function updateNivelIdiomaCv($data) {
    $objDB = $GLOBALS['objDBCommand'];
    $params = array(
        "id_cv" => $data['fk_id_cv'],
        "id_idioma" => $data['fk_id_idioma'],
        "idioma" => $data['idioma'],
        "id_nivel" => $data['fk_id_nivel'],
        "institucion" => $data['institucion'],
        "calificacion" => $data['calificacion']);
    // var_dump($params);
    return $objDB->Execute("sp_niveles_idioma_update", $params);
}

function updateEntrevistaDataToDB($data){
    $objDB = $GLOBALS['objDBCommand'];
    // var_dump($params);
    return $objDB->Execute("sp_entrevista_update", $data);
}

function insertEntrevistaDataToDB($data){
    $objDB = $GLOBALS['objDBCommand'];
    //guardo info de entrevistas
    return $objDB->Execute("sp_entrevistas_a", $data);

}

function insertExperienciaDataToDB($data){
    $objDB = $GLOBALS['objDBCommand'];
    $params = array(
        "fk_id_cv" => $data['fk_id_cv'],
        "cargo" => $data['cargo'],
        "cliente" => $data['cliente'],
        "compania" => $data['compania'],
        "contexto_proyecto" => $data['contexto_proyecto'],
        "fk_id_pais" => ($data['fk_id_pais'] == "") ? null : $data['fk_id_pais'],
        "actividades" => $data['actividades'],
        "pais" => $data['pais'],
        "fecha_desde" => getStringFecha($data['cmbPeriodosDesdeA'], $data['cmbPeriodosDesdeM'], $data['cmbPeriodosDesdeD']),
        "fecha_hasta" => getStringFecha($data['cmbPeriodosHastaA'], $data['cmbPeriodosHastaM'], $data['cmbPeriodosHastaD']),
        );
    //guardo info de entrevistas
    return $objDB->Execute("sp_experiencias_a", $params);
}

function insertEstudioDataToDB($data) {
    $objDB = $GLOBALS['objDBCommand'];
    $params = array(
        "fk_id_cv" => $data['fk_id_cv'],
        "titulo" => $data['titulo'],
        "area" => $data['area'],
        "institucion" => $data['institucion'],
        "descripcion" => $data['descripcion'],
        "actualidad" => $data['actualidad'],
        "fk_id_nivel_estudio" => $data['fk_id_nivel_estudio'],
        "fecha_desde" => getStringFecha($data['fecha_desdeA'], $data['fecha_desdeM'], $data['fecha_desdeD']),
        "fecha_hasta" => getStringFecha($data['fecha_hastaA'], $data['fecha_hastaM'], $data['fecha_hastaD']),
        );
    return $objDB->Execute("sp_estudios_a", $params);
}

function insertCursoDataToDB($data) {
    $objDB = $GLOBALS['objDBCommand'];
    $params = array(
        "fk_id_cv" => $data["fk_id_cv"],
        "nombre" => $data['nombre'],
        "descripcion" => $data['descripcion'],
        );
    return $objDB->Execute("sp_cursos_a", $params);
}


?>
