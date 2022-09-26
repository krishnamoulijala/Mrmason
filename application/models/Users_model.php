<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Users_model extends CI_MODEL
{
    public function get($table)
    {
        return $this->db->get($table);
    }

    public function check($table, $data)
    {
        $this->db->where($data);
        return $this->db->get($table);
    }

    public function selectedCheck($select, $table, $data)
    {
        $this->db->select($select);
        $this->db->from($table);
        $this->db->where($data);
        return $this->db->get();
    }

    public function update($table, $check, $data)
    {
        $this->db->where($check);
        return $this->db->update($table, $data);
    }

    public function delete($table, $where)
    {
        return $this->db->delete($table, $where);
        /*$afftectedRows = $this->db->affected_rows();
        return $afftectedRows;*/
    }

    /**
     * Inserting into database table with data
     * @param $table
     * @param $data
     * @return mixed
     */
    public function save($table, $data)
    {
        return $this->db->insert($table, $data);
    }

    /**
     * Inserting into database table and returning with last inserted Id
     * @param $table
     * @param $data
     * @return mixed
     */
    public function insertReturnLastId($table, $data)
    {
        $this->db->insert($table, $data);
        $insertId = $this->db->insert_id();
        return $insertId;
    }
}