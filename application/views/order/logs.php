<?php
/**
 * Created by PhpStorm.
 * User: Tina
 * Date: 2018/12/16
 * Time: 14:42
 */
?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">筛选</h3>

                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div class="row col-md-12">
                    <div class="form-body">
                        <div class="col-md-3">
                            <label>开始时间</label>
                            <input id="search-start-date" type="text" class="form-control form-date date-day" readonly value="<?=date('Y-m-d')?>">
                        </div>
                        <div class="col-md-3">
                            <label>结束时间</label>
                            <div class="input-group">
                                <input id="search-end-date" type="text" class="form-control form-date date-day" readonly value="<?=date('Y-m-d')?>">
                                <span class="input-group-addon"><a onclick="Utils.clearDatetimePicker(this)" href="javascript:;"><span class="glyphicon glyphicon-remove"></span></a></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label> &nbsp;</label>
                            <div class="input-group">
                            <button id="btn-search" class="btn btn-primary btn-block" type="button"><i class="fa fa-search"></i> 搜索</button>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
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
                    <!--动态ajax加载博文-->
                </div>
            </div>
        </div><!-- /.box -->
    </div><!-- /.col -->
</div><!-- /.row -->

<script>
    var btnSearch = $("#btn-search");
    var btnExcel = $("#btn-excel");
    $(function () {
        Utils.dateInit();
        initBind();
        getListByPage(1);
    });

    // DOM 元素绑定事件
    function initBind() {
        btnSearch.on("click", function () {
            getListByPage(1);
        });
    }

    // 获取列表数据
    function getListByPage(page) {
        var startDate = $("#search-start-date").val().trim();
        var endDate = $("#search-end-date").val().trim();
        Utils.ajax({
            url: "<?=site_url('order/page')?>/" + page,
            data: { startDate: startDate, endDate: endDate},
            success: function (data) {
                if(data.success){
                    $("#dataList").html(data.html);
                } else {
                    Utils.noticeWarning(data.msg);
                }
            }
        });
    }


</script>