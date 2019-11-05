<?php

$_lang['commerce_abandonedcart'] = 'Abandoned Cart';
$_lang['commerce_abandonedcarts'] = 'Abandoned Carts';
$_lang['commerce_abandonedcart.description'] = 'Automatically send messages to customers who have abandoned their cart.';

// Admin
$_lang['commerce_abandonedcart.converted_on_method'] = 'Converted On Method';
$_lang['commerce_abandonedcart.converted_on_method_desc'] = 'Configures when the abandoned cart is marked as converted. This may vary depending on what you sell with your shop and what payment methods you utilize.';
$_lang['commerce_abandonedcart.converted_on_method_thank_you'] = 'Thank You Step';
$_lang['commerce_abandonedcart.converted_on_method_state_cart_to_processing'] = 'Order Moved to Processing';

$_lang['commerce_abandonedcart.add_message'] = 'Add Scheduled Message';
$_lang['commerce_abandonedcart.update_message'] = 'Update Scheduled Message';
$_lang['commerce_abandonedcart.delete_message'] = 'Delete Scheduled Message';
$_lang['commerce_abandonedcart.schedule'] = 'Schedule';
$_lang['commerce_abandonedcart.none_scheduled'] = 'No messages are scheduled. Add a new scheduled message below!';
$_lang['commerce_abandonedcart.schedule_desc'] = 'Schedule when abandoned cart messages should be sent out. This is from the time the user enters an address.';
$_lang['commerce_abandonedcart.subject'] = 'Subject';
$_lang['commerce_abandonedcart.from'] = 'From';
$_lang['commerce_abandonedcart.subject_desc'] = 'Message subject the customer sees.';
$_lang['commerce_abandonedcart.send_time'] = 'Send Time';
$_lang['commerce_abandonedcart.send_time_desc'] = 'Enter in PHP strtotime format (ex. +1 day). Relative to when the abandoned cart was created.';
$_lang['commerce_abandonedcart.content'] = 'Content';
$_lang['commerce_abandonedcart.conditions'] = 'Conditions';
$_lang['commerce_abandonedcart.conditions_desc'] = 'Conditions that must be met for this message to send.';

$_lang['commerce_abandonedcart.subscription_status'] = 'Subscription Status';
$_lang['commerce_abandonedcart.all'] = 'All';
$_lang['commerce_abandonedcart.subscribed'] = 'Subscribed';
$_lang['commerce_abandonedcart.unsubscribed'] = 'Unsubscribed';
$_lang['commerce_abandonedcart.email'] = 'Email';
$_lang['commerce_abandonedcart.customer_type'] = 'Customer Type';
$_lang['commerce_abandonedcart.guest'] = 'Guest';
$_lang['commerce_abandonedcart.registered'] = 'Registered';
$_lang['commerce_abandonedcart.customer'] = 'Customer';
$_lang['commerce_abandonedcart.unknown_customer'] = 'Unknown Customer';
$_lang['commerce_abandonedcart.customer_id'] = 'Customer ID';
$_lang['commerce_abandonedcart.customers'] = 'Customers';
$_lang['commerce_abandonedcart.carts'] = 'Carts';
$_lang['commerce_abandonedcart.search_by_customer'] = 'Search by Customer';
$_lang['commerce_abandonedcart.search_by_email'] = 'Search by Email';
$_lang['commerce_abandonedcart.msg_count'] = 'Message Count';
$_lang['commerce_abandonedcart.converted'] = 'Converted';
$_lang['commerce_abandonedcart.not_converted'] = 'Not Converted';
$_lang['commerce_abandonedcart.converted_on'] = 'Converted On';
$_lang['commerce_abandonedcart.added_on'] = 'Added On';
$_lang['commerce_abandonedcart.delete_abandoned_cart'] = 'Delete Abandoned Cart';
$_lang['commerce_abandonedcart.update_customer'] = 'Update Customer';
$_lang['commerce_abandonedcart.delete_customer'] = 'Delete Customer';

// Validation
$_lang['commerce_abandonedcart.email_invalid'] = 'Email entered is not a valid email address.';
$_lang['commerce_abandonedcart.send_time_invalid'] = 'Invalid send time. Enter in PHP strtotime format.';

// Logs
$_lang['commerce_abandonedcart.added_abandonedcart'] = 'Added abandoned cart with ID [[+id]].';
$_lang['commerce_abandonedcart.converted_abandonedcart'] = 'Converted abandoned cart with ID [[+id]].';

// Reports
$_lang['commerce_abandonedcart.user_report'] = 'Abandoned Cart Customers';
$_lang['commerce_abandonedcart.user_report_desc'] = 'Export a list of abandoned cart customers.';

// Templates
$_lang['commerce_abandonedcart.unsubscribed_from_emails'] = 'You have been unsubscribed from Abandoned Cart emails.';
$_lang['commerce_abandonedcart.not_found'] = 'Email address entered was not found.';
$_lang['commerce_abandonedcart.already_unsubscribed'] = 'Email address entered has already been unsubscribed.';