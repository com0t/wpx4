<?php
/* * * * * * * * * * * * * * * * * * * *
 *  ██████╗ █████╗  ██████╗ ███████╗
 * ██╔════╝██╔══██╗██╔═══██╗██╔════╝
 * ██║     ███████║██║   ██║███████╗
 * ██║     ██╔══██║██║   ██║╚════██║
 * ╚██████╗██║  ██║╚██████╔╝███████║
 *  ╚═════╝╚═╝  ╚═╝ ╚═════╝ ╚══════╝
 *
 * @author   : Daan van den Bergh
 * @url      : https://daan.dev/wordpress-plugins/caos/
 * @copyright: (c) 2021 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */

defined('ABSPATH') || exit;

class CAOS
{
    /**
     * Used to check if Super Stealth is (de)activated and update files (e.g. analytics.js) accordingly.
     */
    const CAOS_SUPER_STEALTH_UPGRADE_PLUGIN_SLUG = 'caos-super-stealth-upgrade';

    /** @var string $plugin_text_domain */
    private $plugin_text_domain = 'host-analyticsjs-local';

    /**
     * CAOS constructor.
     */
    public function __construct()
    {
        $this->define_constants();
        $this->do_setup();

        if (is_admin()) {
            $this->do_settings();
        }

        if (!is_admin()) {
            $this->do_frontend();
            $this->do_tracking_code();
        }

        // API Routes
        add_action('rest_api_init', [$this, 'register_routes']);

        // Automatic File Updates
        add_action('activated_plugin', function ($plugin) {
            $this->maybe_do_update($plugin, 'activate');
        });
        add_action('deactivated_plugin', function ($plugin) {
            $this->maybe_do_update($plugin, 'deactivate');
        });
        add_action('admin_init', [$this, 'do_update_after_save']);
    }

    /**
     * Define constants
     */
    public function define_constants()
    {
        global $caos_file_aliases;

        $caos_file_aliases      = get_option(CAOS_Admin_Settings::CAOS_CRON_FILE_ALIASES);
        $translated_tracking_id = _x('UA-123456789', 'Define a different Tracking ID for this site.', $this->plugin_text_domain);

        define('CAOS_SITE_URL', 'https://daan.dev');
        define('CAOS_BLOG_ID', get_current_blog_id());
        define('CAOS_OPT_TRACKING_ID', $translated_tracking_id != 'UA-123456789' ? $translated_tracking_id : esc_attr(get_option(CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACKING_ID)));
        define('CAOS_OPT_ALLOW_TRACKING', esc_attr(get_option(CAOS_Admin_Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING)));
        define('CAOS_OPT_COOKIE_NAME', esc_attr(get_option(CAOS_Admin_Settings::CAOS_BASIC_SETTING_COOKIE_NOTICE_NAME)));
        define('CAOS_OPT_COOKIE_VALUE', esc_attr(get_option(CAOS_Admin_Settings::CAOS_BASIC_SETTING_COOKIE_VALUE)));
        define('CAOS_OPT_SNIPPET_TYPE', esc_attr(get_option(CAOS_Admin_Settings::CAOS_BASIC_SETTING_SNIPPET_TYPE)));
        define('CAOS_OPT_SCRIPT_POSITION', esc_attr(get_option(CAOS_Admin_Settings::CAOS_BASIC_SETTING_SCRIPT_POSITION)) ?: 'header');
        define('CAOS_OPT_COMPATIBILITY_MODE', esc_attr(get_option(CAOS_Admin_Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE)) ?: null);
        define('CAOS_OPT_COOKIE_EXPIRY_DAYS', esc_attr(get_option(CAOS_Admin_Settings::CAOS_ADV_SETTING_GA_COOKIE_EXPIRY_DAYS, 30)));
        define('CAOS_OPT_SITE_SPEED_SAMPLE_RATE', esc_attr(get_option(CAOS_Admin_Settings::CAOS_ADV_SETTING_SITE_SPEED_SAMPLE_RATE, 1)));
        define('CAOS_OPT_ADJUSTED_BOUNCE_RATE', esc_attr(get_option(CAOS_Admin_Settings::CAOS_EXT_SETTING_ADJUSTED_BOUNCE_RATE)));
        define('CAOS_OPT_ENQUEUE_ORDER', esc_attr(get_option(CAOS_Admin_Settings::CAOS_ADV_SETTING_ENQUEUE_ORDER)) ?: 10);
        define('CAOS_OPT_ANONYMIZE_IP', esc_attr(get_option(CAOS_Admin_Settings::CAOS_BASIC_SETTING_ANONYMIZE_IP)));
        define('CAOS_OPT_TRACK_ADMIN', esc_attr(get_option(CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACK_ADMIN)));
        define('CAOS_OPT_DISABLE_DISPLAY_FEAT', esc_attr(get_option(CAOS_Admin_Settings::CAOS_ADV_SETTING_DISABLE_DISPLAY_FEATURES)));
        define('CAOS_OPT_REMOTE_JS_FILE', esc_attr(get_option(CAOS_Admin_Settings::CAOS_ADV_SETTING_JS_FILE)) ?: 'analytics.js');
        define('CAOS_OPT_COOKIELESS_ANALYTICS', esc_attr(get_option(CAOS_Admin_Settings::CAOS_ADV_SETTING_COOKIELESS_ANALYTICS)));
        define('CAOS_OPT_CACHE_DIR', esc_attr(get_option(CAOS_Admin_Settings::CAOS_ADV_SETTING_CACHE_DIR)) ?: '/uploads/caos/');
        define('CAOS_OPT_CDN_URL', esc_attr(get_option(CAOS_Admin_Settings::CAOS_ADV_SETTING_CDN_URL)));
        define('CAOS_OPT_EXT_CAPTURE_OUTBOUND_LINKS', esc_attr(get_option(CAOS_Admin_Settings::CAOS_EXT_SETTING_CAPTURE_OUTBOUND_LINKS)));
        define('CAOS_OPT_DEBUG_MODE', esc_attr(get_option(CAOS_Admin_Settings::CAOS_ADV_SETTING_DEBUG_MODE)));
        define('CAOS_OPT_UNINSTALL_SETTINGS', esc_attr(get_option(CAOS_Admin_Settings::CAOS_ADV_SETTING_UNINSTALL_SETTINGS)));
        define('CAOS_OPT_EXT_PLUGIN_HANDLING', esc_attr(get_option(CAOS_Admin_Settings::CAOS_EXT_SETTING_PLUGIN_HANDLING)) ?: 'set_redirect');
        define('CAOS_OPT_EXT_STEALTH_MODE', esc_attr(get_option(CAOS_Admin_Settings::CAOS_EXT_SETTING_STEALTH_MODE)));
        define('CAOS_OPT_EXT_TRACK_AD_BLOCKERS', esc_attr(get_option(CAOS_Admin_Settings::CAOS_EXT_SETTING_TRACK_AD_BLOCKERS)));
        define('CAOS_OPT_EXT_LINKID', esc_attr(get_option(CAOS_Admin_Settings::CAOS_EXT_SETTING_LINKID)));
        define('CAOS_OPT_EXT_OPTIMIZE', esc_attr(get_option(CAOS_Admin_Settings::CAOS_EXT_SETTING_OPTIMIZE)));
        define('CAOS_OPT_EXT_OPTIMIZE_ID', esc_attr(get_option(CAOS_Admin_Settings::CAOS_EXT_SETTING_OPTIMIZE_ID)));
        define('CAOS_COOKIE_EXPIRY_SECONDS', CAOS_OPT_COOKIE_EXPIRY_DAYS ? CAOS_OPT_COOKIE_EXPIRY_DAYS * 86400 : 2592000);
        define('CAOS_CRON', 'caos_update_analytics_js');
        define('CAOS_GA_URL', 'https://www.google-analytics.com');
        define('CAOS_GTM_URL', 'https://www.googletagmanager.com');
        define('CAOS_LOCAL_DIR', WP_CONTENT_DIR . CAOS_OPT_CACHE_DIR);
        define('CAOS_PROXY_URI', '/wp-json/caos/v1/proxy');
    }

