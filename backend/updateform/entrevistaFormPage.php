<?php

require_once("formPage.php");
require_once("form_functions.php");
require_once("formvalidator.php");

class EntrevistaFormPage extends FormPage {

    private $cv_id;
    private $objTpl;
    private $flash_msg;

    public function __construct($objTpl, $cv_id) {
        $this->objTpl = $objTpl;
        $this->cv_id = $cv_id;
        $this->flash_msg = "";
        $this->setFormTemplate();
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.hdn_id", $this->cv_id);
        $this->objTpl->SetVar("title_2", " Entrevista:");
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.alta_modif", $this->getInternalOperation());
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.id_entrevista", $this->getIdEntrevista());

        //var_dump(getDatosEntrevista(2));
    }

    public function isSubmitted() {
        return isset($_POST['submited']);
    }

    public function getInputValuesArray() {
        $values = array();
        $values['organizacion'] = fncRequest(REQUEST_METHOD_POST, "org", REQUEST_TYPE_STRING, "");
        $values['contacto'] = fncRequest(REQUEST_METHOD_POST, "contacto", REQUEST_TYPE_STRING, "");
        $values['fecha'] = fncRequest(REQUEST_METHOD_POST, "fecha", REQUEST_TYPE_STRING, "");
        $values['comentario'] = fncRequest(REQUEST_METHOD_POST, "comentarioEntrevista", REQUEST_TYPE_STRING, "");
        $values['comentario_tecnico'] = fncRequest(REQUEST_METHOD_POST, "comentarioTecnico", REQUEST_TYPE_STRING, "");
        $values['comentario_idiomas'] = fncRequest(REQUEST_METHOD_POST, "comentarioIdiomas", REQUEST_TYPE_STRING, "");
        $values['comentario_presentacion'] = fncRequest(REQUEST_METHOD_POST, "comentarioPresentacion", REQUEST_TYPE_STRING, "");
        $values['nivel_tecnico'] = fncRequest(REQUEST_METHOD_POST, "optsNivelTecnico", REQUEST_TYPE_STRING, "");
        $values['nivel_idiomas'] = fncRequest(REQUEST_METHOD_POST, "optsNivelIdiomas", REQUEST_TYPE_STRING, "");
        $values['nivel_presentacion'] = fncRequest(REQUEST_METHOD_POST, "optsNivelPresentacion", REQUEST_TYPE_STRING, "");
        $values['aprobado'] = fncRequest(REQUEST_METHOD_POST, "optsAprobo", REQUEST_TYPE_STRING, "");
        $values['observaciones'] = fncRequest(REQUEST_METHOD_POST, "observaciones", REQUEST_TYPE_STRING, "");

        return $values;
    }

    private function getCalificacionesNiveles() {
        return array(1 => "bajo", 2 => "medio", 3 => "alto");
    }

    private function setValuesFromArray($datos) {
        //var_dump($datos);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.org", $datos['organizacion']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.fecha", $datos['fecha']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.contacto", $datos['contacto']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.comentarioEntrevista", $datos['comentario']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.comentarioTecnico", $datos['comentario_tecnico']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.comentarioIdiomas", $datos['comentario_idiomas']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.comentarioPresentacion", $datos['comentario_presentacion']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.optsNivelTecnico",generateChoiceHTML("optsNivelTecnico",$this->getCalificacionesNiveles(),  $datos['nivel_tecnico']));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.optsNivelIdioma", generateChoiceHTML("optsNivelIdiomas",$this->getCalificacionesNiveles(),  $datos['nivel_idiomas']));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.optsNivelPresentacion",generateChoiceHTML("optsNivelPresentacion",$this->getCalificacionesNiveles(),  $datos['nivel_presentacion']));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.optsAprobo",generateChoiceHTML("optsAprobo",array(1 => "Si",0 => "No"),  $datos['aprobado']));
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.observaciones", $datos['observaciones']);

    }

    public function setFormValuesFromInput() {
        $this->setValuesFromArray($this->getInputValuesArray());
    }

    private function getIdEntrevista() {
        if (isset($_POST['hdn_alt_id']))
            return $_POST['hdn_alt_id'];
        else
            return NULL;
    }

    private function setFormValuesFromDB() {

        $entrevista = getDatosEntrevista($this->getIdEntrevista());
        $values['organizacion'] = $entrevista['organizacion'];
        $values['fecha'] = $entrevista['fecha'];
        $values['contacto'] = $entrevista['contacto'];
        $values['comentario'] = $entrevista['comentario'];
        $values['comentario_tecnico'] = $entrevista['comentario_tecnico'];
        $values['nivel_tecnico'] = $entrevista['nivel_tecnico'];
        $values['comentario_idiomas'] = $entrevista['comentario_idiomas'];
        $values['nivel_idiomas'] = $entrevista['nivel_idiomas'];
        $values['comentario_presentacion'] = $entrevista['comentario_presentacion'];
        $values['nivel_presentacion'] = $entrevista['nivel_presentacion'];
        $values['aprobado'] = $entrevista['aprobado'];
        $values['observaciones'] = $entrevista['observaciones'];
        $values['hdn_alt_id'] = $this->getIdEntrevista();
        $this->setValuesFromArray($values);
    }

    public function validate() {
        $valid = TRUE;
        //Setup Validations
        $validator = new FormValidator();
        $validator->addValidation("org", "req", "Ingrese el nombre del cliente o cuenta que realizó la entrevista.");
        $validator->addValidation("fecha", "req", "Debe especificar la fecha de la entrevista.");
        $validator->addValidation("comentarioEntrevista", "req", "Escriba un comentario general de la entrevista.");
        $validator->addValidation("optsNivelTecnico", "req", "Debe calificar el nivel técnico del candidato.");
        $validator->addValidation("optsNivelIdiomas", "req", "Debe calificar el nivel de idiomas del candidato.");
        $validator->addValidation("optsNivelPresentacion", "req", "Debe calificar la presentación del candidato.");
        $validator->addValidation("optsAprobo", "req", "Debe indicar si el candidato aprobo la entrevista o no!");


        //Now, validate the form
        if (!$validator->ValidateForm()) {
            $valid = FALSE;
            $val_message = "";
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

    private function getInternalOperation() {
        if (isset($_POST['hdn_op_int']))
            return $_POST['hdn_op_int'];
        else
            return NULL;
    }

    private function setFormTemplate() {
        $this->objTpl->SetFile("MIDDLE", PATH_TEMPLATES . "cvedit.html");
        $this->objTpl->SetFile("MIDDLE.EDITFORM", PATH_TEMPLATES . "entrevista_form_template.html");
    }

    public function render() {
        $this->objTpl->Parse("MIDDLE.EDITFORM.BLK_FORM");
    }

    public function submit() {

        $datos = $this->getInputValuesArray();

        if ($this->getInternalOperation() == "modificacion") {
            //es modificacion
            $datos = $this->getInputValuesArray();
            $datos['id_entrevista'] = $this->getIdEntrevista();
            updateEntrevistaDataToDB($datos);
            $this->setFlashMessage("Se actualizaron los datos de la entrevista");
        } else {
            // es alta
            $datos = $this->getInputValuesArray();
            $datos['id_cv'] = $this->cv_id;
            insertEntrevistaDataToDB($datos);
            unset($_POST);
            $this->setFormValuesFromInput();
            $this->setFlashMessage("Se agrego la nueva entrevista");

        }

    }

}

?>
