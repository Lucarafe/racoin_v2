<?php

namespace controller;

use AllowDynamicProperties;
use model\Categorie;
use model\Annonce;
use model\Photo;
use model\Annonceur;

#[AllowDynamicProperties] class getCategorie {

    protected $categories = array();

    public function getCategories() {
        return Categorie::orderBy('nom_categorie')->get()->toArray();
    }

    public function getCategorieContent($n) {
        $annonces = Annonce::with("Annonceur")->orderBy('id_annonce','desc')->where('id_categorie', "=", $n)->get();
        $results = array();
        foreach ($annonces as $annonce) {
            $annonce->nb_photo = $annonce->photo->count();
            $annonce->url_photo = $annonce->photo->count() > 0 ? $annonce->photo->first()->url_photo : 'img/noimg.png';

            $annonce->nom_annonceur = $annonce->annonceur()->first()->nom_annonceur;
            $results[] = $annonce;
        }
        return $results;
    }

    public function displayCategorie($twig, $menu, $chemin, $cat, $n) {
        $template = $twig->load("index.html.twig");
        $menu = array(
            array('href' => $chemin,
                'text' => 'Acceuil'),
            array('href' => $chemin."/cat/".$n,
                'text' => Categorie::find($n)->nom_categorie)
        );

        $this->annonce = $this->getCategorieContent($n);
        echo $template->render(array(
            "breadcrumb" => $menu,
            "chemin" => $chemin,
            "categories" => $cat,
            "annonces" => $this->annonce));
    }
}
