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
                'ctime' => ''
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
        $i= 0;
        foreach($fields as $k=>$v){
            $field = $v;
            $field['log_id'] = $log_id;
            $field['ctime'] = $log['ctime'];
            if($this->orderGoods_model->create($field)){
                $i ++;
            }else{
                $err[] = $k;
            }
        }
        $this->importLog_model->update(['success_count'=>$i],['id'=>$log_id]);

        $this->rs['success'] = true;
        $this->rs['msg'] =  '导入成功'.$i."条数据";
        $this->rs['id'] = $log_id;
        lwReturn($this->rs);
    }

    public function info($log_id){
        $data = [];
        $data['assets'] = $this->lw_assets->getPageAssets();
        $data['data'] = $this->orderGoods_model->getList($log_id);
        $data['breadcrumb'] = [
            ['导入纪录',site_url(['order/logs'])],
            ['导入信息','']
        ];
        $data['shanghu'] = ['威妮海购','斯旺森','海带','德国双心','PPR总部','HealthMore'];
        $data['log_id'] = $log_id;
        $this->_tpl_page('order/info', $data);
    }

    public function excel($log_id){
        $tpls = [
            '威妮海购'=>[
                '订单编号'=>function($model){return $model['order_sn'];},
                '订单时间'=>'',
                '买家会员名'=>'',
                '支付人'=>function($model){return '';},
                '收件人'=>function($model){return $model['receiver'];},
                '身份证'=>function($model){return $model['people_num'];},
                '手机号'=>function($model){return $model['phone_num'];},
                '州省'=>function($model){return $model['sheng'];},
                '区市'=>function($model){return $model['shi'];},
                '区县'=>function($model){return $model['qu'];},
                '详细地址'=>function($model){return $model['address'];},
                '商品编号'=>function($model){return $model['good_messae'];},
                '数量'=>function($model){return $model['good_messae'].'*'.$model['shanghu_good_num'];},
                '售价(含税)'=>function($model){return $model['actual_price'];},
                '运费'=>'',
                '优惠'=>'',
                '客户备注'=>''

            ],
            '斯旺森'=>[
                '日期'=>function($model){return date("Y月d日",strtotime($model['ctime']));},
                'SW码'=>function($model){return $model['good_messae'];},
                '商品名称'=>function($model){return $model['good_name'];},
                '数量'=>function($model){return $model['shanghu_good_num'];},
                '姓名'=>function($model){return $model['receiver'];},
                '地址'=>function($model){return $model['address'];},
                '电话'=>function($model){return $model['phone_num'];},
                '身份证'=>function($model){return $model['people_num'];},
                '发出运单号（京东快递）'=>''
            ],
            '海带'=>[
                '订单编号'=>function($model){return $model['order_sn'];},
                '支付单号'=>'',
                '货号'=>function($model){return $model['good_messae'];},
                '商品名称'=>function($model){return $model['good_name'];},
                '有效期/生产日期'=>'',
                '数量'=>function($model){return $model['shanghu_good_num'];},
                '收货人'=>function($model){return $model['receiver'];},
                '联系电话'=>function($model){return $model['phone_num'];},
                '身份证号码'=>function($model){return $model['people_num'];},
                '省'=>function($model){return $model['sheng'];},
                '市'=>function($model){return $model['shi'];},
                '区'=>function($model){return $model['qu'];},
                '详细地址'=>function($model){return $model['address'];},
                '留言'=>''

            ],
            '德国双心'=>[
                '订单编号'=>function($model){return $model['order_sn'];},
                '收货人姓名'=>function($model){return $model['receiver'];},
                '收货人身份证号码'=>function($model){return $model['people_num'];},
                '收货地址（省）'=>function($model){return $model['sheng'];},
                '收货地址（市）'=>function($model){return $model['shi'];},
                '收货地址（区）'=>function($model){return $model['qu'];},
                '收货地址（详细）'=>function($model){return $model['address'];},
                '联系电话'=>function($model){return $model['phone_num'];},
                '商品名称'=>function($model){return $model['good_name'];},
                '商品数量'=>function($model){return $model['shanghu_good_num'];},
                '商品编号'=>function($model){return $model['good_messae'];},
                '发货店铺'=>function($model){return '洋货节';},
                '备注'=>'',
                '支付人姓名'=>'',
                '支付人身份证'=>'',
                '快递公司'=>''

            ],
            'PPR总部'=>[
                '订单号'=>function($model){return $model['order_sn'];},
                '商品ID'=>'',
                '商品SN'=>function($model){return $model['good_messae'];},
                '商品单价(必填)'=>function($model){return $model['actual_price'];},
                '商品数量(必填)'=>function($model){return $model['shanghu_good_num'];},
                '收货人姓名'=>function($model){return $model['receiver'];},
                '收货人电话'=>function($model){return $model['phone_num'];},
                '身份证'=>function($model){return $model['phone_num'];},
                '省'=>function($model){return $model['sheng'];},
                '市'=>function($model){return $model['shi'];},
                '区县'=>function($model){return $model['qu'];},
                '详细地址'=>function($model){return $model['address'];},
                '邮编'=>'',
                '所属会员'=>function($model){return '13004154845';}

            ],
            'HealthMore'=>[
                '产品名称'=>function($model){return $model['good_name'];},
                '商品编号'=>function($model){return $model['order_sn'];},
                '数量'=>function($model){return $model['shanghu_good_num'];},
                '代理用户'=>'',
                '收货人'=>function($model){return $model['receiver'];},
                '身份证号'=>function($model){return $model['phone_num'];},
                '手机号'=>function($model){return $model['phone_num'];},
                '省份'=>function($model){return $model['sheng'];},
                '城市'=>function($model){return $model['shi'];},
                '县区'=>function($model){return $model['qu'];},
                '地址'=>function($model){return $model['address'];},
            ],
        ];
        $post = $this->input->post();
        $tpl_name = empty($post['tpl_name'])? '':trim($post['tpl_name']);
        if(!$log_id || !$tpl_name || !isset($tpls[$tpl_name])){
            $this->rs['msg'] = '参数错误';

            lwReturn($this->rs);
        }
        $data = $this->orderGoods_model->getList($log_id,$tpl_name);
        if(!$data){
            $this->rs['msg'] = '无数据';
            lwReturn($this->rs);
        }
        $excel_data = [];
        $title = [];
        foreach($tpls[$tpl_name] as $k=>$v){
            $title[] = $k;
        }
        foreach($data as $v){
            $d = [];
            foreach($tpls[$tpl_name] as $k=>$fn){
                if($fn){
                    $d[] = $fn($v);
                }else{
                    $d[] = '';
                }
            }
            $excel_data[] = $d;
        }
        $width = [];
        for ($i = 0; $i < sizeof($title); $i++) {
            $width[$i] = 30;
        }
        $this->load->library('lw_string');
        $this->load->helper('excel');
        $date = date('Y-d', time());
        $uniStr = $this->lw_string->getUniName();
        $path = "outputExcel/log/" . $date;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $fn = $path . "/" . $uniStr . ".xls";
        getExcel($title, $width, $excel_data , "$fn");
        $this->rs['success'] = true;
        $this->rs['msg'] = 'excel导出';
        $this->rs['excelPath'] = base_url($fn);
        lwReturn($this->rs);
    }

}