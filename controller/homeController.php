<?php

namespace controller;

use model\Annonce;
use model\Photo;
use model\Annonceur;

class homeController
{
    protected $annonce = array();

    public function displayAllAnnonce($twig, $chemin, $cat)
    {
        $template = $twig->load("index.html.twig");
        $menu     = array(
            array(
                'href' => $chemin,
                'text' => 'Acceuil'
            ),
        );

        $this->annonce = $this->getAll();
        echo $template->render(array(
            "breadcrumb" => $menu,
            "chemin"     => $chemin,
            "categories" => $cat,
            "annonces"   => $this->annonce
        ));
    }



    public function getAll() : array
    {
        $annonces = Annonce::with("Annonceur")
            ->orderBy('id_annonce', 'desc')
            ->take(12)
            ->get();
        $results = array();
        foreach ($annonces as $annonce) {
            $annonce->nb_photo = $annonce->photo->count();
            $annonce->url_photo = $annonce->photo->count() > 0 ? $annonce->photo->first()->url_photo : 'img/noimg.png';

            $annonce->nom_annonceur = $annonce->annonceur()->first()->nom_annonceur;
            $results[] = $annonce;
        }
        return $results;
    }

}
