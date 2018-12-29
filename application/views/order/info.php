<?php
/**
 * Created by PhpStorm.
 * User: Tina
 * Date: 2018/12/17
 * Time: 22:45
 */
?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">导出</h3>

                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div class="row col-md-12">
                    <?php foreach($shanghu as $v): ?>
                    <button class="export-btn btn btn-success" data-name="<?= $v ?>"><?= $v ?></button>
                    <?php endforeach;?>
                    <!-- /.box-body -->
                </div><!-- /.row -->
            </div><!-- ./box-body -->
        </div><!-- /.box -->
    </div><!-- /.col -->
    <div class="col-md-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">列表</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div id="dataList" class="row col-md-12">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>订单号</th>
                            <th>外部订单号</th>
                            <th>商品名称</th>
                            <th>商品规格</th>
                            <th>规格编码</th>
                            <th>收货人</th>
                            <th>手机号</th>
                            <th>商家</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data as $num =>$v): ?>
                        <tr>
                            <td><?= $num+1?></td>
                            <td><?= $v['order_sn']?></td>
                            <td><?= $v['out_order_sn']?></td>
                            <td><?= $v['good_name']?></td>
                            <td><?= $v['good_spece']?></td>
                            <td><?= $v['spec_code']?></td>
                            <td><?= $v['receiver']?></td>
                            <td><?= $v['phone_num']?></td>
                            <td><?= $v['company_name']?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div><!-- /.box -->
    </div><!-- /.col -->
</div><!-- /.row -->

<script>
    var btnExcel = $("#btn-excel");
    $(function () {
        initBind();
    });

    // DOM 元素绑定事件
    function initBind() {
        btnExcel.on("click", function () {
            var param = {};
            Utils.ajax({
                url: "<?=site_url('order/excel')?>",
                data: {param: JSON.stringify(param)},
                success: function (data) {
                    if(data.success){
                        window.open(data.excelPath);
                    } else {
                        Utils.noticeWarning(data.msg);
                    }
                }
            });
        })
        $(".export-btn").click(function(){
            var tpl_name = $(this).attr('data-name');
            Utils.ajax({
                url: "<?=site_url('order/excel/'.$log_id)?>",
                data: {tpl_name: tpl_name},
                success: function (data) {
                    if(data.success){
                        window.open(data.excelPath);
                    } else {
                        Utils.noticeWarning(data.msg);
                    }
                }
            });
        })
    }



</script>