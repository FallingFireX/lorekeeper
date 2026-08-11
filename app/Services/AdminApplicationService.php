<?php

namespace App\Services;

use App\Facades\Notifications;
use App\Facades\Settings;
use App\Models\Submission\AdminApplication;
use App\Models\User\User;
use Auth;
use Illuminate\Support\Facades\DB;

class AdminApplicationService extends Service {
    /**
     * Accept a applicant and notify them.
     *
     * @param mixed $user
     * @param mixed $application
     */
    public function acceptApplication($application, $user) {
        DB::beginTransaction();

        try {
            if (Auth::check()) {
                $user = Auth::user()->id;
            }

            $saveData = [
                'admin_id'      => $affiliate->staff_id ?? $user,
                'status'        => isset($data['status']) ? parse($data['status']) : null,
            ];

            $application->update($saveData);

            return $this->commitReturn($application);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * accept an applicant and notify them.
     *
     * @param mixed $data
     * @param mixed $user
     */
    public function acceptApplicant($data, $user) {
        DB::beginTransaction();

        try {
            $applicant = AdminApplication::with('team')->find($data['id']);

            $saveData = [
                'admin_id' => $applicant->admin_id ?? $user,
                'status'   => 'accepted',
            ];

            $applicant->update($saveData);

            $recipient = User::find($applicant->user_id);
            if ($recipient && Settings::get('notify_staff_applicants') == 1) {
                $teamName = optional($applicant->team)->name ?? 'Unknown Team';
                Notifications::create('APPLICATION_ACCEPTED', $recipient, [
                    'admin_name' => Auth::user()->name,
                    'team_name'  => $teamName,
                    'url'        => url('applications/'.$applicant->id),
                ]);
            }

            return $this->commitReturn($applicant);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Reject an applicant and notify them.
     *
     * @param mixed $data
     * @param mixed $user
     */
    public function rejectApplicant($data, $user) {
        DB::beginTransaction();

        try {
            $applicant = AdminApplication::with('team')->find($data['id']);

            $saveData = [
                'admin_id' => $applicant->admin_id ?? $user,
                'status'   => 'denied',
            ];

            $applicant->update($saveData);

            $recipient = User::find($applicant->user_id);
            if ($recipient && Settings::get('notify_staff_applicants') == 1) {
                $teamName = optional($applicant->team)->name ?? 'Unknown Team';
                Notifications::create('APPLICATION_ACCEPTED', $recipient, [
                    'admin_name' => Auth::user()->name,
                    'team_name'  => $teamName,
                    'url'        => url('applications/'.$applicant->id),
                ]);
            }

            return $this->commitReturn($applicant);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }
}
