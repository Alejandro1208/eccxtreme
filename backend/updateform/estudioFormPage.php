<?php

require_once("formPage.php");
require_once("form_functions.php");
require_once("formvalidator.php");

class EstudioFormPage extends FormPage {

    private $cv_id;
    private $objTpl;
    private $flash_msg;

    public function __construct($objTpl, $cv_id) {
        $this->objTpl = $objTpl;
        $this->cv_id = $cv_id;
        $this->estudio_id = $_POST['hdn_alt_id'];
        $this->flash_msg = "";
        $this->setFormTemplate();
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.hdn_id", $this->cv_id);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.alta_modif", $this->getInternalOperation());
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.hdn_est_id", $this->estudio_id);
        $this->objTpl->SetVar("title_2", "CV id:  " . $this->cv_id . " editar estudio:");
    }

    public function isSubmitted() {
        return isset($_POST['submited']);
    }

    public function getInputValuesArray() {
        $values = array();
        $values['titulo'] = fncRequest(REQUEST_METHOD_POST, "txtTitulo", REQUEST_TYPE_STRING, "");
        $values['area'] = fncRequest(REQUEST_METHOD_POST, "txtArea", REQUEST_TYPE_STRING, "");
        $values['institucion'] = fncRequest(REQUEST_METHOD_POST, "txtInstitucion", REQUEST_TYPE_STRING, "");
        $values['fk_id_nivel_estudio'] = fncRequest(REQUEST_METHOD_POST, "cmbNiveles", REQUEST_TYPE_STRING, "");
        $values['descripcion'] = fncRequest(REQUEST_METHOD_POST, "taEstudiosDescripcion", REQUEST_TYPE_STRING, "");
        $values['fecha_desdeA'] = fncRequest(REQUEST_METHOD_POST, "cmbFechaDesdeA", REQUEST_TYPE_STRING, "");
        $values['fecha_desdeM'] = fncRequest(REQUEST_METHOD_POST, "cmbFechaDesdeM", REQUEST_TYPE_STRING, "");
        $values['fecha_desdeD'] = fncRequest(REQUEST_METHOD_POST, "cmbFechaDesdeD", REQUEST_TYPE_STRING, "");
        $values['fecha_hastaA'] = fncRequest(REQUEST_METHOD_POST, "cmbFechaHastaA", REQUEST_TYPE_STRING, "");
        $values['fecha_hastaM'] = fncRequest(REQUEST_METHOD_POST, "cmbFechaHastaM", REQUEST_TYPE_STRING, "");
        $values['fecha_hastaD'] = fncRequest(REQUEST_METHOD_POST, "cmbFechaHastaD", REQUEST_TYPE_STRING, "");
        $values['actualidad'] = fncRequest(REQUEST_METHOD_POST, "chkActualidad", REQUEST_TYPE_STRING, "");

        return $values;
    }

    private function setValuesFromArray($datos) {
        //    var_dump($datos);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtTitulo", $datos['titulo']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtArea", $datos['area']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtInstitucion", $datos['institucion']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbNiveles", generateOptions(getCmbNivelesEstudioOptions(), $datos['fk_id_nivel_estudio']));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.taEstudiosDescripcion", $datos['descripcion']);

        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.chkActualidad", generateChoiceHTML("chkActualidad", getOptionsYesNo(), $datos['actualidad']));

        //fecha desde
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbFechaDesdeA", generateOptions(getYearsOptions(), $datos['fecha_desdeA'], TRUE));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbFechaDesdeM", generateOptions(getFechasNacimientoM(), $datos['fecha_desdeM']));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbFechaDesdeD", generateOptions(getDaysOptions(), $datos['fecha_desdeD']));

        //fecha hasta
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbFechaHastaA", generateOptions(getYearsOptions(), $datos['fecha_hastaA'], TRUE));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbFechaHastaM", generateOptions(getFechasNacimientoM(), $datos['fecha_hastaM']));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbFechaHastaD", generateOptions(getDaysOptions(), $datos['fecha_hastaD']));
    }

    public function setFormValuesFromInput() {
        $this->setValuesFromArray($this->getInputValuesArray());
    }

    private function setFormValuesFromDB() {
        $values = getEstudioFromDB($this->estudio_id);

        $fecha = getFechaVector($values["fecha_desde"]);
        $values['fecha_desdeA'] = $fecha[0];
        $values['fecha_desdeM'] = $fecha[1];
        $values['fecha_desdeD'] = $fecha[2];

        $fecha = getFechaVector($values["fecha_hasta"]);
        $values['fecha_hastaA'] = $fecha[0];
        $values['fecha_hastaM'] = $fecha[1];
        $values['fecha_hastaD'] = $fecha[2];

        $this->setValuesFromArray($values);
    }

    public function validate() {
        $valid = TRUE;
        //Setup Validations
        $validator = new FormValidator();
        $validator->addValidation("txtTitulo", "req", "Escriba un titulo.");

        //Now, validate the form
        if (!$validator->ValidateForm()) {
            $valid = FALSE;
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
            if ($this->getInternalOperation() == "modificacion") {
                //es modificacion
                $this->setFormValuesFromDB();
            } else {
                // es alta
                $this->setFormValuesFromInput();
            }
        }
    }

    private function setFormTemplate() {
        $this->objTpl->SetFile("MIDDLE", PATH_TEMPLATES . "cvedit.html");
        $this->objTpl->SetFile("MIDDLE.EDITFORM", PATH_TEMPLATES . "estudio_form_template.html");
    }

    public function render() {
        $this->objTpl->Parse("MIDDLE.EDITFORM.BLK_FORM");
    }

    public function submit() {
        $data = $this->getInputValuesArray();

        if ($this->getInternalOperation() == "modificacion") {
            updateEstudioDataToDB($data, $this->estudio_id);
            $this->setFlashMessage("Se actualizaron los datos del estudio");
        } else {
            // es alta
            $data['fk_id_cv'] = $this->cv_id;
            insertEstudioDataToDB($data);
            unset($_POST);
            $this->setFormValuesFromInput();
            $this->setFlashMessage("Se agrego el nuevo estudio");
        }
    }

    private function getInternalOperation() {
        if (isset($_POST['hdn_op_int']))
            return $_POST['hdn_op_int'];
        else
            return NULL;
    }

}

?>
