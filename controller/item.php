<?php

namespace controller;

use AllowDynamicProperties;
use model\Annonce;
use model\Annonceur;
use model\Categorie;
use model\Departement;
use model\Photo;
use service\ServiceCommun;

#[AllowDynamicProperties] class item
{
    protected ServiceCommun $serviceCommun;

    public function __construct()
    {
        $this->serviceCommun = new ServiceCommun();
    }

    function afficherItem($twig, $menu, $chemin, $n, $cat): void
    {

        $this->annonce = Annonce::find($n);
        if (!isset($this->annonce)) {
            echo "404";
            return;
        }

        $menu = array(
            array('href' => $chemin,
                'text' => 'Acceuil'),
            array('href' => $chemin . "/cat/" . $n,
                'text' => Categorie::find($this->annonce->id_categorie)?->nom_categorie),
            array('href' => $chemin . "/item/" . $n,
                'text' => $this->annonce->titre)
        );

        $this->annonceur = Annonceur::find($this->annonce->id_annonceur);
        $this->departement = Departement::find($this->annonce->id_departement);
        $this->photo = Photo::where('id_annonce', '=', $n)->get();
        $template = $twig->load("item.html.twig");
        echo $template->render(array("breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $this->annonce,
            "annonceur" => $this->annonceur,
            "dep" => $this->departement->nom_departement,
            "photo" => $this->photo,
            "categories" => $cat));
    }

    function supprimerItemGet($twig, $menu, $chemin, $n)
    {
        $this->annonce = Annonce::find($n);
        if (!isset($this->annonce)) {
            echo "404";
            return;
        }
        $template = $twig->load("delGet.html.twig");
        echo $template->render(array("breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $this->annonce));
    }


    function supprimerItemPost($twig, $menu, $chemin, $n, $cat)
    {
        $this->annonce = Annonce::find($n);
        $reponse = false;
        if (password_verify($_POST["pass"], $this->annonce->mdp)) {
            $reponse = true;
            photo::where('id_annonce', '=', $n)->delete();
            $this->annonce->delete();

        }

        $template = $twig->load("delPost.html.twig");
        echo $template->render(array("breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $this->annonce,
            "pass" => $reponse,
            "categories" => $cat));
    }

    function modifyGet($twig, $menu, $chemin, $id)
    {
        $this->annonce = Annonce::find($id);
        if (!isset($this->annonce)) {
            echo "404";
            return;
        }
        $template = $twig->load("modifyGet.html.twig");
        echo $template->render(array("breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $this->annonce));
    }

    function modifyPost($twig, $menu, $chemin, $n, $cat, $dpt)
    {
        $this->annonce = Annonce::find($n);
        $this->annonceur = Annonceur::find($this->annonce->id_annonceur);
        $this->categItem = Categorie::find($this->annonce->id_categorie)->nom_categorie;
        $this->dptItem = Departement::find($this->annonce->id_departement)->nom_departement;

        $reponse = false;
        if (password_verify($_POST["pass"], $this->annonce->mdp)) {
            $reponse = true;

        }

        $template = $twig->load("modifyPost.html.twig");
        echo $template->render(array("breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $this->annonce,
            "annonceur" => $this->annonceur,
            "pass" => $reponse,
            "categories" => $cat,
            "departements" => $dpt,
            "dptItem" => $this->dptItem,
            "categItem" => $this->categItem));
    }

    function edit($twig, $menu, $chemin, $allPostVars): void
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
