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
    foreach ($invoices as $contactId => $profiles) {
        foreach ($profiles as $profileName => $invoice) {
            ?>
            <li class="<?php echo $active; ?>">
                <a href="#invoice-<?php echo $contactId; ?>-<?php echo md5($profileName); ?>"><?php echo $invoice['profile_name']; ?></a>
            </li>
            <?php
            $active = '';
        }
    }
    ?>
</ul>
<div class="tab-content">
    <?php
    $active = 'active';
    foreach ($invoices as $contactId => $profiles) {
        foreach ($profiles as $profileName => $invoice) {
            ?>
            <div id="invoice-<?php echo $contactId; ?>-<?php echo md5($profileName); ?>" class="tab-pane <?php echo $active; ?>">
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
                                $total = array('quantity' => 0, 'amount' => 0);
                                foreach ($invoice['items'] as $invoiceItem) {
                                    $total['quantity'] += $invoiceItem['quantity'];
                                    $total['amount'] += $invoiceItem['quantity'] * $invoiceItem['amount'];
                                    ?>
                                    <tr>
                                        <td><?php echo str_replace("\n", '<br/>', $invoiceItem['description']); ?></td>
                                        <td>
                                            <span class="pull-right"><?php echo number_format($invoiceItem['quantity'], 2); ?></span>
                                        </td>
                                        <td><span class="pull-right"><?php echo $invoiceItem['amount']; ?></span>
                                        </td>
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
                                <tr>
                                    <td>Total</td>
                                    <td>
                                        <span class="pull-right"><?php echo number_format($total['quantity'], 2); ?></span>
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                        <span class="pull-right"><?php echo '$' . number_format($total['amount'], 2); ?></span>
                                    </td>
                                    <td>
                                        <span class="pull-right"><?php echo '$' . number_format($total['amount'] / 1.1, 2); ?></span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>Times</td>
                        <td>
                            <pre><?php render('email/_times', array('times' => $invoice['times'])); ?></pre>
                        </td>
                    </tr>
                </table>
            </div>
            <?php
            $active = '';
        }
    }
    ?>
</div>
