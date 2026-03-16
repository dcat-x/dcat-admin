<?php

declare(strict_types=1);
use App\Admin\Controllers\AuthController;
use Dcat\Admin\Exception\Handler;
use Dcat\Admin\Grid\Actions\Delete;
use Dcat\Admin\Grid\Actions\Edit;
use Dcat\Admin\Grid\Actions\QuickEdit;
use Dcat\Admin\Grid\Actions\Show;
use Dcat\Admin\Grid\ColumnSelector\SessionStore;
use Dcat\Admin\Grid\Displayers\DropdownActions;
use Dcat\Admin\Grid\Tools\BatchActions;
use Dcat\Admin\Grid\Tools\BatchDelete;
use Dcat\Admin\Grid\Tools\Paginator;
use Dcat\Admin\Models\Administrator;
use Dcat\Admin\Models\DataRule;
use Dcat\Admin\Models\Department;
use Dcat\Admin\Models\Menu;
use Dcat\Admin\Models\Permission;
use Dcat\Admin\Models\Role;

return [

    /*
    |--------------------------------------------------------------------------
    | dcat-admin name
    |--------------------------------------------------------------------------
    |
    | This value is the name of dcat-admin, This setting is displayed on the
    | login page.
    |
    */
    'name' => 'Dcat Admin',

    /*
    |--------------------------------------------------------------------------
    | dcat-admin logo
    |--------------------------------------------------------------------------
    |
    | The logo of all admin pages. You can also set it as an image by using a
    | `img` tag, eg '<img src="http://logo-url" alt="Admin logo">'.
    |
    */
    'logo' => '<img src="/vendor/dcat-admin/images/logo.png" width="35"> &nbsp;Dcat Admin',

    /*
    |--------------------------------------------------------------------------
    | dcat-admin mini logo
    |--------------------------------------------------------------------------
    |
    | The logo of all admin pages when the sidebar menu is collapsed. You can
    | also set it as an image by using a `img` tag, eg
    | '<img src="http://logo-url" alt="Admin logo">'.
    |
    */
    'logo-mini' => '<img src="/vendor/dcat-admin/images/logo.png">',

    /*
    |--------------------------------------------------------------------------
    | dcat-admin favicon
    |--------------------------------------------------------------------------
    |
    */
    'favicon' => null,

    /*
     |--------------------------------------------------------------------------
     | User default avatar
     |--------------------------------------------------------------------------
     |
     | Set a default avatar for newly created users.
     |
     */
    'default_avatar' => '@admin/images/default-avatar.jpg',

    /*
    |--------------------------------------------------------------------------
    | dcat-admin route settings
    |--------------------------------------------------------------------------
    |
    | The routing configuration of the admin page, including the path prefix,
    | the controller namespace, and the default middleware. If you want to
    | access through the root path, just set the prefix to empty string.
    |
    */
    'route' => [
        'domain' => env('ADMIN_ROUTE_DOMAIN'),

        'prefix' => env('ADMIN_ROUTE_PREFIX', 'admin'),

        'namespace' => 'App\\Admin\\Controllers',

        'middleware' => ['web', 'admin'],

        'enable_session_middleware' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | dcat-admin install directory
    |--------------------------------------------------------------------------
    |
    | The installation directory of the controller and routing configuration
    | files of the administration page. The default is `app/Admin`, which must
    | be set before running `artisan admin::install` to take effect.
    |
    */
    'directory' => app_path('Admin'),

    /*
    |--------------------------------------------------------------------------
    | dcat-admin html title
    |--------------------------------------------------------------------------
    |
    | Html title for all pages.
    |
    */
    'title' => 'Admin',

    /*
    |--------------------------------------------------------------------------
    | Assets hostname
    |--------------------------------------------------------------------------
    |
   */
    'assets_server' => env('ADMIN_ASSETS_SERVER'),

    /*
    |--------------------------------------------------------------------------
    | Access via `https`
    |--------------------------------------------------------------------------
    |
    | If your page is going to be accessed via https, set it to `true`.
    |
    */
    'https' => env('ADMIN_HTTPS', false),

    /*
    |--------------------------------------------------------------------------
    | dcat-admin auth setting
    |--------------------------------------------------------------------------
    |
    | Authentication settings for all admin pages. Include an authentication
    | guard and a user provider setting of authentication driver.
    |
    | You can specify a controller for `login` `logout` and other auth routes.
    |
    */
    'auth' => [
        'enable' => true,

        'controller' => AuthController::class,

        'guard' => 'admin',

        'guards' => [
            'admin' => [
                'driver' => 'session',
                'provider' => 'admin',
            ],
        ],

        'providers' => [
            'admin' => [
                'driver' => 'eloquent',
                'model' => Administrator::class,
            ],
        ],

        // Add "remember me" to login form
        'remember' => true,

        // All method to path like: auth/users/*/edit
        // or specific method to path like: get:auth/users.
        'except' => [
            'auth/login',
            'auth/logout',
        ],

        'enable_session_middleware' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | The global Grid setting
    |--------------------------------------------------------------------------
    */
    'grid' => [

        // The global Grid action display class.
        'grid_action_class' => DropdownActions::class,

        // The global Grid batch action display class.
        'batch_action_class' => BatchActions::class,

        // The global Grid pagination display class.
        'paginator_class' => Paginator::class,

        'actions' => [
            'view' => Show::class,
            'edit' => Edit::class,
            'quick_edit' => QuickEdit::class,
            'delete' => Delete::class,
            'batch_delete' => BatchDelete::class,
        ],

        // The global Grid column selector setting.
        'column_selector' => [
            'store' => SessionStore::class,
            'store_params' => [
                'driver' => 'file',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | dcat-admin helpers setting.
    |--------------------------------------------------------------------------
    */
    'helpers' => [
        'enable' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | dcat-admin permission setting
    |--------------------------------------------------------------------------
    |
    | Permission settings for all admin pages.
    |
    */
    'permission' => [
        // Whether enable permission.
        'enable' => true,

        // All method to path like: auth/users/*/edit
        // or specific method to path like: get:auth/users.
        'except' => [
            '/',
            'auth/login',
            'auth/logout',
            'auth/setting',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | dcat-admin menu setting
    |--------------------------------------------------------------------------
    |
    */
    'menu' => [
        'cache' => [
            // enable cache or not
            'enable' => env('ADMIN_MENU_CACHE', false),
            'store' => 'file',
        ],

        // Whether enable menu bind to a permission.
        'bind_permission' => true,

        // Whether enable role bind to menu.
        'role_bind_menu' => true,

        // Whether enable permission bind to menu.
        'permission_bind_menu' => true,

        'default_icon' => 'feather icon-circle',
    ],

    /*
    |--------------------------------------------------------------------------
    | dcat-admin department setting
    |--------------------------------------------------------------------------
    |
    */
    'department' => [
        // Whether enable department feature
        // Set to true only if you have run the department migration
        'enable' => false,

        // Whether user can belong to multiple departments
        'user_multi_department' => true,

        // Whether user inherits roles from department
        'inherit_department_roles' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | dcat-admin data permission setting
    |--------------------------------------------------------------------------
    |
    */
    'data_permission' => [
        // Whether enable data permission
        // Set to true only if you have run the data permission migration
        'enable' => false,

        // System variables
        'variables' => [
            'user_id',
            'department_id',
            'department_path',
            'department_ids',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | dcat-admin upload setting
    |--------------------------------------------------------------------------
    |
    | File system configuration for form upload files and images, including
    | disk and upload path.
    |
    */
    'upload' => [

        // Disk in `config/filesystem.php`.
        'disk' => 'public',

        // Image and file upload path under the disk above.
        'directory' => [
            'image' => 'images',
            'file' => 'files',
        ],

        /*
        |--------------------------------------------------------------------------
        | Aliyun OSS Direct Upload Settings (optional)
        |--------------------------------------------------------------------------
        |
        | Configuration for OSS direct upload feature.
        | Required package: composer require alibabacloud/sts-20150401
        |
        | You can also configure these in config/filesystems.php under 'disks.oss'
        |
        */
        'oss' => [
            // Enable OSS direct upload feature
            'enable' => env('ADMIN_OSS_ENABLE', false),

            // OSS bucket name
            'bucket' => env('OSS_BUCKET'),

            // OSS endpoint (e.g., oss-cn-hangzhou.aliyuncs.com)
            'endpoint' => env('OSS_ENDPOINT'),

            // CDN domain for file access (optional)
            'cdn_domain' => env('OSS_CDN_DOMAIN'),

            // Private bucket disk name for signed URL generation
            'private_disk' => env('OSS_PRIVATE_DISK', 'oss-private'),

            // Signed URL expiration time in minutes
            'signed_url_expire' => env('OSS_SIGNED_URL_EXPIRE', 60),

            // Allowed upload directories (for security)
            'allowed_directories' => [
                'files',
                'images',
                'documents',
                'videos',
                'apk',
                'app',
            ],

            // STS (Security Token Service) configuration
            'sts' => [
                'access_key_id' => env('OSS_ACCESS_KEY_ID'),
                'access_key_secret' => env('OSS_ACCESS_KEY_SECRET'),
                'role_arn' => env('OSS_STS_ROLE_ARN'),
                'region_id' => env('OSS_STS_REGION_ID', 'cn-hangzhou'),
                'duration' => env('OSS_STS_DURATION', 3600),
                'policy' => env('OSS_STS_POLICY'),
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | dcat-admin database settings
    |--------------------------------------------------------------------------
    |
    | Here are database settings for dcat-admin builtin model & tables.
    |
    */
    'database' => [

        // Database connection for following tables.
        'connection' => '',

        // User tables and model.
        'users_table' => 'admin_users',
        'users_model' => Administrator::class,

        // Role table and model.
        'roles_table' => 'admin_roles',
        'roles_model' => Role::class,

        // Permission table and model.
        'permissions_table' => 'admin_permissions',
        'permissions_model' => Permission::class,

        // Menu table and model.
        'menu_table' => 'admin_menu',
        'menu_model' => Menu::class,

        // Pivot table for table above.
        'role_users_table' => 'admin_role_users',
        'role_permissions_table' => 'admin_role_permissions',
        'role_menu_table' => 'admin_role_menu',
        'permission_menu_table' => 'admin_permission_menu',
        'settings_table' => 'admin_settings',
        'extensions_table' => 'admin_extensions',
        'extension_histories_table' => 'admin_extension_histories',

        // Department tables
        'departments_table' => 'admin_departments',
        'departments_model' => Department::class,
        'department_users_table' => 'admin_department_users',
        'department_roles_table' => 'admin_department_roles',

        // Data permission tables
        'data_rules_table' => 'admin_data_rules',
        'data_rules_model' => DataRule::class,
        'role_data_rules_table' => 'admin_role_data_rules',
    ],

    /*
    |--------------------------------------------------------------------------
    | Application layout
    |--------------------------------------------------------------------------
    |
    | This value is the layout of admin pages.
    */
    'layout' => [
        // default, blue, blue-light
        // Tailwind CSS: slate, gray, zinc, neutral, stone, red, orange, amber, yellow,
        //               lime, green, emerald, teal, cyan, sky, indigo, violet, purple, fuchsia, pink, rose
        'color' => 'gray',

        // sidebar-separate
        'body_class' => [],

        'horizontal_menu' => false,

        'sidebar_collapsed' => false,

        // light, primary, dark
        'sidebar_style' => 'light',

        'dark_mode_switch' => false,

        // bg-primary, bg-info, bg-warning, bg-success, bg-danger, bg-dark
        'navbar_color' => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | The exception handler class
    |--------------------------------------------------------------------------
    |
    */
    'exception_handler' => Handler::class,

    /*
    |--------------------------------------------------------------------------
    | Log emission control
    |--------------------------------------------------------------------------
    |
    | Used by audit/permission/data-permission/config-health logging enhancements.
    | `sample_rate` range: 0 ~ 1
    |
    */
    'log_control' => [
        'audit' => [
            'sample_rate' => 1.0,
            'only_paths' => [],
            'except_paths' => [],
        ],
        'permission_denied' => [
            'sample_rate' => 1.0,
            'only_paths' => [],
            'except_paths' => [],
        ],
        'data_permission' => [
            'sample_rate' => 1.0,
            'only_paths' => [],
            'except_paths' => [],
        ],
        'config_health' => [
            'sample_rate' => 1.0,
            'only_paths' => [],
            'except_paths' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Health check settings
    |--------------------------------------------------------------------------
    |
    */
    'health_check' => [
        // Cache TTL in seconds. Set 0 to disable cache.
        'cache_ttl' => env('ADMIN_HEALTH_CHECK_CACHE_TTL', 0),
    ],

    /*
    |--------------------------------------------------------------------------
    | Enable default breadcrumb
    |--------------------------------------------------------------------------
    |
    | Whether enable default breadcrumb for every page content.
    */
    'enable_default_breadcrumb' => false,

    /*
    |--------------------------------------------------------------------------
    | Extension
    |--------------------------------------------------------------------------
    */
    'extension' => [
        // When you use command `php artisan admin:ext-make` to generate extensions,
        // the extension files will be generated in this directory.
        'dir' => base_path('dcat-admin-extensions'),
    ],
];
