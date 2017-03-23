<h2>
    <?php
    echo 'Total: ';
    echo $times ? $times['total']['total'] : 0;
    echo 'h = $';
    echo number_format($profit ? $profit['total']['total'] : 0, 2);
    ?>
</h2>

<div class="row-fluid">
    <div class="span6">
        <h3><i class="icon-user"></i> Hours by Staff</h3>
        <table class="table">
            <?php
            foreach ($times['total']['staff'] as $staff => $profiles) {
                if ($staff == 'total') continue;
                ?>
                <tr>
                    <td width="40%"><?php echo $staff; ?></td>
                    <td width="60%">
                        <table class="table table-striped table-condensed">
                            <tr>
                                <th width="55%">profile</th>
                                <th width="15%">hours</th>
                                <th width="15%">cost&nbsp;<i class="icon-info-sign" title="Ex GST"></i></th>
                                <th width="15%">profit&nbsp;<i class="icon-info-sign" title="Ex GST"></i></th>
                            </tr>
                            <?php
                            foreach ($profiles as $profile => $hours) {
                                $staffTaxRate = $saasu->getStaffTaxRate($staff, $profile);
                                $staffCost = $saasu->getStaffCost($staff, $profile);
                                $staffProfit = $saasu->getStaffProfit($staff, $profile);
                                $capHours = isset($saasu->profiles[$profile]['capHours']) ? $saasu->profiles[$profile]['capHours'] : 0;
                                if ($capHours && $capHours < $hours) {
                                    $_ = $capHours;
                                    $capHours = $hours - $capHours;
                                    $hours = $_;
                                }
                                ?>
                                <tr>
                                    <td><?php echo $profile; ?></td>
                                    <td>
                                        <span class="pull-right">
                                            <?php echo $hours; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="pull-right">
                                            <?php echo number_format(($hours * $staffCost) / $staffTaxRate, 2); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="pull-right">
                                            <?php echo number_format(($hours * $staffProfit) / $staffTaxRate, 2); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php
                                if ($capHours) {
                                    $cost['total']['staff'][$staff] -= ($capHours * $staffCost) / $staffTaxRate;
                                    $profit['total']['staff'][$staff] -= ($capHours * $staffProfit) / $staffTaxRate;
                                    ?>
                                    <tr>
                                        <td><?php echo $profile; ?> - cap</td>
                                        <td>
                                        <span class="pull-right">
                                            <?php echo $capHours; ?>
                                        </span>
                                        </td>
                                        <td>
                                        <span class="pull-right">
                                            <?php echo number_format(0, 2); ?>
                                        </span>
                                        </td>
                                        <td>
                                        <span class="pull-right">
                                            <?php echo number_format(0, 2); ?>
                                        </span>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            <tr>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <th>Total</th>
                                <th>
                                    <span class="pull-right">
                                        <?php echo $times['total']['staff']['total'][$staff]; ?>
                                    </span>
                                </th>
                                <th>
                                    <span class="pull-right">
                                        <?php echo '$' . number_format($cost ? $cost['total']['staff'][$staff] : 0, 2); ?>
                                    </span>
                                </th>
                                <th>
                                    <span class="pull-right">
                                        <?php echo '$' . number_format($profit ? $profit['total']['staff'][$staff] : 0, 2); ?>
                                    </span>
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
        <h3><i class="icon-briefcase"></i> Hours by Profile</h3>
        <table class="table">
            <?php
            foreach ($times['total']['profile'] as $profile => $staffs) {
                if ($profile == 'total') continue;
                ?>
                <tr>
                    <td width="40%"><?php echo $profile; ?></td>
                    <td width="60%">
                        <table class="table table-striped table-condensed">
                            <tr>
                                <th width="50%">staff</th>
                                <th width="25%">hours</th>
                                <th width="25%">cost&nbsp;<i class="icon-info-sign" title="Ex GST"></i></th>
                                <th width="25%">profit&nbsp;<i class="icon-info-sign" title="Ex GST"></i></th>
                            </tr>
                            <?php
                            foreach ($staffs as $staff => $hours) {
                                $staffTaxRate = $saasu->getStaffTaxRate($staff, $profile);
                                $staffCost = $saasu->getStaffCost($staff, $profile);
                                $staffProfit = $saasu->getStaffProfit($staff, $profile);
                                $capHours = isset($saasu->profiles[$profile]['capHours']) ? $saasu->profiles[$profile]['capHours'] : 0;
                                if ($capHours && $capHours < $hours) {
                                    $_ = $capHours;
                                    $capHours = $hours - $capHours;
                                    $hours = $_;
                                }
                                ?>
                                <tr>
                                    <td><?php echo $staff; ?></td>
                                    <td>
                                        <span class="pull-right"><?php echo $hours; ?></span>
                                    </td>
                                    <td>
                                        <span class="pull-right"><?php echo number_format(($hours * $staffCost) / $staffTaxRate, 2); ?></span>
                                    </td>
                                    <td>
                                        <span class="pull-right"><?php echo number_format(($hours * $staffProfit) / $staffTaxRate, 2); ?></span>
                                    </td>
                                </tr>
                                <?php
                                if ($capHours) {
                                    $cost['total']['profile'][$profile] -= ($capHours * $staffCost) / $staffTaxRate;
                                    $profit['total']['profile'][$profile] -= ($capHours * $staffProfit) / $staffTaxRate;
                                    ?>
                                    <tr>
                                        <td><?php echo $staff; ?> - cap</td>
                                        <td>
                                            <span class="pull-right"><?php echo $capHours; ?></span>
                                        </td>
                                        <td>
                                            <span class="pull-right"><?php echo number_format(0, 2); ?></span>
                                        </td>
                                        <td>
                                            <span class="pull-right"><?php echo number_format(0, 2); ?></span>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            <tr>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <th>Total</th>
                                <th>
                                    <span class="pull-right"><?php echo $times['total']['profile']['total'][$profile]; ?></span>
                                </th>
                                <th>
                                    <span class="pull-right"><?php echo '$' . number_format($cost['total']['profile'][$profile], 2); ?></span>
                                </th>
                                <th>
                                    <span class="pull-right"><?php echo '$' . number_format($profit['total']['profile'][$profile], 2); ?></span>
                                </th>
                            </tr>
                            <tr>
                                <th>
                                    <small>Invoice</small>
                                </th>
                                <th colspan="3">
                                    <span class="pull-right"><small><?php echo '$' . number_format($cost['total']['profile'][$profile] + $profit['total']['profile'][$profile], 2); ?></small></span>
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