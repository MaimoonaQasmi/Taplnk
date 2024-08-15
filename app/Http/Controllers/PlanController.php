<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\Subscription;
use App\Repositories\PlanRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Laracasts\Flash\Flash;

class PlanController extends AppBaseController
{
    /**
     * @var PlanRepository
     */
    private $planRepository;

    /**
     * PlanController constructor.
     */
    public function __construct(PlanRepository $planRepository)
    {
        $this->planRepository = $planRepository;
    }

    /**
     * @param  Request  $request
     * @return Application|Factory|View
     */
    public function index(): \Illuminate\View\View
    {
        return view('sadmin.plans.index');
    }

    /**
     * @return Application|Factory|View
     */
    public function create(): \Illuminate\View\View
    {
        return view('sadmin.plans.create');
    }

    /**
     * @return Application|RedirectResponse|Redirector
     */
    public function store(CreatePlanRequest $request): RedirectResponse
    {
        $input = $request->all();

        $this->planRepository->store($input);

        Flash::success(__('messages.flash.plan_create'));

        return redirect(route('plans.index'));
    }

    /**
     * @return Application|Factory|View
     */
    public function edit(Plan $plan): \Illuminate\View\View
    {
        $templates = $plan->templates()->pluck('template_id')->toArray();
        $feature = PlanFeature::wherePlanId($plan->id)->first();

        return view('sadmin.plans.edit', compact('plan', 'feature', 'templates'));
    }

    /**
     * @return Application|RedirectResponse|Redirector
     */
    public function update(UpdatePlanRequest $request, $id): RedirectResponse
    {
        $input = $request->all();

        $this->planRepository->update($input, $id);

        Flash::success(__('messages.flash.plan_update'));

        return redirect(route('plans.index'));
    }

    public function updateStatus(Plan $plan): JsonResponse
    {
        $plan->update([
            'is_active' => ! $plan->is_active,
        ]);

        return $this->sendSuccess(__('messages.flash.plan_status'));
    }

    public function updatePlanStatus(Plan $plan)
    {

        $plan->update([
            'status' => ! $plan->status,
        ]);

        return $this->sendSuccess(__('messages.flash.plan_status'));
    }

    /**
     * @return mixed
     */
    public function destroy(Plan $plan)
    {
        $subscription = Subscription::where('plan_id', $plan->id)->where('status', Subscription::ACTIVE)->count();

        if ($plan->is_default == Plan::IS_DEFAULT) {
            return $this->sendError(__('messages.placeholder.default_plan_can_not_be_delete'));
        }

        if ($subscription > 0) {
            return $this->sendError(__('messages.placeholder.plan_already_used'));
        }
        $plan->delete();

        return $this->sendSuccess('Plan deleted successfully.');
    }

    public function makePlanDefault(int $id): JsonResponse
    {
        return $this->sendError(__('messages.flash.not_allowed_record'));
        $defaultSubscriptionPlan = Plan::where('is_default', 1)->first();
        $defaultSubscriptionPlan->update(['is_default' => 0]);

        $subscriptionPlan = Plan::find($id);
        if (empty($subscriptionPlan)) {
            $defaultSubscriptionPlan->update(['is_default' => 1]);

            return $this->sendSuccess(__('messages.flash.plan_default'));
        }

        if ($subscriptionPlan->trial_days == 0) {
            $subscriptionPlan->trial_days = Plan::TRIAL_DAYS;
        }
        $subscriptionPlan->is_default = 1;
        $subscriptionPlan->save();

        return $this->sendSuccess(__('messages.flash.plan_default'));
    }
}
