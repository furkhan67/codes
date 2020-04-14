<?php
// set the error level
error_reporting(E_ALL);

// Define the Outer path to the Zamp Folder
define('_PROJECT_PATH_', __DIR__.'/');

// Define the Application path
define('APP_PATH', _PROJECT_PATH_.'application/');

/**
 *    default timezone for direct calling of date() like functions
 *    You're advised to avoid using date() or time() functions in your framework projects, instead use
 *    Zamp_SystemCore->systemTime() method
 *
 *    NOTE: calling Zamp_SystemCore->systemTime() based on GMT time
 */
date_default_timezone_set('GMT');

/**
 *    you can add more applications under your application directory.
 *    array index is the URL. "*" means all URL
 *    array value is application folder name
 *    for example: I've 2 applications called blog and news, so i can define my Application as
 *    $config['applications']['blog'] = 'blog'; -> means if the URL start with 'blog' then the blog application (APP_PATH.'blog') will be loaded.
 *    $config['applications']['*'] = 'news'; -> means for all URL (except defined - here 'blog') the news application (APP_PATH.'news') will be loaded.
 *    Explanation:
 *    http://localhost/blog/add   -> AddController.php inside the blog application called.
 *    http://localhost/add        -> AddController.php inside the news application called.
 */
$config['applications'] = [
	'*' => ''
];

/**
 *    Routing configurations
 *
 *    You can route any URL by regular expression. key MUST be regular expression and value may contain matched part
 *    NOTE: #1# equals to $1 or \\1
 *
 *    Example:
 *        $config['router'] = [
 *            '/^login/i' => 'Member/login',
 *            '#^products/(\d+)/.*#i' => 'product/view/#1#',
 *        ];
 *
 *    for 1st condition if the URL is `http://your-application.com/login` then
 *    Zamp considered as `http://your-application.com/Member/login`
 *
 *    for 2nd condition if the URL is `http://your-application.com/products/123/this_is_my_product_name` then
 *    Zamp considered as `http://your-application.com/product/view/123`
 *
 *    If the URL routed based on your conditions then you can check route information
 *    from `$this->_params` array which is Zamp_SystemCore property
 */
$config['router'] = [];

/**
 *    If you set $config['isDevelopmentPhase'] = false then the follwing settings will be used
 *    If you set send_email_alert = true in the following settings then you can use Zamp_Mailer::sendMail() options
 *    example: If you want to add cc in error alert email use
 *    $config['errorHandler']['cc_email'] = ['cc_email@domain.com' => 'cc person name']; // cc_email option used in Zamp_Mailer::sendMail()
 */
$config['errorHandler'] = [
	'save_error_into_folder' => _PROJECT_PATH_.'app_errors',
	'error_file_format' => 'd-m-Y--h-i-s-a',
	'error_time_diff_from_gmt' => 19800,
	'send_email_alert' => true,
    'interval_for_same_error_realert' => 300,
	'from_address' => [
		'name' => 'ZampPHP System',
		'email' => 'noreply@zampphp.org',
	],
	'to_address' => [
		'phpmathan@gmail.com' => 'Mathan Kumar R'
	],
	'subject' => 'ZampPHP - Application Error',
	'message' => 'Hi, Your Application received some errors. Immediately look it out.',
];

/** End of File **/