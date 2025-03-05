<?php

require_once("formPage.php");
require_once("form_functions.php");
require_once("formvalidator.php");

class CursoFormPage extends FormPage {

    private $cv_id;
    private $objTpl;
    private $flash_msg;

    public function __construct($objTpl, $cv_id) {
        $this->objTpl = $objTpl;
        $this->cv_id = $cv_id;
        $this->curso_id = $_POST['hdn_alt_id'];
        $this->flash_msg = "";
        $this->setFormTemplate();
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.hdn_id", $this->cv_id);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.hdn_curso_id", $this->curso_id);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.alta_modif", $this->getInternalOperation());

        $this->objTpl->SetVar("title_2", "CV id:  " . $this->cv_id . " editar curso:");
    }

    public function isSubmitted() {
        return isset($_POST['submited']);
    }

    public function getInputValuesArray() {
        $values = array();
        $values['nombre'] = fncRequest(REQUEST_METHOD_POST, "txtNombre", REQUEST_TYPE_STRING, "");
        $values['descripcion'] = fncRequest(REQUEST_METHOD_POST, "taDescripcion", REQUEST_TYPE_STRING, "");
        return $values;
    }

    private function setValuesFromArray($datos) {
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtNombre", $datos['nombre']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.taDescripcion", $datos['descripcion']);
    }

    public function setFormValuesFromInput() {
        $this->setValuesFromArray($this->getInputValuesArray());
    }

    private function setFormValuesFromDB() {
        $values = getCursoFromDB($this->curso_id);
        $this->setValuesFromArray($values);
    }

    public function validate() {
        $valid = TRUE;
        //Setup Validations
        $validator = new FormValidator();
        $validator->addValidation("txtNombre", "req", "Escriba el nombre del curso.");

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
        $this->objTpl->SetFile("MIDDLE.EDITFORM", PATH_TEMPLATES . "cursos_form_template.html");
    }

    public function render() {
        $this->objTpl->Parse("MIDDLE.EDITFORM.BLK_FORM");
    }

    public function submit() {
        $data = $this->getInputValuesArray();
        if ($this->getInternalOperation() == "modificacion") {
            updateCursoDataToDB($data, $this->curso_id);
            $this->setFlashMessage("Se actualizaron los datos del curso");
        } else {
            // es alta
            $data['fk_id_cv'] = $this->cv_id;
            insertCursoDataToDB($data);
            unset($_POST);
            $this->setFormValuesFromInput();
            $this->setFlashMessage("Se agrego el nuevo curso");
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
