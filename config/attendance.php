<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Attendance Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for attendance system settings
    |
    */

    'timezone' => env('ATTENDANCE_TIMEZONE', 'Asia/Jakarta'),
    
    'limits' => [
        'max_duration_hours' => env('ATTENDANCE_MAX_DURATION_HOURS', 48),
        'min_duration_minutes' => env('ATTENDANCE_MIN_DURATION_MINUTES', 1),
        'warning_duration_hours' => env('ATTENDANCE_WARNING_DURATION_HOURS', 24),
        'long_duty_threshold_hours' => env('ATTENDANCE_LONG_DUTY_THRESHOLD_HOURS', 8),
    ],

    'session_types' => [
        'work' => 'Kerja',
        'break' => 'Istirahat',
        'meeting' => 'Rapat',
        'overtime' => 'Lembur'
    ],

    'validation' => [
        'player_id_regex' => '/^[a-zA-Z0-9_:]+$/',
        'player_name_regex' => '/^[a-zA-Z0-9\s_-]+$/',
        'time_on_duty_regex' => '/^\d{2}:\d{2}:\d{2}$/',
    ],

    'rate_limits' => [
        'api_absensi' => env('ATTENDANCE_API_RATE_LIMIT', '30,1'),
        'api_general' => env('ATTENDANCE_API_GENERAL_RATE_LIMIT', '60,1'),
        'web_attendance' => env('ATTENDANCE_WEB_RATE_LIMIT', '10,1'),
    ],

    'notifications' => [
        'enabled' => env('ATTENDANCE_NOTIFICATIONS_ENABLED', true),
        'discord_enabled' => env('ATTENDANCE_DISCORD_ENABLED', true),
        'long_duty_check_interval' => env('ATTENDANCE_LONG_DUTY_CHECK_INTERVAL', 60), // minutes
    ],

    'auto_split' => [
        'enabled' => env('ATTENDANCE_AUTO_SPLIT_ENABLED', true),
        'cross_day_enabled' => env('ATTENDANCE_CROSS_DAY_SPLIT_ENABLED', true),
        'cross_week_enabled' => env('ATTENDANCE_CROSS_WEEK_SPLIT_ENABLED', true),
    ],

    'database' => [
        'indexes' => [
            'attendances_user_work_date' => ['user_id', 'work_date'],
            'attendances_work_date_session_duration' => ['work_date', 'session_duration'],
            'attendances_user_session_type' => ['user_id', 'session_type'],
        ]
    ],
];
