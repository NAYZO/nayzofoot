<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Equipe extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('equipe_model');
        $this->load->model('joueur_model');
        $this->load->model('classement_model');
        $this->load->model('match_model');
        $this->load->model('stade_model');
        
        $this->twig->addFunction('getsessionhelper');
    }

    public function index()
    {
        if (!$this->session->userdata('login_in'))
            redirect('/');
        else
        {
            $data['equipes'] = $this->equipe_model->get_all();
            $this->twig->render('equipe/gestionequipe', $data);
        }
    }

    public function ajout()
    {
        if (!$this->session->userdata('login_in'))
            redirect('/');
        else
        {
            $data = array();
            $data['stades'] = $this->stade_model->get_all();
            
            $this->form_validation->set_rules('nom_equipe', 'Nom', 'trim|required|xss_clean');
            $this->form_validation->set_rules('entreneur', 'Entreneur', 'trim|required|xss_clean');

            if ($this->form_validation->run() == FALSE)
                $this->twig->render('equipe/ajoutequipe', $data);
            else
            {
                $config['upload_path']   = './uploads/';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['max_size']	 = '100';
                $config['max_width']     = '1024';
                $config['max_height']    = '768';
                $config['encrypt_name']  = TRUE;
                
                $this->load->library('upload', $config);
                if ( ! $this->upload->do_upload())
                {              
                    $data['error'] = $this->upload->display_errors();
                    $this->twig->render('equipe/ajoutequipe', $data);
                }
                else
                {
                    $photo = $this->upload->data();
                    $this->equipe_model->add_equipe($photo['file_name']);
                    redirect('/equipe');
                }
            }
        }
    }

    public function modifier($id)
    {
        if (!$this->session->userdata('login_in'))
            redirect('/');
        else 
        {
            if(!$id)
                redirect('/');
            
            $data = array();
            $data['stades'] = $this->stade_model->get_all();
            
            $this->form_validation->set_rules('nom_equipe', 'Nom', 'trim|required|xss_clean');
            $this->form_validation->set_rules('entreneur', 'Entreneur', 'trim|required|xss_clean');

            if ($this->form_validation->run() == FALSE) 
            {
                $data['equipe'] = $this->equipe_model->get_equipe($id)->row();
                
                if(!$data['equipe'])
                    redirect('/');
                
                $this->twig->render('equipe/modifierequipe', $data);
            } 
            else 
            {
                $config['upload_path'] = './uploads/';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['max_size']	= '100';
                $config['max_width']  = '1024';
                $config['max_height']  = '768';
                $config['encrypt_name']  = TRUE;
                $this->load->library('upload', $config);

                if ( ! $this->upload->do_upload()){}
                $photo = $this->upload->data();
                $data['photo'] = $photo['file_name']; 
                $data['id'] = $id;
                if($photo['file_name'])
                { 
                        $this->suppphoto($id);
                        $this->equipe_model->update_equipephoto($data);
                        redirect('/equipe');
                }
                else
                {
                    $this->equipe_model->update_equipe($id);
                    redirect('/equipe');
                }
            }
        }
    }
    

    public function voir($id)
    {
        if (!$this->session->userdata('login_in'))
            redirect('/');
        else
        {
            if(!$id)
                redirect('/');
            
            $equipe = $this->equipe_model->get_equipe($id)->row();
            
            if(!$equipe)
                redirect('/');
            
            if($equipe->stade == 0)
                $equipe->stade = 'Aucun stade';
            else
                $equipe->stade = $this->stade_model->get_stade($equipe->stade)->nom;
            
            $data['equipe'] = $equipe;
            $data['joueurs'] = $this->joueur_model->get_joueur_by_equipe($id);
            $this->twig->render('equipe/voirequipe', $data);
        }
    }
    
    public function supprimer($id) {
        if (!$this->session->userdata('login_in'))
            redirect('/');
        else {
            if(!$id) redirect('/');
            $this->suppphoto($id);
            
            $this->match_model->delete_match_by_equipe($id);
            $this->equipe_model->delete_equipe($id);
            $jouers = $this->joueur_model->get_all_by_equipe($id);
            foreach($jouers as $req)
            {
                $this->suppphoto_joueur($req->photo);
            }
            $this->joueur_model->delete_joueur_by_equipe($id);
            $this->classement_model->delete_classement_equipe($id);
                redirect('/equipe');
        }
    }

     public function suppphoto($id)
    {
         if(!$id) redirect('/');
        $logo = $this->equipe_model->get_equipe($id)->row()->logo;
        $path = __DIR__.'/../../uploads/'.$logo;
        try { unlink ($path); } catch(Exception $e){}
    }
    
    public function suppphoto_joueur($photo)
    {
        $path = __DIR__.'/../../uploads/'.$photo;
        try { unlink ($path); } catch(Exception $e){}
    }
}