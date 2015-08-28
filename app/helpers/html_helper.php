<?php

function eh($string)
{
    if (!isset($string)) return;
    echo htmlspecialchars($string, ENT_QUOTES);
}

function readable_text($s) {
    $s = htmlspecialchars($s, ENT_QUOTES);
    $s = nl2br($s);
    return $s;
}

// trim whitespace from $str and collapse adjacent whitespaces into one.
function trim_collapse($str) {
    $str = trim($str);
    $str = preg_replace('/[[:space:]]{2,}/', ' ', $str);
    return $str;
}

function redirect($str) {
    header("Location: " . url($str));
    die();
}

// Redirect the user if authenticated to the homepage or the specified url
function authRedirect($page = '/') {
    if(User::getAuthUser()) redirect($page);
}

// Redirects the user if not authenticated
function notAuthRedirect($page = '/') {
    if(!User::getAuthUser()) redirect($page);
}
