<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php bloginfo(); ?></title>
    <?php wp_head(); ?>
</head>
<body>

<header>
    <?php
    wp_nav_menu( 'primary_menu' );
    ?>
</header>
<div class="main-content">