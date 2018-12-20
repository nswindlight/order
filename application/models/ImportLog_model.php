<?php
/**
 * Created by PhpStorm.
 * User: Tina
 * Date: 2018/12/16
 * Time: 11:59
 */
class ImportLog_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('lw_db', ['tb_name' => static::$_tableName], 'tb_import_log');

    }
    protected static $_tableName = 'tb_import_log';
    public function getList( $post, $page = null){
        $this->load->library('lw_pagination');
        $post = lwCheckValue($post, ['startDate', 'endDate']);

        $startDate = $post['startDate'];
        $startTime = $startDate.' 00:00:00';
        $endTime = $post['endDate'] ? ($post['endDate'].' 23:59:59') : ($startDate.' 23:59:59');
        $where = "ctime BETWEEN '{$startTime}' AND '{$endTime}'";
        $sql = "SELECT
                    a.*
                FROM
                    " . static::$_tableName . " a
                WHERE {$where}";

        $hasWhere = true;
        $group = null;
        $order = "a.id desc";
        $paramFilter = [];
        $data = $this->lw_pagination->lists($sql, [], $page, $hasWhere, $group, $order, $paramFilter, $pageSize = 10);
        $this->rs['success'] = true;
        $this->rs['data'] = $data;

        return $this->rs;
    }

    public function create($field){
       return $this->tb_import_log->insert($field);
    }
    public function update($field,$where){
        return $this->tb_import_log->update($field,$where);
    }
}