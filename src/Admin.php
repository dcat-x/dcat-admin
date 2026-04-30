<?php

declare(strict_types=1);

namespace Dcat\Admin;

use Closure;
use Composer\Autoload\ClassLoader;
use Dcat\Admin\Contracts\ExceptionHandler;
use Dcat\Admin\Contracts\Repository;
use Dcat\Admin\Contracts\Resettable;
use Dcat\Admin\Exception\InvalidArgumentException;
use Dcat\Admin\Extend\Manager;
use Dcat\Admin\Extend\ServiceProvider;
use Dcat\Admin\Http\Controllers\AuthController;
use Dcat\Admin\Http\Controllers\DataRuleController;
use Dcat\Admin\Http\Controllers\DepartmentController;
use Dcat\Admin\Http\Controllers\EditorMDController;
use Dcat\Admin\Http\Controllers\ExtensionController;
use Dcat\Admin\Http\Controllers\HandleActionController;
use Dcat\Admin\Http\Controllers\HandleFormController;
use Dcat\Admin\Http\Controllers\IconController;
use Dcat\Admin\Http\Controllers\ImportController;
use Dcat\Admin\Http\Controllers\MenuController;
use Dcat\Admin\Http\Controllers\OssController;
use Dcat\Admin\Http\Controllers\PermissionController;
use Dcat\Admin\Http\Controllers\RenderableController;
use Dcat\Admin\Http\Controllers\RoleController;
use Dcat\Admin\Http\Controllers\ScaffoldController;
use Dcat\Admin\Http\Controllers\TinymceController;
use Dcat\Admin\Http\Controllers\UserController;
use Dcat\Admin\Http\Controllers\ValueController;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Layout\Menu;
use Dcat\Admin\Layout\Navbar;
use Dcat\Admin\Layout\SectionManager;
use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\Admin\Support\Composer;
use Dcat\Admin\Support\Context;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Support\Setting;
use Dcat\Admin\Support\Translator;
use Dcat\Admin\Traits\HasAssets;
use Dcat\Admin\Traits\HasHtml;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Symfony\Component\HttpFoundation\Response;

class Admin implements Resettable
{
    use HasAssets;
    use HasHtml;

    const VERSION = '1.2.1';

    const SECTION = [
        // 往 <head> 标签内输入内容
        'HEAD' => 'ADMIN_HEAD',

        // 往body标签内部输入内容
        'BODY_INNER_BEFORE' => 'ADMIN_BODY_INNER_BEFORE',
        'BODY_INNER_AFTER' => 'ADMIN_BODY_INNER_AFTER',

        // 往#app内部输入内容
        'APP_INNER_BEFORE' => 'ADMIN_APP_INNER_BEFORE',
        'APP_INNER_AFTER' => 'ADMIN_APP_INNER_AFTER',

        // 顶部导航栏用户面板
        'NAVBAR_USER_PANEL' => 'ADMIN_NAVBAR_USER_PANEL',
        'NAVBAR_AFTER_USER_PANEL' => 'ADMIN_NAVBAR_AFTER_USER_PANEL',
        // 顶部导航栏之前
        'NAVBAR_BEFORE' => 'ADMIN_NAVBAR_BEFORE',
        // 顶部导航栏底下
        'NAVBAR_AFTER' => 'ADMIN_NAVBAR_AFTER',

        // 侧边栏顶部用户信息面板
        'LEFT_SIDEBAR_USER_PANEL' => 'ADMIN_LEFT_SIDEBAR_USER_PANEL',
        // 菜单栏
        'LEFT_SIDEBAR_MENU' => 'ADMIN_LEFT_SIDEBAR_MENU',
        // 菜单栏顶部
        'LEFT_SIDEBAR_MENU_TOP' => 'ADMIN_LEFT_SIDEBAR_MENU_TOP',
        // 菜单栏底部
        'LEFT_SIDEBAR_MENU_BOTTOM' => 'ADMIN_LEFT_SIDEBAR_MENU_BOTTOM',
    ];

    private static $defaultPjaxContainerId = 'pjax-container';

    /**
     * @var Widgets\GlobalSearch|null
     */
    protected static $globalSearch;

    /**
     * 版本.
     *
     * @return string
     */
    public static function longVersion()
    {
        return sprintf('Dcat Admin <comment>version</comment> <info>%s</info>', static::VERSION);
    }

    /**
     * @return Color
     */
    public static function color()
    {
        return app('admin.color');
    }

