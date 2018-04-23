<?php

/*
 * This file is part of Flarum.
 *
 * (c) Toby Zerner <toby.zerner@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Flarum\Auth\Facebook\FacebookAuthController;
use Flarum\Extend;

return [
    (new Extend\Assets('forum'))
        ->js(__DIR__.'/js/forum/dist/main.js')
        ->asset(__DIR__.'/less/forum/extension.less'),

    (new Extend\Assets('admin'))
        ->js(__DIR__.'/js/admin/dist/main.js'),

    (new Extend\Routes('forum'))
        ->get('/auth/facebook', 'auth.facebook', FacebookAuthController::class),
];