    /**
     * @return false|array 
     */
    public static function get_file_aliases()
    {
        global $caos_file_aliases;

        return $caos_file_aliases;
    }

    /**
     * @param string $key 
     * @return string 
     */
    public static function get_file_alias($key = '')
    {
        $file_aliases = self::get_file_aliases();

        if (!$file_aliases) {
            return '';
        }

        return $file_aliases[$key] ?? '';
    }

    /**
     * @param array $file_aliases 
     * @param bool $write 
     * @return bool 
     */
    public static function set_file_aliases($file_aliases, $write = false)
    {
        global $caos_file_aliases;

        $caos_file_aliases = $file_aliases;

        if ($write) {
            return update_option(CAOS_Admin_Settings::CAOS_CRON_FILE_ALIASES, $file_aliases);
        }

        /**
         * There's no reason to assume that updating a global variable would fail. Always return true at this point.
         */
        return true;
    }

    /**
     * @param string $key 
     * @param string $alias 
     * @param bool $write 
     * @return bool 
     */
    public static function set_file_alias($key, $alias, $write = false)
    {
        $file_aliases = self::get_file_aliases();

        $file_aliases[$key] = $alias;

        return self::set_file_aliases($file_aliases, $write);
    }

    /**
     * Includes backwards compatibility for pre 3.11.0
     * 
     * @since 3.11.0
     * 
     * @param mixed $key 
     * @return string|void 
     */
    public static function get_file_alias_path($key)
    {
        $file_path = CAOS_LOCAL_DIR . $key . '.js';

        // Backwards compatibility
        if (!self::get_file_aliases()) {
            return $file_path;
        }

        $file_alias = self::get_file_alias($key) ?? '';

        // Backwards compatibility
        if (!$file_alias) {
            return $file_path;
        }

        return CAOS_LOCAL_DIR . $file_alias;
    }

