<?php

namespace App\Repositories;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\PlanFeature;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Yajra\DataTables\Exceptions\Exception;

class PlanRepository extends BaseRepository
{
    /**
     * @var array
     */
    public $fieldSearchable = [
        'name',
    ];

    /**
     * {@inheritDoc}
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * {@inheritDoc}
     */
    public function model()
    {
        return Plan::class;
    }

    /**
     * @return mixed
     */
    public function store($input)
    {
        try {
            DB::beginTransaction();

            $input['trial_days'] = $input['trial_days'] != null ? $input['trial_days'] : 0;
            $input['price'] = removeCommaFromNumbers($input['price']);

            $plan = Plan::create($input);
            $plan->templates()->sync($input['template_ids']);

            $input['dynamic_vcard'] = (in_array(22, $input['template_ids']) && isset($input['dynamic_vcard'])) ? $input['dynamic_vcard'] : 0;
            $input['plan_id'] = $plan->id;
            PlanFeature::create($input);

            DB::commit();

            return $plan;
        } catch (Exception $e) {
            DB::rollBack();

            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @return array|Builder|Builder[]|Collection|Model
     */
    public function update($input, $id)
    {
        try {
            DB::beginTransaction();

            $plan = Plan::findOrFail($id);
            $input['trial_days'] = $input['trial_days'] != null ? $input['trial_days'] : 0;
            $input['price'] = removeCommaFromNumbers($input['price']);

            $plan->update($input);
            $plan->templates()->sync($input['template_ids']);

            $input['dynamic_vcard'] = (in_array(22, $input['template_ids']) && isset($input['dynamic_vcard'])) ? $input['dynamic_vcard'] : 0;
            $input['plan_id'] = $id;
            $input = $this->setFeatureValue($input);

            $input['custom_qrcode'] = isset($input['custom_qrcode']) ? 1 : 0;

            $input['order_nfc_card'] = isset($input['order_nfc_card']) ? 1 : 0;

            $input['insta_embed'] = isset($input['insta_embed']) ? 1 : 0;

            $input['iframes'] = isset($input['iframes']) ? 1 : 0;

            Subscription::where('plan_id', $id)->update(['no_of_vcards' => $input['no_of_vcards']]);

            $feature = PlanFeature::wherePlanId($id)->firstOrFail();

            $feature->update($input);

            DB::commit();

            return $input;
        } catch (Exception $e) {
            DB::rollBack();

            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @return mixed
     */
    public function setFeatureValue($input)
    {
        $input['products_services'] = isset($input['products_services']);
        $input['gallery'] = isset($input['gallery']);
        $input['testimonials'] = isset($input['testimonials']);
        $input['hide_branding'] = isset($input['hide_branding']);
        $input['enquiry_form'] = isset($input['enquiry_form']);
        $input['social_links'] = isset($input['social_links']);
        $input['analytics'] = isset($input['analytics']);
        $input['password'] = isset($input['password']);
        $input['custom_css'] = isset($input['custom_css']);
        $input['custom_js'] = isset($input['custom_js']);
        $input['custom_fonts'] = isset($input['custom_fonts']);
        $input['products'] = isset($input['products']);
        $input['appointments'] = isset($input['appointments']);
        $input['seo'] = isset($input['seo']);
        $input['blog'] = isset($input['blog']);
        $input['affiliation'] = isset($input['affiliation']);

        return $input;
    }
}