    /**
     * 菜单管理.
     *
     * @return Menu
     */
    public static function menu(?Closure $builder = null)
    {
        $menu = app('admin.menu');

        $builder && $builder($menu);

        return $menu;
    }

    /**
     * 设置 title.
     *
     * @return string|void
     */
    public static function title($title = null)
    {
        if ($title === null) {
            return static::context()->metaTitle ?: config('admin.title');
        }

        static::context()->metaTitle = $title;
    }

    /**
     * @param  null|string  $favicon
     * @return string|void
     */
    public static function favicon($favicon = null)
    {
        if ($favicon === null) {
            return static::context()->favicon ?: config('admin.favicon');
        }

        static::context()->favicon = $favicon;
    }

    /**
     * 设置翻译文件路径.
     */
    public static function translation(?string $path)
    {
        static::context()->translation = $path;
    }

    /**
     * 获取登录用户模型.
     *
     * @return Model|Authenticatable|null
     */
    public static function user()
    {
        return static::guard()->user();
    }

    /**
     * @return Guard|StatefulGuard
     */
    public static function guard()
    {
        return Auth::guard(config('admin.auth.guard') ?: 'admin');
    }

    /**
     * @return Navbar
     */
    public static function navbar(?Closure $builder = null)
    {
        $navbar = app('admin.navbar');

        $builder && $builder($navbar);

        return $navbar;
    }

    /**
     * 启用或禁用Pjax.
     *
     * @return void
     */
    public static function pjax(bool $value = true)
    {
        static::context()->pjaxContainerId = $value ? self::$defaultPjaxContainerId : false;
    }

    /**
     * 禁用pjax.
     *
     * @return void
     */
    public static function disablePjax()
    {
        static::pjax(false);
    }

    /**
     * 获取pjax ID.
     *
     * @return string|void
     */
    public static function getPjaxContainerId()
    {
        $id = static::context()->pjaxContainerId;

        if ($id === false) {
            return;
        }

        return $id ?: self::$defaultPjaxContainerId;
    }

    /**
     * section.
     *
     * @return SectionManager
     */
    public static function section(?Closure $builder = null)
    {
        $manager = app('admin.sections');

        $builder && $builder($manager);

        return $manager;
    }

    /**
     * 配置.
     *
     * @return Setting
     */
    public static function setting()
    {
        return app('admin.setting');
    }

    /**
     * 创建数据仓库实例.
     *
     * @param  string|Repository|Model|Builder  $repository
     * @return Repository
     */
    public static function repository($repository, array $args = [])
    {
        if (is_string($repository)) {
            $repository = new $repository($args);
        }

        if ($repository instanceof Model || $repository instanceof Builder) {
            $repository = EloquentRepository::make($repository);
        }

        if (! $repository instanceof Repository) {
            $class = is_object($repository) ? get_class($repository) : $repository; // @phpstan-ignore-line

            throw new InvalidArgumentException("The class [{$class}] must be a type of [".Repository::class.'].');
        }

        return $repository;
    }

    /**
     * 应用管理.
     *
     * @return Application
     */
    public static function app()
    {
        return app('admin.app');
    }

    /**
     * 处理异常.
     *
     * @return mixed
     */
    public static function handleException(\Throwable $e)
    {
        return app(ExceptionHandler::class)->handle($e);
    }

    /**
     * 上报异常.
     *
     * @return mixed
     */
    public static function reportException(\Throwable $e)
    {
        return app(ExceptionHandler::class)->report($e);
    }

    /**
     * 显示异常信息.
     *
     * @return mixed
     */
    public static function renderException(\Throwable $e)
    {
        return app(ExceptionHandler::class)->render($e);
    }

    /**
     * @param  callable  $callback
     */
    public static function booting($callback)
    {
        Event::listen('admin:booting', $callback);
    }

    /**
     * @param  callable  $callback
     */
    public static function booted($callback)
    {
        Event::listen('admin:booted', $callback);
    }

    /**
     * @return void
     */
    public static function callBooting()
    {
        Event::dispatch('admin:booting');
    }

    /**
     * @return void
     */
    public static function callBooted()
    {
        Event::dispatch('admin:booted');
    }

    /**
     * 上下文管理.
     *
     * @return Context
     */
    public static function context()
    {
        return app('admin.context');
    }

    /**
     * 翻译器.
     *
     * @return Translator
     */
    public static function translator()
    {
        return app('admin.translator');
    }

    /**
     * @param  array|string  $name
     * @return void
     */
    public static function addIgnoreQueryName($name)
    {
        $context = static::context();

        $ignoreQueries = $context->ignoreQueries ?? [];

        $context->ignoreQueries = array_merge($ignoreQueries, (array) $name);
    }

