<?php

namespace controller;
use Service\ServiceCommun;

class addItem
{
    protected ServiceCommun $serviceCommun;
    
    public function __construct()
    {
        $this->serviceCommun = new ServiceCommun();
    }
    
    function addItemView($twig, $menu, $chemin, $cat, $dpt): void
    {
        $template = $twig->load("add.html.twig");
        echo $template->render(array(
                "breadcrumb" => $menu,
                "chemin" => $chemin,
                "categories" => $cat,
                "departements" => $dpt
            )
        );

    }

    public function addNewItem($twig, $menu, $chemin, $allPostVars): void
    {
        date_default_timezone_set('Europe/Paris');
        $formData = $this->serviceCommun->sanitizeFormData($_POST);
        $errors = $this->serviceCommun->validateFormData($formData);

        if (!empty($errors)) {
            $this->serviceCommun->displayErrors($twig, $menu, $chemin, $errors);
        } else {
            $this->serviceCommun->saveFormData($allPostVars);
            $this->serviceCommun->displaySuccess($twig, $menu, $chemin);
        }
    }
}

