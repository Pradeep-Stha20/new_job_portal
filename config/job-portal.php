<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Job Portal Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains all custom configuration for the Job Portal system
    | including fraud detection, job matching, and feature settings
    |
    */

    'fraud_detection' => [
        // Fraud score thresholds
        'safe_threshold' => 30,
        'moderate_threshold' => 60,
        'high_risk_threshold' => 80,

        // Minimum fraud score to flag application
        'flag_threshold' => 85,

        // Detection settings
        'enable_spam_detection' => true,
        'enable_duplicate_check' => true,
        'enable_profile_check' => true,
        'enable_cv_validation' => true,
        'enable_pattern_detection' => true,
        'enable_account_age_check' => true,
    ],

    'job_matching' => [
        // Match score weights (must sum to 100)
        'skills_weight' => 50,
        'experience_weight' => 30,
        'profile_weight' => 20,

        // Match thresholds
        'excellent_match' => 61,  // 61-100
        'moderate_match' => 31,   // 31-60
        'poor_match' => 0,        // 0-30

        // Enable recommendations
        'enable_recommendations' => true,
        'recommendation_limit' => 10,
    ],

    'pagination' => [
        'per_page' => 15,
        'admin_per_page' => 25,
    ],

    'files' => [
        'cv_max_size' => 2048,  // KB
        'profile_pic_max_size' => 1024,  // KB
        'allowed_cv_types' => ['pdf', 'doc', 'docx'],
        'allowed_image_types' => ['jpg', 'jpeg', 'png', 'gif'],
    ],

    'email' => [
        'send_notification_on_application' => true,
        'send_notification_on_approval' => true,
        'send_notification_on_rejection' => true,
        'queue_emails' => false,
    ],

    'features' => [
        'enable_saved_jobs' => true,
        'enable_job_recommendations' => true,
        'enable_fraud_detection' => true,
        'enable_applicant_ranking' => true,
    ],

    'security' => [
        'enable_rate_limiting' => true,
        'max_applications_per_day' => 50,
        'enable_csrf_protection' => true,
    ],
];