    /**
     * @return array
     */
    public static function getIgnoreQueryNames()
    {
        return static::context()->ignoreQueries ?? [];
    }

    /**
     * 中断默认的渲染逻辑.
     *
     * @param  string|Renderable|Closure  $value
     */
    public static function prevent($value)
    {
        if ($value !== null) {
            static::context()->add('contents', $value);
        }
    }

    /**
     * @return bool
     */
    public static function shouldPrevent()
    {
        return count(static::context()->getArray('contents')) > 0;
    }

    /**
     * 渲染内容.
     *
     * @return string|void
     */
    public static function renderContents()
    {
        if (! static::shouldPrevent()) {
            return;
        }

        $results = '';

        foreach (static::context()->getArray('contents') as $content) {
            $results .= Helper::render($content);
        }

        // 等待JS脚本加载完成
        static::script('Dcat.wait()', true);

        $asset = static::asset();

        static::baseCss([], false);
        static::baseJs([], false);
        static::headerJs([], false);
        static::fonts([]);

        return $results
            .static::html()
            .$asset->jsToHtml()
            .$asset->cssToHtml()
            .$asset->scriptToHtml()
            .$asset->styleToHtml();
    }

    /**
     * 响应json数据.
     *
     * @return JsonResponse
     */
    public static function json(array $data = [])
    {
        return JsonResponse::make($data);
    }

    /**
     * 插件管理.
     *
     * @return Manager|ServiceProvider|null
     */
    public static function extension(?string $name = null)
    {
        if ($name) {
            return app('admin.extend')->get($name);
        }

        return app('admin.extend');
    }

    /**
     * 响应并中断后续逻辑.
     *
     * @param  Response|JsonResponse|string|array  $response
     *
     * @throws HttpResponseException
     */
    public static function exit($response = '')
    {
        if (is_array($response)) {
            $response = response()->json($response);
        } elseif ($response instanceof JsonResponse) {
            $response = $response->send();
        }

        throw new HttpResponseException($response instanceof Response ? $response : response($response));
    }

    /**
     * 类自动加载器.
     *
     * @return ClassLoader
     */
    public static function classLoader()
    {
        return Composer::loader();
    }

    /**
     * 往分组插入中间件.
     */
    public static function mixMiddlewareGroup(array $mix = [])
    {
        $router = app('router');

        $group = $router->getMiddlewareGroups()['admin'] ?? [];

        if ($mix) {
            $finalGroup = [];
            $inserted = false;

            foreach ($group as $i => $mid) {
                $next = $i + 1;

                $finalGroup[] = $mid;

                if ($inserted || ! isset($group[$next]) || $group[$next] !== 'admin.permission') {
                    continue;
                }

                foreach ($mix as $m) {
                    $finalGroup[] = $m;
                }
                $inserted = true;
            }

            if (! $inserted) {
                foreach ($mix as $m) {
                    $finalGroup[] = $m;
                }
            }

            $group = $finalGroup;
        }

        $router->middlewareGroup('admin', $group);
    }

    /**
     * 获取js配置.
     *
     * @return string|void
     */
    public static function jsVariables(?array $variables = null)
    {
        $context = static::context();
        $jsVariables = $context->jsVariables ?: [];

        if ($variables !== null) {
            $context->jsVariables = $jsVariables
                ? ($variables ? array_merge($jsVariables, $variables) : $jsVariables)
                : $variables;

            return;
        }

        $sidebarStyle = config('admin.layout.sidebar_style') ?: 'light';

        $pjaxId = static::getPjaxContainerId();

        $jsVariables['pjax_container_selector'] = $pjaxId ? ('#'.$pjaxId) : '';
        $jsVariables['token'] = csrf_token();
        $lang = __('admin.client');
        $customLang = $jsVariables['lang'] ?? [];
        $jsVariables['lang'] = $lang ? ($customLang ? array_merge($lang, $customLang) : $lang) : [];
        $jsVariables['colors'] = static::color()->all();
        $jsVariables['dark_mode'] = static::isDarkMode();
        $jsVariables['sidebar_dark'] = config('admin.layout.sidebar_dark') || ($sidebarStyle === 'dark');
        $jsVariables['sidebar_light_style'] = in_array($sidebarStyle, ['dark', 'light'], true) ? 'sidebar-light-primary' : 'sidebar-primary';

        return admin_javascript_json($jsVariables);
    }

