<?php
/**
 * Created by PhpStorm.
 * User: Tina
 * Date: 2018/12/16
 * Time: 19:44
 */
?>
<div class="box" style="width: 500px;margin: 10px auto;">
    <div class="box-header with-border">
        <h3 class="box-title">导入订单</h3>
    </div><!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-8">
                <input type="file" id="file_input">
            </div>
            <div class="col-sm-4">
                <button type="button" class="btn-check btn btn-block btn-primary btn-sm">提交数据</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(".btn-check").click(function(){
        var fileinput = document.getElementById('file_input');
        if(!fileinput.files.length) {
            Utils.noticeWarning('请选择文件');
            return;
        }
        var X = XLSX;
        var f = fileinput.files[0];
        var reader = new FileReader();
        reader.onload = function(e) {
            var data = e.target.result;
            var wb = XLSX.read(data, {
                type: 'binary',
                dateNF:'yyyy-m-d h:mm:ss',
            });
            check_data(XLSX.utils.sheet_to_json(wb.Sheets[wb.SheetNames[0]],{defval:'',header :1}));
        };
        reader.readAsBinaryString(f);
    })
    function check_data(data){
        var header_str = '订单号\t外部订单号\t订单商品状态\t交易成功时间\t商品名称\t商品类型\t商品类目\t商品规格\t规格编码\t商品编码\t商品单价\t商品成本价\t商品优惠方式\t商品优惠后价格\t商品数量\t商品金额小计\t店铺优惠（分摊）\t商品实际成交金额\t商品抵用积分数\t商品留言\t收货人/提货人\t收货人手机号/提货人手机号\t收货人省份\t收货人城市\t收货人地区\t详细收货地址/提货地址\t买家备注\t商品发货状态\t商品发货方式\t商品发货物流公司\t商品发货物流单号\t发货员工\t商品发货时间\t商品退款状态\t商品已退款金额\t商家订单备注\t周期购信息';
        var headers = header_str.split("\t");
        var file_head = data[0];
        for(var i=0;i<headers.length;i++){
            if(file_head[i] != headers[i]){
                Utils.noticeWarning('文件头部验证失败');
                return;
            }
        }
        var errs = new Array();
        $.each(data,function(n,v){
            if(!v[0] || !v[4] || !v[7]|| !v[8] || !v[14]|| !v[20] || !v[21] || !v[22] || !v[23] || !v[24] || !v[25] ||  !v[34]){
                var line = n+1;
                errs.push('第'+line +'行数据存在问题,必填项目未全部填写');
            }
        })
        if(errs.length >0){
            Utils.noticeAlive(errs.join('<br />'),'error');
            return;
        }

        data.splice(0,1);
        Utils.ajax({
            url:'<?= site_url("order/add")?>',
            data:{data:JSON.stringify(data)},
            success:function(data){
                Utils.alertSuccess(data.msg)
            }
        })
    }
</script>
