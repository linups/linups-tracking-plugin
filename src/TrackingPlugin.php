<?php
namespace Linups\TrackingPlugin;

use Illuminate\Support\Facades\Http;

class TrackingPlugin {
    protected $postData = array();

    public function __construct() {
        $this->postData['project_id'] = config('tracking-plugin-config.tracking_project_id');
    }

    public function action(string $type, array $actionData = null) {
        $this->postData['action'] = $type;
        if(isset($actionData)) $this->postData['actionData'][] = $actionData;
        return $this;
    }

    public function slug(string $slug) {
        $this->postData['slug'] = $slug;
        return $this;
    }

    public function slugVersion(string $slugVersion) {
        $this->postData['slugVersion'] = $slugVersion;
        return $this;
    }

    public function submit():void {
        try {
            $this->gatherAdditionalData();
            Http::post(config('tracking-plugin-config.tracking_raw_endpoint').'/api/v1/tracking-raw',
                $this->postData
            );

            //--- Temporary for testing
            Http::post('https://tracking.thewatkinsmethod.com/api/v1/tracking-raw',
                $this->postData
            );

        } catch (\Throwable $ex) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($ex);
            }
            
            \Log::debug('TrackingRawService error: '.$ex->getMessage());
        }
    }

    private function gatherAdditionalData() {
        $this->postData['request_url'] = request()->url();
        $this->postData['query'] = explode('?', request()->fullUrl())[1] ?? '';
        $this->postData['ip'] = request()->ip();
        $this->postData['user_agent'] = request()->header('user-agent');
    }
}