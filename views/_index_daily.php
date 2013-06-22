<h2>Daily Hours</h2>

<script>
    $(document).ready(function () {
        $('#daily-tab a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        })
    });
</script>

<ul id="daily-tab" class="nav nav-pills">
    <?php
    $active = 'active';
    foreach ($times['daily'] as $date => $daily) {
        if ($date == 'total') continue;
        ?>
        <li class="<?php echo $active; ?>">
            <a href="#daily-<?php echo $date; ?>"><?php echo date('D, j-M', strtotime($date)); ?></a>
        </li>
        <?php
        $active = '';
    }
    ?>
</ul>
<div class="tab-content">
    <?php
    $active = 'active';
    foreach ($times['daily'] as $date => $daily) {
        if ($date == 'total') continue;
        ?>
        <div id="daily-<?php echo $date; ?>" class="tab-pane <?php echo $active; ?>">
            <h3>
                <?php
                echo date('D, j-M', strtotime($date));
                echo ' - ';
                echo Helper::formatHours($times['daily']['total'][$date]);
                echo 'h = $';
                echo number_format($profit ? $profit['total'][$date] : 0, 2);
                ?>

            </h3>

            <div class="row-fluid">
                <div class="span6">
                    <h4><i class="icon-user"></i> Hours by Staff</h4>
                    <table class="table">
                        <?php
                        foreach ($daily['staff'] as $staff => $profiles) {
                            if ($staff == 'total') continue;
                            ?>
                            <tr>
                                <td width="40%"><?php echo $staff; ?></td>
                                <td width="60%">
                                    <table class="table table-striped table-condensed">
                                        <tr>
                                            <th width="55%">profile</th>
                                            <th width="15%">hours</th>
                                            <th width="15%">cost</th>
                                            <th width="15%">profit</th>
                                        </tr>
                                        <?php
                                        foreach ($profiles as $profile => $hours) {
                                            ?>
                                            <tr>
                                                <td><?php echo $profile; ?></td>
                                                <td>
                                                    <span class="pull-right"><?php echo Helper::formatHours($hours); ?></span>
                                                </td>
                                                <td>
                                                    <span class="pull-right"><?php echo number_format(($hours * $saasu->getStaffRate($staff, $profile)) / $saasu->getStaffTaxRate($staff, $profile), 2); ?></span>
                                                </td>
                                                <td>
                                                    <span class="pull-right"><?php echo number_format(($hours * $saasu->getStaffProfit($staff, $profile)) / $saasu->getStaffTaxRate($staff, $profile), 2); ?></span>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                        <tr>
                                            <th>Total</th>
                                            <th>
                                                <span class="pull-right"><?php echo Helper::formatHours($daily['staff']['total'][$staff]); ?></span>
                                            </th>
                                            <th>
                                                <span class="pull-right"><?php echo '$' . number_format($cost ? $cost[$date]['total']['staff'][$staff] : 0, 2); ?></span>
                                            </th>
                                            <th>
                                                <span class="pull-right"><?php echo '$' . number_format($profit ? $profit[$date]['total']['staff'][$staff] : 0, 2); ?></span>
                                            </th>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </table>
                </div>
                <div class="span6">
                    <h4><i class="icon-briefcase"></i> Hours by Profile</h4>
                    <table class="table">
                        <?php
                        foreach ($daily['profile'] as $profile => $staffs) {
                            if ($profile == 'total') continue;
                            ?>
                            <tr>
                                <td width="40%"><?php echo $profile; ?></td>
                                <td width="60%">
                                    <table class="table table-striped table-condensed">
                                        <tr>
                                            <th width="50%">staff</th>
                                            <th width="25%">hours</th>
                                            <th width="25%">cost</th>
                                            <th width="25%">profit</th>
                                        </tr>
                                        <?php
                                        foreach ($staffs as $staff => $hours) {
                                            ?>
                                            <tr>
                                                <td><?php echo $staff; ?></td>
                                                <td>
                                                    <span class="pull-right"><?php echo Helper::formatHours($hours); ?></span>
                                                </td>
                                                <td>
                                                    <span class="pull-right"><?php echo number_format(($hours * $saasu->getStaffCost($staff, $profile)) / $saasu->getStaffTaxRate($staff, $profile), 2); ?></span>
                                                </td>
                                                <td>
                                                    <span class="pull-right"><?php echo number_format(($hours * $saasu->getStaffProfit($staff, $profile)) / $saasu->getStaffTaxRate($staff, $profile), 2); ?></span>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                        <tr>
                                            <th>Total</th>
                                            <th>
                                                <span class="pull-right"><?php echo Helper::formatHours($daily['profile']['total'][$profile]); ?></span>
                                            </th>
                                            <th>
                                                <span class="pull-right"><?php echo '$' . number_format($cost ? $cost[$date]['total']['profile'][$profile] : 0, 2); ?></span>
                                            </th>
                                            <th>
                                                <span class="pull-right"><?php echo '$' . number_format($profit ? $profit[$date]['total']['profile'][$profile] : 0, 2); ?></span>
                                            </th>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
        <?php
        $active = '';
    }
    ?>

</div>