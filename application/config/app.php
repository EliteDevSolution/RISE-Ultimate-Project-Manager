<?php

defined('BASEPATH') OR exit('No direct script access allowed');

//don't change or add new config in this file

$config['app_version'] = '2.6.1';

$config['app_update_url'] = 'https://releases.fairsketch.com/rise/';

$config['updates_path'] = './updates/';

$config['app_csrf_exclude_uris'] = array(
    "notification_processor/create_notification",
    "paypal_ipn", "paypal_ipn/index",
    "paytm_redirect", "paytm_redirect/index", "paytm_redirect.*+",
    "stripe_redirect", "stripe_redirect/index",
    "pay_invoice", "pay_invoice/index",
    "google_api/save_access_token", "google_api/save_access_token_of_calendar",
    "webhooks_listener.*+",
    "external_tickets.*+",
    "cron"
);
