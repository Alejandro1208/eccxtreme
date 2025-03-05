<?php

require_once("formPage.php");
require_once("form_functions.php");
require_once("formvalidator.php");
require_once("./../common/includes/constants.inc.php");

class UpdateCVFormPage extends FormPage {

    private $cv_id;
    private $objTpl;
    private $flash_msg;

    public function __construct($objTpl, $cv_id) {
        $this->objTpl = $objTpl;
        $this->cv_id = $cv_id;
        $this->flash_msg = "";
        $this->setFormTemplate();
//        $this->objTpl->SetVar("title_2", "CV nro  " . $this->cv_id);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.hdn_op", "m");
        $this->paises = getCmbPaisesOptions();
        $this->idiomas = getCmbIdiomasOptions();
    }

    public function isSubmitted() {
        return isset($_POST['submited']);
    }

    public function getInputValuesArray() {
        $values = array();
        $values['cv_id'] = fncRequest(REQUEST_METHOD_POST, "hdn_id", 'REQUEST_TYPE_STRING', "");
        $values['txtNyA'] = fncRequest(REQUEST_METHOD_POST, "txtNyA", 'REQUEST_TYPE_STRING', "");
        $values['cmbFechasNacimientoA'] = fncRequest(REQUEST_METHOD_POST, "cmbFechasNacimientoA", 'REQUEST_TYPE_STRING', "");
        $values['cmbFechasNacimientoM'] = fncRequest(REQUEST_METHOD_POST, "cmbFechasNacimientoM", 'REQUEST_TYPE_STRING', "");
        $values['cmbFechasNacimientoD'] = fncRequest(REQUEST_METHOD_POST, "cmbFechasNacimientoD", 'REQUEST_TYPE_STRING', "");
        $values['rdoEstadoCivil'] = fncRequest(REQUEST_METHOD_POST, "rdoEstadoCivil", 'REQUEST_TYPE_INT', null);
        $values['cmbHijos'] = fncRequest(REQUEST_METHOD_POST, "cmbHijos", 'REQUEST_TYPE_INT', null);
        $values['cmbTiposDocumento'] = fncRequest(REQUEST_METHOD_POST, "cmbTiposDocumento", 'REQUEST_TYPE_INT', null);
        $values['txtDocumento'] = fncRequest(REQUEST_METHOD_POST, "txtDocumento", 'REQUEST_TYPE_STRING', null);
        $values['txtCuil'] = fncRequest(REQUEST_METHOD_POST, "txtCuil", 'REQUEST_TYPE_STRING', null);
        $values['txtTelefono'] = fncRequest(REQUEST_METHOD_POST, "txtTelefono", 'REQUEST_TYPE_STRING', null);
        $values['txtCalle'] = fncRequest(REQUEST_METHOD_POST, "txtCalle", 'REQUEST_TYPE_STRING', "");
        $values['txtNro'] = fncRequest(REQUEST_METHOD_POST, "txtNro", 'REQUEST_TYPE_INT', null);
        $values['txtDepto'] = fncRequest(REQUEST_METHOD_POST, "txtDepto", 'REQUEST_TYPE_STRING', null);
        $values['txtPiso'] = fncRequest(REQUEST_METHOD_POST, "txtPiso", 'REQUEST_TYPE_STRING', "");
        $values['txtCp'] = fncRequest(REQUEST_METHOD_POST, "txtCp", 'REQUEST_TYPE_STRING', "");
        $values['txtBarrio'] = fncRequest(REQUEST_METHOD_POST, "txtBarrio", 'REQUEST_TYPE_STRING', "");
        $values['cmbProvincias'] = fncRequest(REQUEST_METHOD_POST, "cmbProvincias", 'REQUEST_TYPE_INT', null);
        $values['txtEmail'] = fncRequest(REQUEST_METHOD_POST, "txtEmail", 'REQUEST_TYPE_STRING', "");
        $values['cmbPerfiles'] = fncRequest(REQUEST_METHOD_POST, "cmbPerfiles", 'REQUEST_TYPE_INT', null);
        $values['chkPerfiles'] = $_POST['chkPerfiles'];
        $values['txtPerfilSapOtro'] = fncRequest(REQUEST_METHOD_POST, "txtPerfilSapOtro", 'REQUEST_TYPE_STRING', "");
        $values['chkConocimientosSap'] = fncRequest(REQUEST_METHOD_POST, "chkConocimientosSap", 'REQUEST_TYPE_BOOLEAN', false);
        $values['rdoCertificadoSap'] = fncRequest(REQUEST_METHOD_POST, "rdoCertificadoSap", 'REQUEST_TYPE_BOOLEAN', false);
        $values['taConocimientos'] = fncRequest(REQUEST_METHOD_POST, "taConocimientos", 'REQUEST_TYPE_STRING', "");
        $values['txtRemuneracion'] = fncRequest(REQUEST_METHOD_POST, "txtRemuneracion", 'REQUEST_TYPE_STRING', "");
      //  $values['txtModalidad'] = fncRequest(REQUEST_METHOD_POST, "txtModalidad", 'REQUEST_TYPE_STRING', "");
        $values['cmbTipoContratacion'] = fncRequest(REQUEST_METHOD_POST, "cmbTipoContratacion", 'REQUEST_TYPE_INT', "");
        return $values;
    }

