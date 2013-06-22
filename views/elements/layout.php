<?php
/**
 * @var $header_title
 * @var $header_tags
 * @var $content
 */

// set variable defaults
if (empty($page_title)) $page_title = 'Time Sheet';
if (empty($header_title)) $header_title = $page_title;
if (empty($header_tags)) $header_tags = '';
if (empty($content)) $content = '';

// set a random background for 5 minutes
if (!isset($_SESSION['background']) || $_SESSION['background']['ttl'] < time()) {
    $_SESSION['background'] = array(
        'rand' => rand(1, 8),
        'ttl' => time() + 300,
    );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $header_title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?php echo bu(); ?>/img/invoice_16.png" type="image/png">

    <!-- styles -->
    <link href="<?php echo bu(); ?>/vendors/bootstrap/css/bootstrap.css" rel="stylesheet">
    <style>
        body {
            padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
        }
    </style>
    <link href="<?php echo bu(); ?>/vendors/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="<?php echo bu(); ?>/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="<?php echo bu(); ?>/vendors/bootstrap/js/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo bu(); ?>/vendors/bootstrap/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo bu(); ?>/vendors/bootstrap/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo bu(); ?>/vendors/bootstrap/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="<?php echo bu(); ?>/vendors/bootstrap/ico/apple-touch-icon-57-precomposed.png">
    <link rel="shortcut icon" href="<?php echo bu(); ?>/vendors/bootstrap/ico/favicon.png">

    <script src="<?php echo bu(); ?>/vendors/bootstrap/js/jquery-1.10.1.min.js"></script>
    <script src="<?php echo bu(); ?>/vendors/bootstrap/js/bootstrap.min.js"></script>

    <?php echo $header_tags; ?>
</head>

<body>

<?php render('elements/menu'); ?>

<div class="container">

    <?php echo $content; ?>

</div>
</body>
</html>