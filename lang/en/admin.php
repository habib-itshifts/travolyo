<?php

/*
 * Admin panel UI strings — English (LTR)
 *
 * Usage in Blade:  __('admin.key')
 * To add a new language: duplicate this file into lang/{locale}/admin.php and translate the values.
 *
 * RTL locales supported: ar, he, fa, ur
 * Direction is set automatically in master.blade.php — no extra work needed here.
 */

return [

    // ── General ──────────────────────────────────────────────────────
    'app_name'                 => 'Travolyo',
    'view_site'                => 'View Site',
    'logout'                   => 'Logout',
    'language_label'           => 'English',
    'administrator'            => 'Administrator',
    'view_all'                 => 'View All',
    'last_7_days'              => 'Last 7 days',

    // ── Sidebar navigation ───────────────────────────────────────────
    'nav_dashboard'            => 'Dashboard',
    'nav_location'             => 'Location',
    'nav_hotel'                => 'Hotel',
    'nav_hotel_rooms'          => 'Hotel Rooms',
    'nav_tour'                 => 'Tour',
    'nav_flight'               => 'Flight',
    'nav_bookings'             => 'Bookings',
    'nav_reviews'              => 'Reviews',
    'nav_news'                 => 'News',
    'nav_media'                => 'Media',
    'nav_amenities'            => 'Amenities',
    'nav_services'             => 'Services',
    'nav_currencies'           => 'Currencies',
    'section_content'          => 'Content',

    // ── Dashboard page ───────────────────────────────────────────────
    'dashboard'                => 'Dashboard',
    'dashboard_subtitle'       => 'Welcome back, :name',
    'welcome_title'            => 'Welcome back, :email!',
    'welcome_subtitle'         => "Here's what's happening with your business today.",
    'btn_view_reports'         => 'View Reports',
    'btn_new_booking'          => 'New Booking',

    // Stat cards
    'stat_revenue'             => 'Total Revenue',
    'stat_earning'             => 'Total Earning',
    'stat_bookings'            => 'Total Bookings',
    'stat_services'            => 'Bookable Services',

    // Chart
    'chart_title'              => 'Earning Statistics',
    'chart_subtitle'           => 'Revenue vs Earning overview',
    'chart_revenue_label'      => 'Total Revenue',
    'chart_earning_label'      => 'Total Earning',

    // Recent bookings
    'recent_bookings'          => 'Recent Bookings',
    'recent_bookings_subtitle' => 'Latest transactions',
    'status_completed'         => 'Completed',
    'status_paid'              => 'Paid',

];
