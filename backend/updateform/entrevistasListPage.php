<?php

require_once('formPage.php');
require_once("form_functions.php");

class EntrevistasListPage extends FormPage {

    private $cv_id;
    private $objTpl;

    function validate() {

    }

    function setFormValuesFromInput() {

    }

    function getInputValuesArray() {

    }

    function __construct($objTpl, $cv_id) {
        $this->objTpl = $objTpl;
        $this->cv_id = $cv_id;
        $this->setFormTemplate();
        $nya = getNombreYApellido($cv_id);
        $this->objTpl->SetVar("title_2", "Entrevistas a " . $nya);


//        var_dump(getDatosEntrevistas(173));
//        var_dump(getDatosEntrevistas(173));
//        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.hdn_idioma_id", $this->idioma_id);
    }

    private function setFormTemplate() {
        $this->objTpl->SetFile("MIDDLE", PATH_TEMPLATES . "cvedit.html");
        $this->objTpl->SetFile("MIDDLE.EDITFORM", PATH_TEMPLATES . "entrevistas_list_template.html");
    }

    function process() {
        $operation = $this->getInternalOperation();
        switch ($operation){
            case "b":
                deleteEntrevista($this->getInternalAltId());
                $this->setFlashMessage("Se elimino la entrevista.");
                break;
                default:
                break;
        }
    }

    private function setFlashMessage($str_msg) {
        $this->flash_msg = $str_msg;
        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.flash", '<p style="color:green;">' . $str_msg . '</p>');
    }

    function render() {
        $calificacion = array(1=>"bajo",2=>"mediano",3=>"alto");
        $entrevistas = getDatosEntrevistas($this->cv_id);
        
        if (count($entrevistas) > 0) {
            foreach ($entrevistas as $e) {//echo $e['fecha']; exit();
                $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_ENTREVISTA.org", $e['organizacion']);
                $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_ENTREVISTA.fecha", format_date_mysql_to_local($e['fecha'],"."));
                $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_ENTREVISTA.contacto", $e['contacto']);
                $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_ENTREVISTA.comentario_tecnico", nl2br($e['comentario_tecnico']));
                $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_ENTREVISTA.nivel_tecnico", $calificacion[$e['nivel_tecnico']]);
                $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_ENTREVISTA.comentario_idiomas", nl2br($e['comentario_idiomas']));
                $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_ENTREVISTA.nivel_idiomas", $calificacion[$e['nivel_idiomas']]);
                $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_ENTREVISTA.comentario_presentacion", nl2br($e['comentario_presentacion']));
                $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_ENTREVISTA.nivel_presentacion", $calificacion[$e['nivel_presentacion']]);
                $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_ENTREVISTA.comentarios", nl2br($e['comentario']));
                $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_ENTREVISTA.aprobado", $e['aprobado']?"Si":"No");
                $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_ENTREVISTA.observaciones", nl2br($e['observaciones']));
                $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_ENTREVISTA.id_ent", $e['id_entrevista']);

                $this->objTpl->Parse("MIDDLE.EDITFORM.BLK_ENTREVISTA");
            }
        }

        $this->objTpl->SetVar("MIDDLE.EDITFORM.BLK_FORM.hdn_id", $this->cv_id);
        $this->objTpl->Parse("MIDDLE.EDITFORM.BLK_FORM");
    }

    function isSubmitted() {

    }

    function submit() {

    }

    private function getInternalAltId(){
        if (isset($_POST['hdn_alt_id']))
            return $_POST['hdn_alt_id'];
        else
            return NULL;
        
    }

    private function getInternalOperation() {
        if (isset($_POST['hdn_op_int']))
            return $_POST['hdn_op_int'];
        else
            return NULL;
    }

}

?>
