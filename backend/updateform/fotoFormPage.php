<?php

require_once("formPage.php");
require_once("form_functions.php");
require_once("../fileUpload.php");
define('RUTA_IMAGENES', "../upload/");

class FotoFormPage extends FormPage {

    private $cv_id;
    private $objTpl;
    private $flash_msg;
    private $ruta_foto;

    public function __construct($objTpl, $cv_id) {
        $this->objTpl = $objTpl;
        $this->cv_id = $cv_id;
        $this->curso_id = $_POST['hdn_alt_id'];
        $this->flash_msg = "";
        $this->setFormTemplate();
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.hdn_id", $this->cv_id);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.rutaFoto", RUTA_IMAGENES . $this->getRutaFoto());
        $this->objTpl->SetVar("title_2", "CV id:  " . $this->cv_id . " cambiar foto:");
    }

    public function isSubmitted() {
        return isset($_POST['submited']);
    }

    public function getInputValuesArray() {
        $values = array();
//        $values['nombre'] = fncRequest(REQUEST_METHOD_POST, "txtNombre", REQUEST_TYPE_STRING, "");
//        $values['descripcion'] = fncRequest(REQUEST_METHOD_POST, "taDescripcion", REQUEST_TYPE_STRING, "");
        return $values;
    }

    private function getRutaFoto() {
        $foto = getRutaFotoFromDB($this->cv_id);
        if ($foto['ruta_foto'] == "") {
            $foto['ruta_foto'] = "sin_foto.jpg";
        }
        return $foto['ruta_foto'];
    }

    private function setValuesFromArray($datos) {
//        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtNombre", $datos['nombre']);
    }

    public function setFormValuesFromInput() {

    }

    private function setFormValuesFromDB() {
//        $values = getCursoFromDB($this->curso_id);
//        $this->setValuesFromArray($values);
    }

    private function generateErrorString($error_array) {
        $error_str = "";
        foreach ($error_array as $e) {
            $error_str .= '<p style="color:red;">' . $e . '</p>';
        }
        return $error_str;
    }

    public function validate() {

        if ($_FILES['archivo']['error'] != 4) {
            $res = guardar_imagen("archivo", RUTA_IMAGENES);
            //var_dump($res);
            if ($res[0] == TRUE) {
                $old_file = $this->getRutaFoto();
                if($old_file!="sin_foto.jpg")
                    unlink(RUTA_IMAGENES . $old_file);
                $this->ruta_foto = $res[1];
                return TRUE;
            } else {
                $errores = array_slice($res, 1);
                $error_str = "";
                $this->setFlashMessage($this->generateErrorString($errores));
                return FALSE;
            }
        }

        $this->setFlashMessage('<p style="color:red;">' . "Debe seleccionar una foto." . '</p>');
        return FALSE;
    }

    public function setFlashMessage($str_msg) {
        $this->flash_msg = $str_msg;
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.flash", '<p style="color:green;">' . $str_msg . '</p>');
    }

    public function process() {
        if ($this->isSubmitted()) {
            $this->setFormValuesFromInput();
            if ($this->validate()) {
                $this->submit();
                $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.rutaFoto", RUTA_IMAGENES . $this->getRutaFoto());
            }
        } else {
            $this->setFormValuesFromDB();
        }
    }

    private function setFormTemplate() {
        $this->objTpl->SetFile("MIDDLE", PATH_TEMPLATES . "cvedit.html");
        $this->objTpl->SetFile("MIDDLE.EDITFORM", PATH_TEMPLATES . "foto_form_template.html");
    }

    public function render() {
        $this->objTpl->Parse("MIDDLE.EDITFORM.BLK_FORM");
    }

    public function submit() {
        updateRutaFotoToDB($this->ruta_foto, $this->cv_id);
        $this->setFlashMessage("Se actualizó la foto.");
    }

}

?>
