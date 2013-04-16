<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Match extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('equipe_model');
        $this->load->model('match_model');
        $this->load->model('saison_model');
        $this->twig->addFunction('getsessionhelper');
    }

    public function index() {
        if (!$this->session->userdata('login_in'))
            redirect('/');
        else {
                // affichage calendrier de liste des matches 
             $this->twig->render('match/gestionmatch');
        }
    }

    public function ajout() {
        if (!$this->session->userdata('login_in'))
            redirect('/');
        else {
            $this->form_validation->set_rules('arbitre', 'Arbitre', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $data['saisons'] = $this->saison_model->get_all();
                $this->twig->render('match/ajoutmatch', $data);
            } else {
                $this->equipe_model->add_equipe();
                redirect('/equipe');
            }
        }
    }

    public function modifier($id) {
        if (!$this->session->userdata('login_in'))
            redirect('/');
        else {
            $this->form_validation->set_rules('nom', 'Nom', 'trim|required');
            $this->form_validation->set_rules('directeur', 'directeur', 'trim|required');
            $this->form_validation->set_rules('entreneur', 'entreneur', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->twig->render('modifierequipe', $this->equipe_model->get_equipe($id)->row());
            } else {
                $this->equipe_model->update_equipe();
                redirect('/equipe');
            }
        }
    }

    public function voir($id) {
        if (!$this->session->userdata('login_in'))
            redirect('/');
        else {
            $this->twig->render('equipe/voirequipe', $this->equipe_model->get_equipe($id)->row());
        }
    }

    
}