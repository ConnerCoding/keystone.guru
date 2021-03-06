<?php

namespace App\Service\Dashboard;

use App\Models\DungeonRoute;
use App\Models\PageView;
use App\Models\Team;
use App\User;
use Carbon\Carbon;

/**
 *
 * @package App\Service
 * @author Wouter
 * @since 13/06/2019
 */
class DashboardService implements DashboardServiceInterface
{
    /**
     * @return mixed
     */
    function getTopCardsData()
    {
        return [
            'users' => User::count(),
            'usersLastWeek' => User::whereDate('created_at', '>=', Carbon::now()->subWeek())->count(),
            'routes' => sprintf('%s visible, %s unpublished, %s unlisted',
                DungeonRoute::visible()->count(),
                DungeonRoute::where('published', false)->where('demo', false)->count(),
                DungeonRoute::where('unlisted', true)->where('demo', false)->count()
                ),
            'routesLastWeek' => DungeonRoute::whereDate('created_at', '>=', Carbon::now()->subWeek())->count(),
            'teams' => Team::count(),
            'teamsLastWeek' => Team::whereDate('created_at', '>=', Carbon::now()->subWeek())->count(),
            'pageViews' => PageView::count(),
            'pageViewsLastWeek' => PageView::whereDate('created_at', '>=', Carbon::now()->subWeek())->count(),
        ];
    }

}