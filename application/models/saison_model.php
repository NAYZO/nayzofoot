<?php
class Saison_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }
    
    function get_all(){
        $query = $this->db->get('saison');
            return $query->result();
    }
    
    function add_saison(){
            $data = array(
            'saison' => $this->input->post('saison')
        );
        $this->db->insert('saison', $data);
        return  $this->db->insert_id()  ;
    }
    
    function update_saison(){
            $data = array(
            'saison' => $this->input->post('saison'),
        );
        $this->db->update('saison', $data);
    }    
    
    function get_saison($id){
        $this->db->select('*')
                ->from('saison')
                ->where('id', $id)
                ->limit(1);
        $query = $this->db->get();
        if($query->num_rows()==1)
            return $query->result();
        else
            return false;
    }
}