<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Book_store extends CI_Model {
    
    public function add_book($data){
        $query = $this->db->insert('store_books', $data);
        return $this->db->affected_rows();
    }


    public function add_book_stock($data){
        $query = $this->db->insert_batch('store_books', $data);
        return $this->db->affected_rows();
    }



    public function check_book($id){
        $query = $this->db->get_where('store_books', array('book_id' => $id));
        return $query;
    }

    public function get_stock($book_id){
        $query =  $this->db->select('*');
        $this->db->from('store_books');

        $this->db->where('is_disabled', 0);
        //$this->db->where('book_id', $book_id);
        //$this->db->group_by('store_books.class');
        $queryRes = $this->db->get();
        return $queryRes->result_array();
    }


 public function getStudentByStudentId($id) {
        $this->db->select('classes.class')->from('student_session');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('students', 'students.id = student_session.student_id');
        $this->db->where('student_id', $id);
        //$this->db->where('session_id', $this->current_session);
        //$this->db->order_by('id');
        $query = $this->db->get();
            return  $query->result_array();
        //return $query->row_array();
    }

    function getOrderLists($studentId){
      $this->db->select('std_id,s.class,s.title,s.brand,s.author,s.quantity,s.price');
      $this->db->join('students', 'students.id = book_store_orders.std_id');
      $this->db->join('store_books as s', 's.id = book_store_orders.book_id');
      $this->db->where('std_id', $studentId);
      $this->db->group_by('s.class');
      $query = $this->db->get('book_store_orders');
      return $query->result_array(); 
    }


    // public function getStudentByStudentId($id) {
    //     $this->db->select('classes.class,students.firstname')->from('student_session');
    //     $this->db->join('classes', 'student_session.class_id = classes.id');
    //     $this->db->join('students', 'students.id = student_session.student_id');
    //     $this->db->where('student_id', $id);
    //     //$this->db->where('session_id', $this->current_session);
    //     //$this->db->order_by('id');
    //     $query = $this->db->get();
    //     return $query->row_array();
    // }

    
    public function getClasses(){
        $this->db->select('store_books.class')->from('store_books')->group_by("store_books.class");
        $queryRes = $this->db->get();
        return $queryRes->result_array();
    }

    public function get_stock_by_ids($data){
    
        $query = $this->db->select('*')->from('store_books')->where_in('id' , $data);
        $queryRes = $this->db->get();
        return $queryRes->result_array();
    }


    public function edit_book($id){
        $query = $this->db->get_where('store_books', array('id' => $id));
        return $query->row();
    }
    
    public function update_book($data){
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('store_books', $data);
            return true;
        } 
        return false;
    }

    public function disable_book($data){
        if (isset($data['id'])) {

            $this->db->where('id', $data['id']);
            $this->db->update('store_books', $data);
            return true;
        } 
        return false;
    }



    public function check_book_exists($isbn){
        $this->db->where('book_id', $isbn);
        $queryRes = $this->db->get('store_books');
        if($queryRes->num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }
    
    public function getLastQuantity($book_id){
        $this->db->select('quantity');
        $this->db->where('id', $book_id);
        $queryRes = $this->db->get('store_books');
        $res = $queryRes->result_array();
        //print_r($res[0]['quantity']);
        return (int)$res[0]["quantity"];
        
    }



    public function updateStockOnOrderCompltion($order){
        foreach ($order as $p){

            if($this->getLastQuantity($p['book_id']) > 0){
            $this->db->set('quantity', '`quantity`- '. $p['quantity'], FALSE);

            $this->db->where('id', $p['book_id']);

            $this->db->update('store_books');
            }
        }
    }

}
?>