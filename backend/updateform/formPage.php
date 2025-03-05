<?php

abstract class FormPage {

    abstract function validate();

    abstract function setFormValuesFromInput();

    abstract function getInputValuesArray();

    abstract function process();

    abstract function isSubmitted();

    abstract function render();

    abstract function submit();
}

?>
