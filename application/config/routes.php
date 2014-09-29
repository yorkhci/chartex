<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['chartex/results'] = 'chartex/results';
$route['chartex/results/(:any)'] = 'chartex/results/$1';
$route['chartex/document'] = 'chartex/document';
$route['chartex/document/(:any)'] = 'chartex/document/$1';
$route['chartex/entity'] = 'chartex/entity';
$route['chartex/entity/(:any)'] = 'chartex/entity/$1';
$route['chartex/entity/(:any)/(:any)'] = 'chartex/entity/$1/$2';
$route['chartex/entity/(:any)/(:any)/(:any)'] = 'chartex/entity/$1/$2/$3';
$route['chartex/tooltip'] = 'chartex/tooltip';
$route['chartex/tooltip/(:any)'] = 'chartex/tooltip/$1';
$route['chartex/help'] = 'chartex/help';
$route['chartex/help/(:any)'] = 'chartex/help/$1';
$route['chartex'] = 'chartex';
$route['(:any)'] = 'chartex';
$route['default_controller'] = 'chartex';

/* End of file routes.php */
/* Location: ./application/config/routes.php */