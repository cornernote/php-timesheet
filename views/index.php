<?php
$gs = new GrindStone(config('GrindStone'));
$saasu = new Saasu(config('Saasu'));
$times = $gs->getTimes();
$profit = $saasu->getProfits($times);
$cost = $saasu->getCosts($times);
$invoices = $saasu->getInvoices();
?>

<h1>Time Sheet</h1>

<script>
    $(document).ready(function () {
        $('#main-tab a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        })
    });
</script>

<ul id="main-tab" class="nav nav-pills">
    <li class="active"><a href="#summary">Summary</a></li>
    <li><a href="#daily">Daily</a></li>
    <li><a href="#invoice">Invoice</a></li>
</ul>

<div class="tab-content">

    <div id="summary" class="tab-pane active">
        <?php
        render('_index_summary', array(
            'profit' => $profit,
            'cost' => $cost,
            'times' => $times,
            'saasu' => $saasu,
        ));
        ?>
    </div>

    <div id="daily" class="tab-pane">
        <?php
        render('_index_daily', array(
            'profit' => $profit,
            'cost' => $cost,
            'times' => $times,
            'saasu' => $saasu,
        ));
        ?>
    </div>

    <div id="invoice" class="tab-pane">
        <?php
        render('_index_invoice', array(
            'times' => $times,
            'saasu' => $saasu,
            'invoices' => $invoices,
        ));
        ?>
    </div>

</div>