    private function getRutaFoto() {
        $foto = getRutaFotoFromDB($this->cv_id);
        if ($foto['ruta_foto'] == "") {
            $foto['ruta_foto'] = "sin_foto.jpg";
        }
        return $foto['ruta_foto'];
    }

    public function setFormValuesFromInput() {
        $data = $this->getInputValuesArray();
        $this->objTpl->SetVar("title_2", "CV " . $data['txtNyA']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.hdn_id", $data['cv_id']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtNyA", $data['txtNyA']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtDocumento", $data['txtDocumento']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtCuil", $data['txtCuil']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtTelefono", $data['txtTelefono']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtCalle", $data['txtCalle']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtNro", $data['txtNro']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtPiso", $data['txtPiso']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtDepto", $data['txtDepto']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtCp", $data['txtCp']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtBarrio", $data['txtBarrio']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtEmail", $data['txtEmail']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbFechasNacimientoA", generateOptions(getYearsOptions(), $data['cmbFechasNacimientoA'], TRUE));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbFechasNacimientoM", generateOptions(getFechasNacimientoM(), $data['cmbFechasNacimientoM']));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbFechasNacimientoD", generateOptions(getDaysOptions(), $data['cmbFechasNacimientoD']));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbHijos", generateOptions(getHijosOptions(), $data['cmbHijos']));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbTiposDocumento", generateOptions(getCmbTiposDocumentosOptions(), $data['cmbTiposDocumento']));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbProvincias", generateOptions(getCmbProvinciasOptions(), $data['cmbProvincias']));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbPerfiles", generateOptions(getCmbPerfiles(), $data['cmbPerfiles']));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.rdoEstadoCivil", generateChoiceHTML("rdoEstadoCivil", getRdoEstadoCivil(), $data['rdoEstadoCivil']));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.chkConocimientosSap", generateChoiceHTML("chkConocimientosSap", getOptionsYesNo(), $data['chkConocimientosSap']));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.chkPerfiles", generateCheckHTML("chkPerfiles[]", getOptionsSAPModules(), $data['chkPerfiles']));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.rdoCertificadoSap", generateChoiceHTML("rdoCertificadoSap", getOptionsYesNo(), $data['rdoCertificadoSap']));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtPerfilSapOtro", $data['txtPerfilSapOtro']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.taConocimientos", $data['taConocimientos']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtRemuneracion", $data['txtRemuneracion']);
        //$this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtModalidad", $data['txtModalidad']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbTiposContratacion", generateOptions(getCmbTiposContratacionOptions(), $data['cmbTipoContratacion']));
        $this->setExperienceOutput();
        $this->setCursosOutput();
        $this->setEstudiosOutput();
        $this->setIdiomasOutput();
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.rutaFoto", $this->getRutaFoto($this->cv_id));
    }

    private function setFormValuesFromDB() {
        $rs = getCVDataFromDB($this->cv_id);
        $this->objTpl->SetVar("title_2", "CV " . $rs->Field("nya")->Value());
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.hdn_id", $this->cv_id);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtNyA", $rs->Field("nya")->Value());
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtDocumento", $rs->Field("numero_documento")->Value());
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtTelefono", $rs->Field("telefono")->Value());
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtCalle", $rs->Field("calle")->Value());
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtNro", $rs->Field("numero")->Value());
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtPiso", $rs->Field("piso")->Value());
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtDepto", $rs->Field("depto")->Value());
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtCp", $rs->Field("cp")->Value());
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtBarrio", $rs->Field("barrio")->Value());
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtEmail", $rs->Field("email")->Value());

