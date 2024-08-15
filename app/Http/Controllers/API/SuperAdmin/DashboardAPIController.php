<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\AppBaseController;
use App\Models\Role;
use App\Models\User;
use App\Models\Vcard;
use App\Repositories\DashboardRepository;

class DashboardAPIController extends AppBaseController
{
    /* @var DashboardRepository */
    private DashboardRepository $dashboardRepository;

    public function __construct(DashboardRepository $dashboardRepo)
    {
        $this->dashboardRepository = $dashboardRepo;
    }

    public function index()
    {
        $data['activeUsersCount'] = User::whereHas('roles', function ($q) {
            $q->where('name', '!=', 'super_admin');
        })->where('is_active', 1)->count();

        $data['deActiveUsersCount'] = User::whereHas('roles', function ($q) {
            $q->where('name', '!=', 'super_admin');
        })->where('is_active', 0)->count();

        if (Auth()->user()->hasRole(Role::ROLE_SUPER_ADMIN)) {
            $data['activeVcard'] = Vcard::whereStatus(1)->count();
            $data['deActiveVcard'] = Vcard::whereStatus(0)->count();

            return $this->sendResponse($data, 'Super Admin Dashboard retrieve Successfully');
        }

        $data['activeVcard'] = Vcard::whereTenantId(auth()->user()->tenant_id)->get();
        $data['deActiveVcard'] = Vcard::whereTenantId(auth()->user()->tenant_id)->whereStatus(0)->count();
        return $this->sendResponse($data, 'Super Admin Dashboard retrieve Successfully');
    }

    public function incomeChartData()
    {
        $data = $this->dashboardRepository->incomeChartData();

        return $this->sendResponse($data, 'Income chart data fetch successfully.');
    }
}