    /**
     * Global debug logging function.
     * 
     * @param mixed $message 
     * @return void 
     */
    public static function debug($message)
    {
        if (!CAOS_OPT_DEBUG_MODE) {
            return;
        }

        error_log(current_time('Y-m-d H:i:s') . ": $message\n", 3, trailingslashit(WP_CONTENT_DIR) . 'caos-debug.log');
    }

    /**
     * @return CAOS_Setup
     */
    private function do_setup()
    {
        register_uninstall_hook(CAOS_PLUGIN_FILE, 'CAOS::do_uninstall');

        return new CAOS_Setup();
    }

    /**
     * @return CAOS_Admin_Settings
     */
    private function do_settings()
    {
        return new CAOS_Admin_Settings();
    }

    /**
     * @return CAOS_Frontend_Functions
     */
    private function do_frontend()
    {
        return new CAOS_Frontend_Functions();
    }

    /**
     * @return CAOS_Frontend_Tracking
     */
    private function do_tracking_code()
    {
        return new CAOS_Frontend_Tracking();
    }

    /**
     * Triggers when Super Stealth Upgrade is (de)activated.
     * 
     * @return CAOS_Cron_Update 
     */
    public function trigger_cron_script()
    {
        return new CAOS_Cron_Update();
    }

    /**
     * Check if (de)activated plugin is Super Stealth and if so, update or notify.
     */
    public function maybe_do_update($plugin, $action = 'activate')
    {
        if (strpos($plugin, self::CAOS_SUPER_STEALTH_UPGRADE_PLUGIN_SLUG) === false) {
            return;
        }

        $this->update_or_notify($action);
    }

    /**
     * Run automatic update when Super Stealth is activated.
     * 
     * TODO: Why doesn't automatic update work when Super Stealth is deactivated?
     * 
     * @param string $action 
     * @return CAOS_Cron_Update 
     */
    private function update_or_notify($action)
    {
        if ($action == 'activate') {
            return $this->trigger_cron_script();
        }

        CAOS_Admin_Notice::set_notice(sprintf(__('Super Stealth was deactivated. Please <a href="%s">review CAOS\' Extensions Settings</a> and Save Changes to update all locally hosted files.', $this->plugin_text_domain), admin_url('options-general.php?page=host_analyticsjs_local&tab=caos-extensions-settings')), 'info');
    }

    /**
     * @return CAOS_Admin_UpdateFiles 
     */
    public function do_update_after_save()
    {
        $settings_page    = $_GET['page'] ?? '';
        $settings_updated = $_GET['settings-updated'] ?? '';

        if (CAOS_Admin_Settings::CAOS_ADMIN_PAGE != $settings_page) {
            return;
        }

        if (!$settings_updated) {
            return;
        }

        return $this->trigger_cron_script();
    }

    /**
     * Register CAOS Proxy so endpoint can be used.
     * For using Stealth mode, SSL is required.
     */
    public function register_routes()
    {
        if (CAOS_OPT_EXT_STEALTH_MODE) {
            $proxy = new CAOS_API_Proxy();
            $proxy->register_routes();
        }

        if (CAOS_OPT_EXT_TRACK_AD_BLOCKERS) {
            $proxy = new CAOS_API_AdBlockDetect();
            $proxy->register_routes();
        }
    }

    /**
     * Returns early if File Aliases option doesn't exist for Backwards Compatibility.
     * 
     * @since 3.11.0
     *  
     * @return string
     */
    public static function get_local_file_url()
    {
        $url = content_url() . CAOS_OPT_CACHE_DIR . CAOS_OPT_REMOTE_JS_FILE;

        /**
         * is_ssl() fails when behind a load balancer or reverse proxy. That's why we double check here if 
         * SSL is enabled and rewrite accordingly.
         */
        if (strpos(home_url(), 'https://') !== false && !is_ssl()) {
            $url = str_replace('http://', 'https://', $url);
        }

        if (CAOS_OPT_CDN_URL) {
            $url = str_replace(get_home_url(CAOS_BLOG_ID), '//' . CAOS_OPT_CDN_URL, $url);
        }

        if (!self::get_file_aliases()) {
            return $url;
        }

        $filehandle = str_replace('.js', '', CAOS_OPT_REMOTE_JS_FILE);
        $file_alias = self::get_file_alias($filehandle);

        if (!$file_alias) {
            return $url;
        }

        $url = str_replace(CAOS_OPT_REMOTE_JS_FILE, $file_alias, $url);

        return $url;
    }

    /**
     * @return CAOS_Uninstall
     * @throws ReflectionException
     */
    public static function do_uninstall()
    {
        return new CAOS_Uninstall();
    }

    /**
     * Helper to return WordPress filesystem subclass.
     *
     * @return WP_Filesystem_Base $wp_filesystem
     */
    public static function filesystem()
    {
        global $wp_filesystem;

        if (is_null($wp_filesystem)) {
            require_once ABSPATH . '/wp-admin/includes/file.php';
            WP_Filesystem();
        }

        return $wp_filesystem;
    }
}