    /**
     * @return bool
     */
    public static function isDarkMode()
    {
        $bodyClass = config('admin.layout.body_class');

        return in_array(
            'dark-mode',
            is_array($bodyClass) ? $bodyClass : explode(' ', (string) $bodyClass),
            true
        );
    }

    /**
     * 全局搜索.
     */
    public static function globalSearch(): Widgets\GlobalSearch
    {
        return static::$globalSearch ?: (static::$globalSearch = new Widgets\GlobalSearch);
    }

    /**
     * 注册路由.
     *
     * @return void
     */
    public static function routes()
    {
        $attributes = [
            'prefix' => config('admin.route.prefix'),
            'middleware' => config('admin.route.middleware'),
        ];

        if (config('admin.auth.enable', true)) {
            app('router')->group($attributes, function ($router) {
                /* @var \Illuminate\Routing\Router $router */
                $router->resource('auth/users', UserController::class);
                $router->resource('auth/menu', MenuController::class, ['except' => ['create', 'show']]);

                if (config('admin.permission.enable')) {
                    $router->resource('auth/roles', RoleController::class);
                    $router->resource('auth/permissions', PermissionController::class);
                }

                // 部门管理路由
                if (config('admin.department.enable', false)) {
                    $router->resource('auth/departments', DepartmentController::class);
                }

                // 数据规则路由
                if (config('admin.data_permission.enable', false)) {
                    $router->resource('auth/data-rules', DataRuleController::class);
                }

                $router->resource('auth/extensions', ExtensionController::class, ['only' => ['index', 'store', 'update']]);

                $authController = config('admin.auth.controller', AuthController::class);

                $router->get('auth/login', [$authController, 'getLogin']);
                $router->post('auth/login', [$authController, 'postLogin']);
                $router->get('auth/logout', [$authController, 'getLogout']);
                $router->get('auth/setting', [$authController, 'getSetting']);
                $router->put('auth/setting', [$authController, 'putSetting']);
            });
        }

        static::registerHelperRoutes();
    }

    /**
     * 注册api路由.
     *
     * @return void
     */
    public static function registerApiRoutes()
    {
        $attributes = [
            'prefix' => admin_base_path('dcat-api'),
            'middleware' => config('admin.route.middleware'),
            'as' => 'dcat-api.',
        ];

        app('router')->group($attributes, function ($router) {
            /* @var \Illuminate\Routing\Router $router */
            $router->post('action', [HandleActionController::class, 'handle'])->name('action');
            $router->post('form', [HandleFormController::class, 'handle'])->name('form');
            $router->post('form/upload', [HandleFormController::class, 'uploadFile'])->name('form.upload');
            $router->post('form/destroy-file', [HandleFormController::class, 'destroyFile'])->name('form.destroy-file');
            $router->post('value', [ValueController::class, 'handle'])->name('value');
            $router->get('render', [RenderableController::class, 'handle'])->name('render');
            $router->post('tinymce/upload', [TinymceController::class, 'upload'])->name('tinymce.upload');
            $router->post('editor-md/upload', [EditorMDController::class, 'upload'])->name('editor-md.upload');

            $router->get('import/template', [ImportController::class, 'template'])->name('import.template');
            $router->post('import/execute', [ImportController::class, 'execute'])->name('import.execute');

            // OSS direct upload routes (when enabled)
            if (config('admin.upload.oss.enable', false)) {
                $router->post('oss/sts-token', [OssController::class, 'getStsToken'])->name('oss.sts-token');
                $router->post('oss/filename', [OssController::class, 'generateFilename'])->name('oss.filename');
                $router->get('oss/proxy/{path}', [OssController::class, 'privateImageProxy'])->name('oss.proxy')->where('path', '.*');
            }
        });
    }

    /**
     * 注册开发工具路由.
     *
     * @return void
     */
    public static function registerHelperRoutes()
    {
        if (! config('admin.helpers.enable', true) || ! config('app.debug')) {
            return;
        }

        $attributes = [
            'prefix' => config('admin.route.prefix'),
            'middleware' => config('admin.route.middleware'),
        ];

        app('router')->group($attributes, function ($router) {
            /* @var \Illuminate\Routing\Router $router */
            $router->get('helpers/scaffold', [ScaffoldController::class, 'index']);
            $router->post('helpers/scaffold', [ScaffoldController::class, 'store']);
            $router->post('helpers/scaffold/table', [ScaffoldController::class, 'table']);
            $router->get('helpers/icons', [IconController::class, 'index']);
        });
    }

    public static function resetState(): void
    {
        static::$globalSearch = null;
    }
}
