<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Saison extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('saison_model');
        $this->load->model('classement_model');
        $this->twig->addFunction('getsessionhelper'); 
    }

    public function index() {
        if (!$this->session->userdata('login_in'))
            redirect('/');
        else {
            $data['saison'] = $this->saison_model->get_all();
            $this->twig->render('saison/gestionsaison', $data);
        }
    }

    public function ajout() {
        if (!$this->session->userdata('login_in'))
            redirect('/');
        else {
            $this->form_validation->set_rules('Saison', 'Saison', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->twig->render('saison/ajoutsaison');
            } else {
                $id = $this->saison_model->add_saison();
                $this->classement_model->add_classement($id);
                redirect('/saison');
            }
        }
    }

    public function modifier($id) {
        if (!$this->session->userdata('login_in'))
            redirect('/');
        else {
            $this->form_validation->set_rules('Saison', 'Saison', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $data['saison'] = $this->saison_model->get_saison($id)->row();
                $this->twig->render('modifiersaison', $data);
            } else {
                $this->saison_model->update_saison();
                redirect('/saison');
            }
        }
    }

    public function voir($id) {
        if (!$this->session->userdata('login_in'))
            redirect('/');
        else {
            $this->twig->render('saison/voirsaison', $this->saison_model->get_saison($id)->row());
        }
    }

    
}