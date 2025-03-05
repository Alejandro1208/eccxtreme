<?php

$cv_id = getCvId();

if ($cv_id != "") {

    //Includes necesarios
    require_once("includes/begin-page-interna.inc.php");

    //obtengo la operacion requerida
    $op = getOperacion();

    //obtengo la pagina que maneja dicha operacion
    $page = dispatchOperation($op, $objTpl, $cv_id);

    //proceso e imprimo la pagina
    $page->process();
    $page->render();

    require_once("includes/end-page-interna.inc.php");
} else {

    require_once("includes/begin-page-l.inc.php");
# ---------------------------------------------
# Template.
# ---------------------------------------------
    $objTpl->SetVar("action_f", "cvs-l.php");
# ---------------------------------------------
# ---------------------------------------------
# Listado.
# ---------------------------------------------

//    $strSql = "select nument from (select id_cv, count(id_cv) nument from entrevistas group by id_cv) ent right join cvs on cvs.id_cv=ent.id_cv";// ORDER BY cvs.fecha DESC";
    $strSql = "";
    $strSql .= "SELECT ";
    $strSql .= "cvs.id_cv, ";
    $strSql .= "nya, ";
    $strSql .= "fecha, ";
    $strSql .= "perfil, ";
    $strSql .= "remuneracion, ";
    $strSql .= "nument ";
    $strSql .= "FROM ";
    $strSql .= "cvs ";
    $strSql .= "LEFT JOIN (select id_cv, count(id_cv) nument from entrevistas group by id_cv) cant_ent  ON cvs.id_cv = cant_ent.id_cv ";
    $strSql .= "ORDER BY ";
    $strSql .= "fecha DESC";
//    $strSql .= " ; SELECT cvs.id_cv FROM (SELECT id_cv, count(id_cv) FROM entrevistas GROUP BY id_cv) ent RIGHT JOIN cvs ON cvs.id_cv=ent.id_cv ORDER BY cvs.fecha DESC";
    subList(
            $objTpl,
            $strSql,
            "Nombre y apellido,Fecha,Módulo,Rem.,Ent.",
            "45,25,15,15,5",
            "b,m,e,i",
            "",
            "nya|Nombre y apellido,remuneracion|Remuneración,perfil|Modulo,nument|Entrevistas",
            "Perfil|sp_perfiles_combo|fk_id_perfil,Modalidad contrato|sp_tipos_contratacion_combo|modalidad",
            "",
            false,
            ""
    );
# ---------------------------------------------
    require_once("includes/end-page-interna.inc.php");
}

function getOperacion() {
    $operacion = "";
    if (isset($_POST['hdn_op']))
        $operacion = $_POST['hdn_op'];
    return $operacion;
}

function dispatchOperation($op, $objTpl, $cv_id) {
    if ($op == "m") {
        require_once("updateform/cvFormUpdatePage.php");
        return new UpdateCVFormPage($objTpl, $cv_id);
    } elseif ($op == "me") {
        require_once("updateform/experienciaFormPage.php");
        return new ExperienciaFormPage($objTpl, $cv_id);
    } elseif ($op == "mc") {
        require_once("updateform/cursoFormPage.php");
        return new CursoFormPage($objTpl, $cv_id);
    } elseif ($op == "mest") {
        require_once("updateform/estudioFormPage.php");
        return new EstudioFormPage($objTpl, $cv_id);
    } elseif ($op == "mi") {
        require_once("updateform/idiomaFormPage.php");
        return new IdiomaFormPage($objTpl, $cv_id);
    } elseif ($op == "mimg") {
        require_once("updateform/fotoFormPage.php");
        return new FotoFormPage($objTpl, $cv_id);
    } elseif ($op == "ent") {
        require_once("updateform/entrevistasListPage.php");
        return new EntrevistasListPage($objTpl, $cv_id);
    } elseif ($op == "nent") {
        require_once("updateform/entrevistaFormPage.php");
        return new EntrevistaFormPage($objTpl, $cv_id);
    } else
        return NULL;
}

function getCvId() {
    $cv_id = "";
    if (isset($_POST['hdn_id'])) {
        $cv_id = $_POST['hdn_id'];
        if (preg_match("/^[[:digit:]]+$/", $cv_id))
            $cv_id = $_POST['hdn_id'];
        else
            $cv_id = "";
    }
    return $cv_id;
}

?>