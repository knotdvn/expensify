<?php
// Single page authenticator and data manipulator for:
// Expensify Programming Challenge ?>
<!DOCTYPE html>
<html class="no-js">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Place favicon.ico and apple-touch-icon(s) in the root directory -->

        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/dvn.css">
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
        <script src="js/vendor/modernizr-2.7.1.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->

        <div class="ribbon" id="auth">
            <div class="wrap">
                <h1>Please Authenticate</h1>
                <form id="expensify_credentials">
                <input id="email_address" type="email"  placeholder="Just your email address">
                <input id="password" type="password"  placeholder="Just your password">
                <div id="authenticator" class="button"><p>Authenticate</p></div>
                </form>
            </div>
        </div>

        <div class="ribbon" id="transaction">
            <div class="wrap">
                <h1>Transaction List</h1>
                <p>Only the 10 "newest" transactions are shown.</p>
                <h2 id="loading">Loading Data</h2>

            <ul id="list_label">
                <li>
                    <span class="date">Date</span><span class="amount">Amount</span><span class="merchant">Merchant</span>
                </li>
            </ul>
            <ul id="transaction_list"></ul>

            <div id="create">
                <h1>Create Transaction Form:</h1>
                <input type="text" id="datepicker" size="30" placeholder="Date">
                <input type="text" id="amount_input" size="10" placeholder="Amount" />
                <input type="text" id="merchant_input" size="30" placeholder="Merchant" />
                <div>
                    <div class="button" id="create_button">Create Transaction</div>
                    <div class="button" id="refresh_button">Refresh List</div>
                    <div class="button" id="reauthenticate_button">ReAuthenticate</div>
                </div>
            </div>



            </div>
        </div>
       

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jq.min.js"><\/script>')</script>
         <!-- https://github.com/carhartl/jquery-cookie-->
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <script src="js/vendor/jquery.cookie.js"></script>
        <script src="js/dvn.js"></script>

     
    </body>
</html>
