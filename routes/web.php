<?php

use Illuminate\Support\Facades\Route;

//Edyone Website (must be before admin — avoids {organization} wildcard swallowing /web/* routes)
require __DIR__.'/website.php';

// Super Admin
require __DIR__.'/super-admin.php';

//Admin School
require __DIR__.'/admin.php';

//Accounts Panel
require __DIR__.'/accounts.php';



