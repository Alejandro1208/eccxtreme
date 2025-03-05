<?php

require_once("formPage.php");
require_once("form_functions.php");
require_once("formvalidator.php");

class IdiomaFormPage extends FormPage {

    private $cv_id;
    private $objTpl;
    private $flash_msg;

    public function __construct($objTpl, $cv_id) {
        $this->objTpl = $objTpl;
        $this->cv_id = $cv_id;
        $this->idioma_id = $_POST['hdn_alt_id'];
        $this->flash_msg = "";
        $this->setFormTemplate();
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.hdn_id", $this->cv_id);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.hdn_idioma_id", $this->idioma_id);
        $this->objTpl->SetVar("title_2", "CV id:  " . $this->cv_id . " editar idioma:");
    }

    public function isSubmitted() {
        return isset($_POST['submited']);
    }

    private function getIdiomasOptionsFromInput() {
        $data = array();
        foreach ($_POST as $k => $v) {
            if (substr_count($k, "cmbNivel") > 0) {
                $data[substr($k, 8)] = $v;
            }
        }
        return $data;
    }

    public function getInputValuesArray() {
        $values = array();
        $values['institucion'] = fncRequest(REQUEST_METHOD_POST, "txtInstitucion", REQUEST_TYPE_STRING, "");
        $values['idioma'] = fncRequest(REQUEST_METHOD_POST, "txtIdioma", REQUEST_TYPE_STRING, "");
        $values['niveles_idioma'] = $this->getNivelesIdiomaHTML(getCmbNivelesIdiomaOptions(), $this->getIdiomasOptionsFromInput());
        return $values;
    }

    private function setValuesFromArray($datos) {
        //var_dump($datos);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtIdioma", $datos['idioma']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.txtInstitucion", $datos['institucion']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.niveles", $datos['niveles_idioma']);
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.otro", $datos['idioma']);
    }

    public function setFormValuesFromInput() {
        $this->setValuesFromArray($this->getInputValuesArray());
    }

    private function getNivelesIdiomaHTML($niveles, $defaults) {
        $strNivelesCombo = "";
        foreach ($niveles as $idNivel => $nombre) {
            $strNivelesCombo .= "<p>$nombre: ";
            $strNivelesCombo .= generateChoiceHTML("cmbNivel" . $idNivel, array("ava" => "Avanzado", "int" => "Intermedio", "bas" => "B&aacute;sico"), $defaults[$idNivel]);
            $strNivelesCombo .="</p>";
        }
        return $strNivelesCombo;
    }

    private function setFormValuesFromDB() {
        $values = getIdiomaEstudio($this->idioma_id, $this->cv_id);
        $values['niveles_idioma'] = $this->getNivelesIdiomaHTML(getCmbNivelesIdiomaOptions(), getCalifNivelesIdiomas($this->cv_id, $this->idioma_id));
        $idiomas = getCmbIdiomasOptions();
        $idioma = ($idiomas[$this->idioma_id]!="Otro")?$idiomas[$this->idioma_id]:$values['idioma'];
        $values['idioma'] = $idioma;
        $this->setValuesFromArray($values);
    }

    public function validate() {
        $valid = TRUE;
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
        $this->objTpl->SetFile("MIDDLE.EDITFORM", PATH_TEMPLATES . "idioma_form_template.html");
    }

    public function render() {
        $this->objTpl->Parse("MIDDLE.EDITFORM.BLK_FORM");
    }

    public function submit() {
        $datos = $this->getInputValuesArray();
        $institucion = $datos['institucion'];
        $idiomas = $this->getIdiomasOptionsFromInput();

        foreach ($idiomas as $k => $v) {
            $reg = array(
                "fk_id_cv" => $this->cv_id,
                "fk_id_idioma" => $this->idioma_id,
                "fk_id_nivel" => $k,
                "idioma" => $datos['idioma'],
                "institucion" => $institucion,
                "calificacion" => $v
            );
            updateNivelIdiomaCv($reg);
        }
        $this->setFlashMessage("Se actualizaron los datos del idioma");
    }

}

?>
