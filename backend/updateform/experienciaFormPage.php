<?php

require_once("formPage.php");
require_once("form_functions.php");
require_once("formvalidator.php");

class ExperienciaFormPage extends FormPage {

    private $cv_id;
    private $objTpl;
    private $flash_msg;

    public function __construct($objTpl, $cv_id) {
        $this->objTpl = $objTpl;
        $this->cv_id = $cv_id;
        $this->exp_id = $_POST['hdn_alt_id'];
        $this->flash_msg = "";
        $this->setFormTemplate();
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.hdn_id", $this->cv_id);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.alta_modif", $this->getInternalOperation());
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.hdn_exp_id", $this->exp_id);
        $this->objTpl->SetVar("title_2", "CV id:  " . $this->cv_id . " editar experiencia:");
    }

    public function isSubmitted() {
        return isset($_POST['submited']);
    }

    public function getInputValuesArray() {
        $values = array();
        $values['cargo'] = fncRequest(REQUEST_METHOD_POST, "txtCargo", REQUEST_TYPE_STRING, "");
        $values['cliente'] = fncRequest(REQUEST_METHOD_POST, "txtCliente", REQUEST_TYPE_STRING, "");
        $values['compania'] = fncRequest(REQUEST_METHOD_POST, "txtCompania", REQUEST_TYPE_STRING, "");
        $values['contexto_proyecto'] = fncRequest(REQUEST_METHOD_POST, "txtContextoProyecto", REQUEST_TYPE_STRING, "");
        $values['fk_id_pais'] = fncRequest(REQUEST_METHOD_POST, "cmbPaises", REQUEST_TYPE_STRING, "");
        $values['pais'] = fncRequest(REQUEST_METHOD_POST, "txtPais", REQUEST_TYPE_STRING, "");
        $values['actividades'] = fncRequest(REQUEST_METHOD_POST, "taActividades", REQUEST_TYPE_STRING, "");
        $values['cmbPeriodosDesdeA'] = fncRequest(REQUEST_METHOD_POST, "cmbPeriodosDesdeA", REQUEST_TYPE_STRING, "");
        $values['cmbPeriodosDesdeM'] = fncRequest(REQUEST_METHOD_POST, "cmbPeriodosDesdeM", REQUEST_TYPE_STRING, "");
        $values['cmbPeriodosDesdeD'] = fncRequest(REQUEST_METHOD_POST, "cmbPeriodosDesdeD", REQUEST_TYPE_STRING, "");
        $values['cmbPeriodosHastaA'] = fncRequest(REQUEST_METHOD_POST, "cmbPeriodosHastaA", REQUEST_TYPE_STRING, "");
        $values['cmbPeriodosHastaM'] = fncRequest(REQUEST_METHOD_POST, "cmbPeriodosHastaM", REQUEST_TYPE_STRING, "");
        $values['cmbPeriodosHastaD'] = fncRequest(REQUEST_METHOD_POST, "cmbPeriodosHastaD", REQUEST_TYPE_STRING, "");
        return $values;
    }

    private function setValuesFromArray($datos) {
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtCargo", $datos['cargo']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtCliente", $datos['cliente']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtCompania", $datos['compania']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtContextoProyecto", $datos['contexto_proyecto']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbUbicacion", generateOptions(getCmbPaisesOptions(), $datos['fk_id_pais']));

        //fecha desde
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbPeriodosDesdeA", generateOptions(getYearsOptions(), $datos['cmbPeriodosDesdeA'], TRUE));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbPeriodosDesdeM", generateOptions(getFechasNacimientoM(), $datos['cmbPeriodosDesdeM']));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbPeriodosDesdeD", generateOptions(getDaysOptions(), $datos['cmbPeriodosDesdeD']));

        //fecha hasta
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbPeriodosHastaA", generateOptions(getYearsOptions(), $datos['cmbPeriodosHastaA'], TRUE));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbPeriodosHastaM", generateOptions(getFechasNacimientoM(), $datos['cmbPeriodosHastaM']));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.cmbPeriodosHastaD", generateOptions(getDaysOptions(), $datos['cmbPeriodosHastaD']));

        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtPais", $datos['pais']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.taActividades", $datos['actividades']);
    }

    public function setFormValuesFromInput() {
        $this->setValuesFromArray($this->getInputValuesArray());
    }

    private function setFormValuesFromDB() {
        $values = getExperienciaLaboral($this->exp_id);

        $fecha = getFechaVector($values["fecha_desde"]);
        $values['cmbPeriodosDesdeA'] = $fecha[0];
        $values['cmbPeriodosDesdeM'] = $fecha[1];
        $values['cmbPeriodosDesdeD'] = $fecha[2];

        $fecha = getFechaVector($values["fecha_hasta"]);
        $values['cmbPeriodosHastaA'] = $fecha[0];
        $values['cmbPeriodosHastaM'] = $fecha[1];
        $values['cmbPeriodosHastaD'] = $fecha[2];

        $this->setValuesFromArray($values);
    }

    public function validate() {
        $valid = TRUE;
        //Setup Validations
        $validator = new FormValidator();
        $validator->addValidation("txtCargo", "req", "Escriba un cargo.");

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
        $this->objTpl->SetFile("MIDDLE.EDITFORM", PATH_TEMPLATES . "exp_form_template.html");
    }

    public function render() {
        $this->objTpl->Parse("MIDDLE.EDITFORM.BLK_FORM");
    }

    public function submit() {
        $datos = $this->getInputValuesArray();
        if ($this->getInternalOperation()=="modificacion"){
            updateExpDataToDB($datos, $this->exp_id);
            $this->setFlashMessage("Se actualizaron los datos de la experiencia");
        } else {
            // es alta
            $datos['fk_id_cv'] = $this->cv_id;
            insertExperienciaDataToDB($datos);
            unset($_POST);
            $this->setFormValuesFromInput();
            $this->setFlashMessage("Se agrego la nueva experiencia");
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
