<?php
/**
 * Created by PhpStorm.
 * User: mohammed
 * Date: 4/12/17
 * Time: 3:46 PM
 */
class Login_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

    public function individual_user_existance($phone_no) {
        $this->db->select('individual_users.id');
        $this->db->from('individual_users');
        $this->db->where('individual_users.phone_no', $phone_no);
        $query = $this->db->get();
        if($query->num_rows() > 0) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }


    public function insert_individual_user($user_data_db) {
        $insert = $this->db->insert('individual_users', $user_data_db);
        if($insert) {
            return TRUE;
        }
        else{
            return FALSE;
        }
    }

    public function corporate_user_existance($phone_no) {
        $this->db->select('corporate_users.id');
        $this->db->from('corporate_users');
        $this->db->where('corporate_users.admin_phone', $phone_no);
        $query = $this->db->get();
        if($query->num_rows() > 0) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    public function insert_corporate($corporate_data_db) {
        $insert = $this->db->insert('corporates', $corporate_data_db);
        if($insert) {
            return TRUE;
        }
        else{
            return FALSE;
        }
    }

    public function insert_corporate_users($corporate_user_db) {
        $insert = $this->db->insert('corporate_users', $corporate_user_db);
        if($insert) {
            return TRUE;
        }
        else{
            return FALSE;
        }
    }

    public function corporate_user_check($phone_no,$corporate_id) {
        $this->db->select('corporate_users.id');
        $this->db->from('corporate_users');
        $this->db->where('corporate_users.admin_phone', $phone_no);
        $this->db->where('corporate_users.corporates_id', $corporate_id);
        $query = $this->db->get();
        if($query->num_rows() > 0) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
	
	public function fetch_logged_individual($phone_no) {
        $this->db->select('individual_users.id, individual_users.first_name, individual_users.last_name, individual_users.email, individual_users.phone_no');
        $this->db->from('individual_users');
        $this->db->where('individual_users.phone_no', $phone_no);
        $query = $this->db->get();
        if($query->num_rows() > 0) {
            return $query->result_array();
        }
        else {
            return FALSE;
        }
    }

    public function fetch_logged_corporate($phone_no) {
        $this->db->select('corporate_users.id, corporate_users.corporates_id, corporate_users.admin_name, corporate_users.admin_phone');
        $this->db->from('corporate_users');
        $this->db->where('corporate_users.admin_phone', $phone_no);
        $query = $this->db->get();
        if($query->num_rows() > 0) {
            return $query->result_array();
        }
        else {
            return FALSE;
        }
    }
}