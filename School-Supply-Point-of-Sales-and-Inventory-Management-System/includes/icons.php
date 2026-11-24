<?php
function icon($name)
{
    $icons = array(
        'dashboard' => '<path d="m3 10 9-7 9 7"></path><path d="M5 9v11h14V9M9 20v-6h6v6"></path>',
        'products' => '<path d="M4 5.5 12 2l8 3.5v13L12 22l-8-3.5z"></path><path d="M4 5.5 12 9l8-3.5M12 9v13"></path>',
        'inventory' => '<path d="M4 4h16v6H4zM4 14h16v6H4z"></path><path d="M8 7h.01M8 17h.01"></path>',
        'pos' => '<rect x="4" y="3" width="16" height="18" rx="2"></rect><path d="M8 7h8M8 11h2m4 0h2m-8 4h2m4 0h2m-8 3h2m4 0h2"></path>',
        'transactions' => '<path d="M8 6h13M8 12h13M8 18h13"></path><path d="M3 6h.01M3 12h.01M3 18h.01"></path>',
        'reports' => '<path d="M4 20V10h4v10zM10 20V4h4v16zM16 20v-8h4v8z"></path>',
        'catalog' => '<path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v17H6.5A2.5 2.5 0 0 1 4 17.5z"></path><path d="M4 5.5v12M8 7h8M8 11h8"></path>',
        'profile' => '<circle cx="12" cy="8" r="3.5"></circle><path d="M5 20a7 7 0 0 1 14 0"></path>',
        'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="10" cy="7" r="4"></circle><path d="M20 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path>',
        'logout' => '<path d="M10 17l5-5-5-5M15 12H3"></path><path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7"></path>',
        'arrow' => '<path d="M5 12h14M13 6l6 6-6 6"></path>',
        'plus' => '<path d="M12 5v14M5 12h14"></path>',
        'cash' => '<rect x="3" y="5" width="18" height="14" rx="2"></rect><circle cx="12" cy="12" r="3"></circle><path d="M7 9h.01M17 15h.01"></path>',
        'warning' => '<path d="m12 3 9.5 17h-19z"></path><path d="M12 9v4m0 3h.01"></path>',
        'receipt' => '<path d="M5 3h14v18l-3-2-4 2-4-2-3 2z"></path><path d="M9 8h6M9 12h6M9 16h3"></path>',
        'check' => '<path d="m5 12 4 4L19 6"></path>',
        'close' => '<path d="m6 6 12 12M18 6 6 18"></path>'
    );

    if (!isset($icons[$name])) {
        return '';
    }

    return '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $icons[$name] . '</svg>';
}
