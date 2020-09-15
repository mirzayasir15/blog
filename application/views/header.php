<!DOCTYPE HTML>
<html>

<head>
    <title>My Blog</title>
    <meta name="description" content="website description" />
    <meta name="keywords" content="website keywords, website keywords" />
    <meta http-equiv="content-type" content="text/html; charset=windows-1252" />
    <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Tangerine&amp;v1" />
    <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz" />
    <link rel="stylesheet" type="text/css" href="<?= base_url()?>public/css/style.css" />
    <link rel="stylesheet" type="text/css" href="<?= base_url()?>public/css/bootstrap-theme.min.css" />
    <link rel="stylesheet" type="text/css" href="<?= base_url()?>public/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <script src="<?=  base_url()?>public/js/jquery-2.1.1.min.js"></script>
</head>

<body>
    <div id="main">
        <div id="header">
            <div id="logo">
                <h1><a href="<?=base_url()?>blog/">My Blog</a></h1>
                <div class="slogan">Machines was born to be guided!</div>
            </div>
            <div id="menubar">
                <ul id="menu">
                  <!-- put class="current" in the li tag for the selected page - to highlight which page you're on -->
                    <li class="<?=$home_class;?>" ><a href="<?=base_url()?>blog/">Home</a></li>
                    <?php if($this->session->userdata('user_id')) {?>
                        <li class="<?=$login_class;?>" ><a href="<?=  base_url()?>users/logout">(<?=$this->session->userdata['username']?>)Logout</a></li>
                    <?php } else { ?>
                        <li class="<?=$login_class;?>" ><a href="<?=  base_url()?>users/login">Login</a></li>
                    <?php } ?>
                    <?php if(true) { ?>
                        <li class="<?=$all_users;?>" ><a href="<?=  base_url()?>users/all">Users</a></li>
                    <?php } ?>
                    <li class="<?=$register_class;?>" ><a href="<?=  base_url()?>users/register/">Register</a></li>
<!--                    <li class="<?=$upload_class;?>" ><a href="<?=  base_url()?>upload/">Upload Photo</a></li>-->
                    <li class="<?=$contact_class;?>" ><a href="<?=  base_url()?>pages/contact">Contact Us</a></li>
                </ul>
            </div>
        </div>