//        $ruta_foto = $rs->Field("ruta_foto")->Value();
//        if ($ruta_foto == "")
//            $ruta_foto = "sin_foto.jpg";
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.rutaFoto", $this->getRutaFoto($this->cv_id));

        $anio = $mes = $dia = "";
        $fecha = $rs->Field("fecha_nac")->Value();
        $anio = substr($fecha, 0, 4);
        $mes = substr($fecha, 5, 2);
        $dia = substr($fecha, 8, 2);

        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbFechasNacimientoA", generateOptions(getYearsOptions(), $anio, TRUE));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbFechasNacimientoM", generateOptions(getFechasNacimientoM(), $mes));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbFechasNacimientoD", generateOptions(getDaysOptions(), $dia));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbHijos", generateOptions(getHijosOptions(), $rs->Field("hijos")->Value()));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtCuil", $rs->Field("cuil")->Value());
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbTiposDocumento", generateOptions(getCmbTiposDocumentosOptions(), $rs->Field("fk_id_tipo_documento")->Value()));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbProvincias", generateOptions(getCmbProvinciasOptions(), $rs->Field("fk_id_provincia")->Value()));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbPerfiles", generateOptions(getCmbPerfiles(), $rs->Field("fk_id_perfil")->Value()));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.rdoEstadoCivil", generateChoiceHTML("rdoEstadoCivil", getRdoEstadoCivil(), $rs->Field("fk_id_estado_civil")->Value()));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.chkConocimientosSap", generateChoiceHTML("chkConocimientosSap", getOptionsYesNo(), $rs->Field("tiene_conocimientos_sap")->Value()));
        $arrayPerfiles = explode(",", $rs->Field("perfil")->Value());
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.chkPerfiles", generateCheckHTML("chkPerfiles[]", getOptionsSAPModules(), $arrayPerfiles));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.rdoCertificadoSap", generateChoiceHTML("rdoCertificadoSap", getOptionsYesNo(), $rs->Field("tiene_certificado_sap")->Value()));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtPerfilSapOtro", $rs->Field("perfil_otro")->Value());
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.taConocimientos", $rs->Field("conocimientos")->Value());
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtRemuneracion", $rs->Field("remuneracion")->Value());
      //  $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtModalidad", $rs->Field("modalidad")->Value());
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbTiposContratacion", generateOptions(getCmbTiposContratacionOptions(), $rs->Field("modalidad")->Value()));
        $this->setExperienceOutput();
        $this->setCursosOutput();
        $this->setEstudiosOutput();
        $this->setIdiomasOutput();
    }

    public function validate() {
        $valid = TRUE;
        //Setup Validations
        $validator = new FormValidator();
        $validator->addValidation("txtNyA", "req", "Escriba un nombre y Apellido.");
        $validator->addValidation("txtEmail", "email", "El email tiene un formato incorrecto.");
        $validator->addValidation("txtEmail", "req", "Por favor, ingrese un email.");
        $validator->addValidation("cmbPerfiles", "req", "Por favor, especifique un perfil.");
        $validator->addValidation("cmbTipoContratacion", "req", "Por favor, especifique una modalidad de contrato.");
        $validator->addValidation("rdoEstadoCivil", "req", "Por favor, especifique el estado civil.");
        $validator->addValidation("taConocimientos", "req", "Por favor, Indique los conocimientos.");
        $validator->addValidation("txtDocumento", "numeric", "El documento solo puede contener digitos.");
        $validator->addValidation("txtCuil", "numeric", "El cuil solo puede contener digitos");
        $validator->addValidation("txtNro", "numeric", "El n&uacute;mero de calle solo puede contener digitos");
        $validator->addValidation("txtRemuneracion", "numeric", "La remuneraci&oacute;n solo puede contener digitos");


        //Now, validate the form
        if (!$validator->ValidateForm()) {
            $valid = FALSE;
            $val_message="";
            $error_hash = $validator->GetErrors();
            foreach ($error_hash as $inpname => $inp_err) {
                $val_message = $val_message . '<p class="form_error" style="color:red;">' . $inp_err . '</p>';
            }
            $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.err_string", $val_message);
        }

        return $valid;
    }

    public function setFlashMessage($str_msg) {
        $this->flash_msg = $str_msg;
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.flash", '<p style="color:green;">' . $str_msg . '</p>');
    }

    public function process() {
        if ($this->isSubmitted()) {
            $this->setFormValuesFromInput();
            if ($this->validate())
                $this->submit();
        } else {
            $this->setFormValuesFromDB();
        }
    }

    private function setFormTemplate() {
        $this->objTpl->SetFile("MIDDLE", PATH_TEMPLATES . "cvedit.html");
        $this->objTpl->SetFile("MIDDLE.EDITFORM", PATH_TEMPLATES . "cvupdateform.html");
    }

    public function render() {
        $this->objTpl->Parse("MIDDLE.EDITFORM.BLK_FORM");
        $this->objTpl->Parse("MIDDLE.EDITFORM.BLK_EXP");
        $this->objTpl->Parse("MIDDLE.EDITFORM.BLK_CURSOS");
        $this->objTpl->Parse("MIDDLE.EDITFORM.BLK_ESTUDIOS");
        $this->objTpl->Parse("MIDDLE.EDITFORM.BLK_IDIOMAS");
    }

    public function submit() {
        updateCVDataToDB($this->getInputValuesArray(), $this->cv_id);
        $this->setFlashMessage("Se actualizo el CV");
    }

    private function setExperienceOutput() {
        $exp = getExperienceArray($this->cv_id);
        $output = "<ul>";
        foreach ($exp as $e) {
            $cargo = ($e['cargo'] != "") ? $e['cargo'] . " en " : "";
            $cliente = $e['cliente'];
            $ubicacion = ($e['fk_id_pais'] != "") ? $this->paises[$e['fk_id_pais']] : "";
            if ($ubicacion == "Otro")
                $ubicacion = $e['pais'];
            $periodo = formatearFecha($e['fecha_desde'], $e['fecha_hasta']);
            $actividades = $e['actividades'];

            $output.="<li>";
            $output.= $cargo . $e['compania'];
            $output.= "<p>$cliente  $ubicacion</p>";
            $output.= "<p>" . $periodo . "</p>";
            $output.= "<p>" . nl2br($actividades) . "</p>";
            $output.= " " . $this->getActionLinks("subME", array($e['id_exp']));
            $output.="<p></p></li>";
        }
        $output.="</ul>";
        $output.= '<a href="javascript:subME()" style="font-size: 12px; font-weight:bold;"> Agregar experiencia</a><br/>';
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_EXP.experiencias", $output);
    }

    private function setCursosOutput() {
        $cursos = getCursosArray($this->cv_id);
        $output = "<ul>";
        foreach ($cursos as $e) {
            $output.="<li>";
            $output.= $e['nombre'] . ":<br/> " . nl2br($e['descripcion']);
            $output.= "<p>" . $this->getActionLinks("subMC", array($e['id_curso'])) . "</p>";
            $output.="<p></p></li>";
        }
        $output.="</ul>";
        $output.= '<a href="javascript:subMC()" style="font-size: 12px; font-weight:bold;"> Agregar curso</a><br/>';
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_CURSOS.cursos", $output);
    }

    private function setEstudiosOutput() {
        $estudios = getEstudiosArray($this->cv_id);
        $output = "<ul>";
        foreach ($estudios as $e) {
            $area = $e['area'];
            $institucion = $e['institucion'];
            $descripcion = $e['descripcion'];
            $periodo = formatearFecha($e['fecha_desde'], $e['fecha_hasta']);
            $output.="<li>";
            $output.= $e['titulo'] . ", " . $e['institucion'];
            $output.= "<p>" . $periodo . "</p>";
            $output.= ( $descripcion != "") ? "<p>" . nl2br($descripcion) . "</p>" : "";
            $output.= "<p>" . $this->getActionLinks("subMEst", array($e['id_estudio'])) . "</p>";
            $output.="</li>";
        }
        $output.= "</ul>";
        $output.= '<a href="javascript:subMEst()" style="font-size: 12px; font-weight:bold;"> Agregar estudio</a><br/>';
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_ESTUDIOS.estudios", $output);
    }

    private function setIdiomasOutput() {
        $nivelesIdiomas = getCmbNivelesIdiomaOptions();
        $idiomas = getIdiomasCV($this->cv_id);
        $descNiveles = array("ava" => "Avanzado", "int" => "Intermedio", "bas" => "B&aacute;sico");
        //     var_dump($idiomas);
        $output = "<ul>";
        foreach ($idiomas as $e) {

            $idioma = $this->idiomas[$e['fk_id_idioma']];
            if ($idioma == "Otro")
                $idioma = $e['idioma'];

            $calificaciones = getCalifNivelesIdiomas($this->cv_id, $e['fk_id_idioma']);

            $output.="<li>";
            $output.= "<p><strong>$idioma</strong></p>";
            foreach ($calificaciones as $id_niv => $calif) {
                $output.= "<p>" . $nivelesIdiomas[$id_niv] . ": " . $descNiveles[$calif] . " </p>";
            }
            $output.= "<p>" . $this->getActionLinks("subMI", array($e['fk_id_idioma'])) . "</p>";
            $output.="</li>";
        }
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_IDIOMAS.idiomas", $output);
    }

    private function getActionLinks($subName, $params) {
        $link = '<a href="javascript:' . $subName . '(' . implode(',', $params) . ')">Editar</a>';
        //$link.= '<a href="#">Borrar</a>';
        return $link;
    }

}

function valueOrEmptyStr($var) {
    return ($var != "") ? $var : "";
}

function formatearFecha($fecha_desde, $fecha_hasta) {
    $periodo = "";
    if ($fecha_hasta == "") {
        if ($fecha_desde != "")
            $periodo = format_date_mysql_to_local($fecha_desde,".") . " - actualidad";
    } else {
        if ($fecha_desde != "")
            $periodo = "(" . format_date_mysql_to_local($fecha_desde,".") . " a " . format_date_mysql_to_local($fecha_hasta,".") . ")";
    }
    return $periodo;
}

?>
