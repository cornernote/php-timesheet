<h2>Invoices</h2>

<script>
    $(document).ready(function () {
        $('#invoice-tab a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        })
    });
</script>

<ul id="invoice-tab" class="nav nav-pills">
    <?php
    $active = 'active';
    foreach ($invoices as $k => $invoice) {
        ?>
        <li class="<?php echo $active; ?>">
            <a href="#invoice-<?php echo $k; ?>"><?php echo $invoice['profile_name']; ?></a>
        </li>
        <?php
        $active = '';
    }
    ?>
</ul>
<div class="tab-content">
    <?php
    $active = 'active';
    foreach ($invoices as $k => $invoice) {
        ?>
        <div id="invoice-<?php echo $k; ?>" class="tab-pane <?php echo $active; ?>">
            <h3><?php echo $invoice['profile_name']; ?></h3>
            <table class="table">
                <tr>
                    <td width="15%">To Email Address</td>
                    <td width="85%"><?php echo $invoice['to_email_address']; ?></td>
                </tr>
                <tr>
                    <td>Saasu Contact UID</td>
                    <td><?php echo $invoice['saasu_contact_uid']; ?></td>
                </tr>
                <tr>
                    <td>Items</td>
                    <td>
                        <table class="table table-bordered table-striped table-condensed">
                            <tr>
                                <th width="70%">description</th>
                                <th width="10%">quantity</th>
                                <th width="10%">amount</th>
                                <th width="10%">total</th>
                                <th width="10%">total ex</th>
                            </tr>
                            <?php
                            foreach ($invoice['items'] as $invoiceItem) {
                                ?>
                                <tr>
                                    <td><?php echo $invoiceItem['description']; ?></td>
                                    <td>
                                        <span class="pull-right"><?php echo round($invoiceItem['quantity'], 2); ?></span>
                                    </td>
                                    <td><span class="pull-right"><?php echo $invoiceItem['amount']; ?></span></td>
                                    <td>
                                        <span class="pull-right"><?php echo '$' . number_format($invoiceItem['quantity'] * $invoiceItem['amount'], 2); ?></span>
                                    </td>
                                    <td>
                                        <span class="pull-right"><?php echo '$' . number_format($invoiceItem['quantity'] * $invoiceItem['amount'] / 1.1, 2); ?></span>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>Times</td>
                    <td>
                        <table class="table table-bordered table-striped table-condensed">
                            <tr>
                                <th width="20%">staff</th>
                                <th width="80%">times</th>
                            </tr>
                            <?php
                            foreach ($invoice['times'] as $staffProject => $invoiceTimes) {
                                $staffProject = explode('|', $staffProject);
                                ?>
                                <tr>
                                    <td><?php echo $staffProject[0] . ' @' . $staffProject[1]; ?></td>
                                    <td>
                                        <?php
                                        foreach ($invoiceTimes as $date => $invoiceTimeList) {
                                            $dayTotal = 0;
                                            $dayTasks = array();
                                            ob_start();
                                            ?>
                                            <ul>
                                                <?php
                                                foreach ($invoiceTimeList as $task => $time) {
                                                    $dayTotal += $time;
                                                    $dayTasks[] = htmlspecialchars($task);
                                                    echo '<li>' . Helper::formatHours($time) . ' - ' . $task . '</li>';
                                                }
                                                ?>
                                            </ul>
                                            <?php
                                            $content = ob_get_clean();
                                            echo '<b>' . $date . '</b> <small>' . Helper::formatHours($dayTotal) . '</small>' . $content . '<br/>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <?php
        $active = '';
    }
    ?>
</div>
