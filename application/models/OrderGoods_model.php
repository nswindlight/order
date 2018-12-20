<?php
/**
 * Created by PhpStorm.
 * User: Tina
 * Date: 2018/12/16
 * Time: 11:09
 */
class OrderGoods_model extends CI_Model
{

    protected static $_tableName = 'tb_order_good';
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('lw_db', ['tb_name' => static::$_tableName], 'tb_order_good');
    }
    public function create($field){
        return $this->tb_order_good->insert($field);
    }

    public function update($field,$where){
        return $this->tb_order_good->update($field,$where);
    }

    public function getList($log_id){
        $data = $this->db->select('*')
            ->from(static::$_tableName)
            ->where('log_id', $log_id)
            ->get()
            ->result_array();
        return $data;
    }
}