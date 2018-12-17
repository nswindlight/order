<?php
/**
 * Created by PhpStorm.
 * User: Tina
 * Date: 2018/12/16
 * Time: 02:31
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends AuthController
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('importLog_model');
        $this->load->model('orderGoods_model');
    }
    public function index(){
        
    }
    public function  logs(){
        $data['assets'] = $this->lw_assets->getPageAssets(['datetime']);

        $data['breadcrumb'] = [
            ['订单管理',null]
        ];

        $this->_tpl_page('order/logs', $data);
    }

    public function page($page){
        $post = $this->input->post();
        $rs = $this->importLog_model->getList($post, $page);
        if($rs['success'] === false) {
            lwReturn($rs);
        }
        $data = $rs['data'];
        $this->rs['html'] = $this->_view('order/page', $data, true);
        $this->rs['msg'] = '列表';
        $this->rs['success'] = true;
        lwReturn($this->rs);
    }

    public function import(){
        $data['assets'] = $this->lw_assets->getPageAssets(['xlsx']);

        $data['breadcrumb'] = [
            ['导入订单',null]
        ];

        $this->_tpl_page('order/import', $data);
    }

    public function add(){
        $post = $this->input->post();

        if(!$post){
            $this->rs['msg'] = '请提交数据';
            lwReturn($this->rs);
        }
        $data = json_decode($post['data'],true);
        if(!$data){
            $this->rs['msg'] = '未解析到任何数据';
            lwReturn($this->rs);
        }
        $goods = [];
        $err = [];
        $fields = [];
        foreach ($data as $k=>$v){
            $index = $k+2;
            $name = '';
            $num = '';
            if($v[19]){
                $res =explode(';',$v[19]);
                if(count($res) < 2){
                    $d = $v;
                    $d[] = $index.'行：身份信息格式错误';
                    $err[] = $d;
                    continue;
                }
                $r1 = explode(':',$res[0]);
                if(empty($r1[1])){
                    $d = $v;
                    $d[] = $index.'行：身份信息格式错误';
                    $err[] = $d;
                    continue;
                }
                $name = $r1[1];
                $r1 = explode(':',$res[1]);
                if(empty($r1[1])){
                    $d = $v;
                    $d[] = $index.'行：身份信息格式错误';
                    $err[] = $d;
                    continue;
                }
                $num = $r1[1];
            }
            $g = explode('*',$v[8]);
            $shanghu_good_num = 1;
            $g_code = $g[0];
            if(count($g) == 2){
                $shanghu_good_num = intval($g[1]);
            }
            $field = [
                'order_sn'=>trim($v[0]),
                'out_order_sn'=>trim($v[1]),
                'goods_status'=>trim($v[2]),
                'bill_status'=>trim($v[3]),
                'bill_success_time'=>trim($v[4]),
                'good_name'=>trim($v[5]),
                'good_type'=>trim($v[6]),
                'good_style'=>trim($v[7]),
                'good_spece'=>trim($v[8]),
                'spec_code'=>trim($v[9]),
                'good_price'=>sprintf("%.2f",$v[10]),
                'good_cost'=>sprintf("%.2f",$v[11]),
                'save_prcie_type'=>trim($v[12]),
                'svae_price'=>sprintf("%.2f",$v[13]),
                'good_num'=>intval($v[14]),
                'subtotal'=>sprintf("%.2f",$v[15]),
                'save_price'=>sprintf("%.2f",$v[16]),
                'actual_price'=>sprintf("%.2f",$v[17]),
                'offset_integral'=>intval($v[18]),
                'message'=>trim($v[19]),
                'receiver'=> $name? $name :$v[20],
                'people_name'=>$name,
                'people_num'=>$num,
                'phone_num'=>trim($v[21]),
                'sheng'=>trim($v[22]),
                'shi'=>trim($v[23]),
                'qu'=>trim($v[24]),
                'address'=>trim($v[25]),
                'buyer_message'=>trim($v[26]),
                'fahuo_status'=>trim($v[27]),
                'fahuo_type'=>trim($v[28]),
                'wuliu_company_name'=>trim($v[29]),
                'wuliu_num'=>trim($v[30]),
                'fahuo_user'=>trim($v[31]),
                'fahuo_time' => trim($v[32]),
                'tuikuan_status'=>trim($v[33]),
                'tuikuan_price'=>trim($v[34]),
                'company_name'=>trim($v[35]),
                'week_info'=>trim($v[36]),
                'shanghu_good_num'=>$shanghu_good_num,
                'good_messae'=>$g_code,
            ];
            $fields[] = $field;
        }

        if($err){
            $this->rs['data'] = $err;
            $this->rs['msg'] = '数据存在错误请检查';
            return $this->rs;
        }
        if(!$fields){
            $this->rs['msg'] = '未解析到数据';
            return $this->rs;
        }
        $log = [
            'ctime'=>date("Y-m-d H:i:s"),
            'uid'=>$this->session->adminId,
            'num'=>count($fields),
            'admin_name'=>$this->session->adminName,
            'success_count'=>0
        ];
        $log_id = $this->importLog_model->create($log);
        if(!$log_id){
            $this->rs['msg'] = '导入纪录保存失败，请重新提交';
            return $this->rs;
        }
        $i = 0;
        foreach($fields as $k=>$v){
            $field = $v;
            $field['log_id'] = $log_id;
            if($this->orderGoods_model->create($field)){
                $i ++;
            }else{
                $err[] = $k;
            }
        }
        $log_id = $this->importLog_model->update(['success_count'=>$i],$log_id);

        $this->rs['success'] = true;
        $this->rs['msg'] =  '导入成功';
        $this->rs['id'] = $log_id;
        lwReturn($this->rs);
    }
}