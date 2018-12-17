<?php
/**
 * Created by PhpStorm.
 * User: Tina
 * Date: 2018/12/16
 * Time: 17:59
 */
?>
<div class="row table-responsive no-padding">
    <div class="col-md-12">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>#</th>
                <th>操作人</th>
                <th>时间</th>
                <th>提交数量</th>
                <th>成功导入数量</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!$dataList) {
                echo "<td>当前没有数据</td>";
            } else {
                $i = 1;
                foreach ($dataList as $data): ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= $data['admin_name'] ?></td>
                        <td><?= $data['ctime'] ?></td>
                        <td><?= $data['num'] ?></td>
                        <td><?= $data['success_count'] ?></td>
                        <td>
                            <a href="<?=  site_url('order/list/'.$data['id'])?>" class="btn btn-sm btn-info btn-log-info">详情</a>
                        </td>
                    </tr>
                    <?php $i++; endforeach;
            } ?>
            </tbody>
        </table>
    </div>
</div><!-- /.row -->
<div class="row">
    <div class="text-center">
        <?php if (isset($pageHtml)) {
            echo $pageHtml;
        } ?>
    </div>
</div><!-- /.row -->


