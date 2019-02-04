<?php

Route::domain(config('pixelfed.domain.admin'))->prefix('i/admin')->group(function () {
    Route::redirect('/', '/dashboard');
    Route::redirect('timeline', config('app.url').'/timeline');
    Route::get('dashboard', 'AdminController@home')->name('admin.home');
    Route::get('reports', 'AdminController@reports')->name('admin.reports');
    Route::get('reports/show/{id}', 'AdminController@showReport');
    Route::post('reports/show/{id}', 'AdminController@updateReport');
    Route::post('reports/bulk', 'AdminController@bulkUpdateReport');
    Route::redirect('statuses', '/statuses/list');
    Route::get('statuses/list', 'AdminController@statuses')->name('admin.statuses');
    Route::get('statuses/show/{id}', 'AdminController@showStatus');
    Route::redirect('users', '/users/list');
    Route::get('users/list', 'AdminController@users')->name('admin.users');
    Route::get('users/edit/{id}', 'AdminController@editUser');
    Route::redirect('media', '/media/list');
    Route::get('media/list', 'AdminController@media')->name('admin.media');
});

Route::domain(config('pixelfed.domain.app'))->middleware(['validemail', 'twofactor', 'localization'])->group(function () {
    Route::get('/', 'SiteController@home')->name('timeline.personal');
    Route::post('/', 'StatusController@store');

    Auth::routes();

    Route::get('.well-known/webfinger', 'FederationController@webfinger')->name('well-known.webfinger');
    Route::get('.well-known/nodeinfo', 'FederationController@nodeinfoWellKnown')->name('well-known.nodeinfo');
    Route::get('.well-known/host-meta', 'FederationController@hostMeta')->name('well-known.hostMeta');

    Route::get('/home', 'HomeController@index')->name('home');

    Route::get('discover', 'DiscoverController@home')->name('discover');

    Route::group(['prefix' => 'api'], function () {
        Route::get('search/{tag}', 'SearchController@searchAPI')
          ->where('tag', '[A-Za-z0-9]+');
        Route::get('nodeinfo/2.0.json', 'FederationController@nodeinfo');

        Route::group(['prefix' => 'v1'], function () {
            Route::get('accounts/verify_credentials', 'ApiController@verifyCredentials');
            Route::post('avatar/update', 'ApiController@avatarUpdate');
            Route::get('likes', 'ApiController@hydrateLikes');
            Route::post('media', 'ApiController@uploadMedia');
            Route::get('notifications', 'ApiController@notifications');
            Route::get('timelines/public', 'PublicApiController@publicTimelineApi');
            Route::get('timelines/home', 'PublicApiController@homeTimelineApi');
        });
        Route::group(['prefix' => 'v2'], function() {
            Route::get('discover', 'InternalApiController@discover');
            Route::get('discover/posts', 'InternalApiController@discoverPosts');
            Route::get('profile/{username}/status/{postid}', 'PublicApiController@status');
            Route::get('comments/{username}/status/{postId}', 'PublicApiController@statusComments');
            Route::get('likes/profile/{username}/status/{id}', 'PublicApiController@statusLikes');
            Route::get('shares/profile/{username}/status/{id}', 'PublicApiController@statusShares');
            Route::get('status/{id}/replies', 'InternalApiController@statusReplies');
        });
        Route::group(['prefix' => 'local'], function () {
            Route::get('i/follow-suggestions', 'ApiController@followSuggestions');
            Route::post('i/more-comments', 'ApiController@loadMoreComments');
            Route::post('status/compose', 'InternalApiController@compose');
        });
    });

    Route::get('discover/tags/{hashtag}', 'DiscoverController@showTags');

    Route::group(['prefix' => 'i'], function () {
        Route::redirect('/', '/');
        Route::get('compose', 'StatusController@compose')->name('compose');
        Route::post('comment', 'CommentController@store');
        Route::post('delete', 'StatusController@delete');
        Route::post('mute', 'AccountController@mute');
        Route::post('block', 'AccountController@block');
        Route::post('like', 'LikeController@store');
        Route::post('share', 'StatusController@storeShare');
        Route::post('follow', 'FollowerController@store');
        Route::post('bookmark', 'BookmarkController@store');
        Route::get('lang/{locale}', 'SiteController@changeLocale');
        Route::get('restored', 'AccountController@accountRestored');

        Route::get('verify-email', 'AccountController@verifyEmail');
        Route::post('verify-email', 'AccountController@sendVerifyEmail');
        Route::get('confirm-email/{userToken}/{randomToken}', 'AccountController@confirmVerifyEmail');

        Route::get('auth/sudo', 'AccountController@sudoMode');
        Route::post('auth/sudo', 'AccountController@sudoModeVerify');
        Route::get('auth/checkpoint', 'AccountController@twoFactorCheckpoint');
        Route::post('auth/checkpoint', 'AccountController@twoFactorVerify');

        Route::get('media/preview/{profileId}/{mediaId}', 'ApiController@showTempMedia')->name('temp-media');


        Route::group(['prefix' => 'report'], function () {
            Route::get('/', 'ReportController@showForm')->name('report.form');
            Route::post('/', 'ReportController@formStore');
            Route::get('not-interested', 'ReportController@notInterestedForm')->name('report.not-interested');
            Route::get('spam', 'ReportController@spamForm')->name('report.spam');
            Route::get('spam/comment', 'ReportController@spamCommentForm')->name('report.spam.comment');
            Route::get('spam/post', 'ReportController@spamPostForm')->name('report.spam.post');
            Route::get('spam/profile', 'ReportController@spamProfileForm')->name('report.spam.profile');
            Route::get('sensitive/comment', 'ReportController@sensitiveCommentForm')->name('report.sensitive.comment');
            Route::get('sensitive/post', 'ReportController@sensitivePostForm')->name('report.sensitive.post');
            Route::get('sensitive/profile', 'ReportController@sensitiveProfileForm')->name('report.sensitive.profile');
            Route::get('abusive/comment', 'ReportController@abusiveCommentForm')->name('report.abusive.comment');
            Route::get('abusive/post', 'ReportController@abusivePostForm')->name('report.abusive.post');
            Route::get('abusive/profile', 'ReportController@abusiveProfileForm')->name('report.abusive.profile');
        });
    });

    Route::group(['prefix' => 'account'], function () {
        Route::redirect('/', '/');
        Route::get('activity', 'AccountController@notifications')->name('notifications');
        Route::get('follow-requests', 'AccountController@followRequests')->name('follow-requests');
        Route::post('follow-requests', 'AccountController@followRequestHandle');
    });

    Route::group(['prefix' => 'settings'], function () {
        Route::redirect('/', '/settings/home');
        Route::get('home', 'SettingsController@home')
        ->name('settings');
        Route::post('home', 'SettingsController@homeUpdate');
        Route::get('avatar', 'SettingsController@avatar')->name('settings.avatar');
        Route::post('avatar', 'AvatarController@store');
        Route::get('password', 'SettingsController@password')->name('settings.password')->middleware('dangerzone');
        Route::post('password', 'SettingsController@passwordUpdate')->middleware('dangerzone');
        Route::get('email', 'SettingsController@email')->name('settings.email');
        Route::get('notifications', 'SettingsController@notifications')->name('settings.notifications');
        Route::get('privacy', 'SettingsController@privacy')->name('settings.privacy');
        Route::post('privacy', 'SettingsController@privacyStore');
        Route::get('privacy/muted-users', 'SettingsController@mutedUsers')->name('settings.privacy.muted-users');
        Route::post('privacy/muted-users', 'SettingsController@mutedUsersUpdate');
        Route::get('privacy/blocked-users', 'SettingsController@blockedUsers')->name('settings.privacy.blocked-users');
        Route::post('privacy/blocked-users', 'SettingsController@blockedUsersUpdate');
        Route::get('privacy/blocked-instances', 'SettingsController@blockedInstances')->name('settings.privacy.blocked-instances');

        // Todo: Release in 0.7.2
        Route::group(['prefix' => 'remove', 'middleware' => 'dangerzone'], function() {
            Route::get('request/temporary', 'SettingsController@removeAccountTemporary')->name('settings.remove.temporary');
            Route::post('request/temporary', 'SettingsController@removeAccountTemporarySubmit');
            Route::get('request/permanent', 'SettingsController@removeAccountPermanent')->name('settings.remove.permanent');
            Route::post('request/permanent', 'SettingsController@removeAccountPermanentSubmit');
        });

        Route::group(['prefix' => 'security', 'middleware' => 'dangerzone'], function() {
            Route::get(
                '/', 
                'SettingsController@security'
            )->name('settings.security');
            Route::get(
                '2fa/setup', 
                'SettingsController@securityTwoFactorSetup'
            )->name('settings.security.2fa.setup');
            Route::post(
                '2fa/setup', 
                'SettingsController@securityTwoFactorSetupStore'
            );
            Route::get(
                '2fa/edit', 
                'SettingsController@securityTwoFactorEdit'
            )->name('settings.security.2fa.edit');
            Route::post(
                '2fa/edit', 
                'SettingsController@securityTwoFactorUpdate'
            );
            Route::get(
                '2fa/recovery-codes',
                'SettingsController@securityTwoFactorRecoveryCodes'
            )->name('settings.security.2fa.recovery');
            Route::post(
                '2fa/recovery-codes',
                'SettingsController@securityTwoFactorRecoveryCodesRegenerate'
            );
        });

        Route::get('applications', 'SettingsController@applications')->name('settings.applications');
        Route::get('data-export', 'SettingsController@dataExport')->name('settings.dataexport');
        Route::get('developers', 'SettingsController@developers')->name('settings.developers');
    });

    Route::group(['prefix' => 'site'], function () {
        Route::redirect('/', '/');
        Route::get('about', 'SiteController@about')->name('site.about');
        Route::view('help', 'site.help')->name('site.help');
        Route::view('developer-api', 'site.developer')->name('site.developers');
        Route::view('fediverse', 'site.fediverse')->name('site.fediverse');
        Route::view('open-source', 'site.opensource')->name('site.opensource');
        Route::view('banned-instances', 'site.bannedinstances')->name('site.bannedinstances');
        Route::view('terms', 'site.terms')->name('site.terms');
        Route::view('privacy', 'site.privacy')->name('site.privacy');
        Route::view('platform', 'site.platform')->name('site.platform');
        Route::view('language', 'site.language')->name('site.language');

        Route::group(['prefix'=>'kb'], function() {
            Route::view('getting-started', 'site.help.getting-started')->name('help.getting-started');
            Route::view('sharing-media', 'site.help.sharing-media')->name('help.sharing-media');
            Route::view('your-profile', 'site.help.your-profile')->name('help.your-profile');
            Route::view('stories', 'site.help.stories')->name('help.stories');
            Route::view('embed', 'site.help.embed')->name('help.embed');
            Route::view('hashtags', 'site.help.hashtags')->name('help.hashtags');
            Route::view('discover', 'site.help.discover')->name('help.discover');
            Route::view('direct-messages', 'site.help.dm')->name('help.dm');
            Route::view('timelines', 'site.help.timelines')->name('help.timelines');
            Route::view('what-is-the-fediverse', 'site.help.what-is-fediverse')->name('help.what-is-fediverse');
            Route::view('safety-tips', 'site.help.safety-tips')->name('help.safety-tips');

            Route::view('community-guidelines', 'site.help.community-guidelines')->name('help.community-guidelines');
            Route::view('controlling-visibility', 'site.help.controlling-visibility')->name('help.controlling-visibility');
            Route::view('abusive-activity', 'site.help.abusive-activity')->name('help.abusive-activity');
            Route::view('blocking-accounts', 'site.help.blocking-accounts')->name('help.blocking-accounts');
            Route::view('report-something', 'site.help.report-something')->name('help.report-something');
            Route::view('data-policy', 'site.help.data-policy')->name('help.data-policy');
        });
    });

    Route::group(['prefix' => 'timeline'], function () {
        Route::redirect('/', '/');
        Route::get('public', 'TimelineController@local')->name('timeline.public');
        Route::post('public', 'StatusController@store');
    });

    Route::group(['prefix' => 'users'], function () {
        Route::redirect('/', '/');
        Route::get('{user}.atom', 'ProfileController@showAtomFeed');
        Route::get('{username}/outbox', 'FederationController@userOutbox');
        Route::get('{username}', 'ProfileController@permalinkRedirect');
        Route::get('{username}/followers', 'FederationController@userFollowers');
        Route::get('{username}/following', 'FederationController@userFollowing');
    });

    Route::get('p/{username}/{id}/c/{cid}', 'CommentController@show');
    Route::get('p/{username}/{id}/c', 'CommentController@showAll');
    Route::get('p/{username}/{id}/edit', 'StatusController@edit');
    Route::post('p/{username}/{id}/edit', 'StatusController@editStore');
    Route::get('p/{username}/{id}', 'StatusController@show');
    Route::get('{username}/saved', 'ProfileController@savedBookmarks');
    Route::get('{username}/followers', 'ProfileController@followers')->middleware('auth');
    Route::get('{username}/following', 'ProfileController@following')->middleware('auth');
    Route::get('{username}', 'ProfileController@show');
});
