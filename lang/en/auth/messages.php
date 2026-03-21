<?php
return [
    // Validaciones
    'email_required' => 'Email is required.',
    'email_invalid' => 'Please enter a valid email address with a real domain.',
    'password_required' => 'Password is required.',
    'email_unique' => 'This email is already registered.',
    'password_min' => 'Password must be at least 8 characters.',
    'password_mixed' => 'Must include uppercase and lowercase letters.',
    'password_numbers' => 'Must include at least one number.',
    'password_symbols' => 'Must include at least one symbol (@$!%*?&).',
    'password_confirmed' => 'Passwords do not match.',
    
    // Respuestas
    'credentials_mismatch' => 'These credentials do not match our records.',
    'reset_link_sent' => 'We have emailed your password reset link.',
    'user_not_found' => 'We can\'t find a user with that email address.',
    'password_reset_success' => 'Your password has been successfully reset! You can now log in.',
    'invalid_token' => 'This password reset token is invalid or has expired. Please request a new one.',
];