<?php
/* 
Plugin Name: UKMidebank
Plugin URI: http://www.ukm-norge.no
Description: Idébanken i arrangørsystemet. Henter ut innhold fra UKM.no/arrangorer/idebank-siden
Author: UKM Norge / M Mandal 
Version: 1.0
Author URI: http://www.ukm-norge.no
*/

use UKMNorge\Wordpress\Modul;

class UKMide extends Modul
{
    const SLUG = 'idebank';
    static $action = 'idebank';
    public static $path_plugin = null;

    public static function hook()
    {
        add_action(
            'wp_ajax_UKMidebank_ajax',
            ['UKMide', 'ajax']
        );

        add_action('user_admin_menu', [static::class, 'meny']);
        add_action('admin_init', [static::class, 'registerCloudflareScript']);
    }

    public static function meny()
    {
        $page = add_menu_page(
            'Verktøykasse',
            'Verktøykasse',
            'subscriber', //Deffinerer hva slags brukerrettigheter brukeren måtte ha for å vise menyvalg "Verktøykasse"
            static::SLUG,
            [static::class, 'renderAdmin'],
            'dashicons-welcome-learn-more',
            45
        );
        add_action(
            'admin_print_styles-' . $page,
            [static::class, 'scripts_and_styles']
        );

        # Legg til menyelementer og enqueue scripts + styles
        foreach (static::getSubpages() as $child) {
            $subpage = add_submenu_page(
                'idebank',
                $child->post_title,
                $child->post_title,
                'subscriber', //Deffinerer hva slags brukerrettigheter brukeren måtte ha for å vise menyvalg "Verktøykasse"
                'UKMide_' . $child->post_name,
                [static::class, 'renderAdmin']
            );
            add_action(
                'admin_print_styles-' . $subpage,
                [static::class, 'scripts_and_styles']
            );
        }
    }

    public static function getSubpages()
    {
        // LIST UT ALLE IDÉBANKER
        global $ID_ARRANGOR;

        # Bytt til arrangor
        switch_to_blog(UKM_HOSTNAME == 'ukm.dev' ? 13 : 881);

        # Hent alle sider
        $parent_page = get_page_by_path('idebank');
        # Hent alle sider
        $my_wp_query = new WP_Query();
        $subpages = $my_wp_query->query(array('post_parent' => $parent_page->ID, 'post_type' => 'page', 'posts_per_page' => 100, 'orderby' => 'menu_order', 'order' => 'ASC'));

        foreach ($subpages as $subpage) {
            $subpage->meta = new stdClass();
            $subpage->meta->dashicon = $subpage->__get('dashicon');
            $subpage->meta->description = $subpage->__get('description');
        }

        # Restore til aktiv side
        restore_current_blog();

        return $subpages;
    }

    public static function registerCloudflareScript() {
        // Bruker echo script for å legge til attributter
        // Gjelder bare idebank
        if($_GET['page'] == 'UKMide_nyhetsbrev') {
            echo "<script defer src='https://static.cloudflareinsights.com/beacon.min.js' data-cf-beacon='{\"token\": \"d4e3635a277744b7a073451f00092195\"}'></script>";
        }
    }

    public static function scripts_and_styles()
    {
        wp_enqueue_script('WPbootstrap3_js');
        wp_enqueue_style('WPbootstrap3_css');        
        wp_enqueue_style('UKMide_css', static::getPluginUrl() . 'ukmidebank.css');
    }

    public static function renderAdmin()
    {
        if( $_GET['page'] != static::SLUG ) {
            $_GET['PAGE_SLUG'] = str_replace('UKMide_', '', $_GET['page']);
            if (isset($_GET['subpage'])) {
                $_GET['PAGE_SLUG'] = $_GET['PAGE_SLUG'] . '/' . $_GET['subpage'];
            }
            static::setAction('page');
            static::addViewData('current_page', $_GET['page']);
        }

        return parent::renderAdmin();
    }
}

UKMide::init(__DIR__);
UKMide::hook();
