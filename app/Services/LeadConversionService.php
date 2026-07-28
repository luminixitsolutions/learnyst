<?php

namespace App\Services;

use App\Models\CourseEnrollment;
use App\Models\Lead;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LeadConversionService
{
    public function convert(Lead $lead, ?int $courseId = null, ?User $performedBy = null): User
    {
        if ($lead->isConverted()) {
            throw ValidationException::withMessages(['lead' => 'Lead is already converted.']);
        }

        $learnerRole = Role::where('slug', 'learner')->firstOrFail();
        $courseId = $courseId ?: $lead->course_id;

        return DB::transaction(function () use ($lead, $learnerRole, $courseId, $performedBy) {
            $user = User::withTrashed()->where('email', $lead->email)->first();

            if ($user) {
                if ($user->trashed()) {
                    $user->restore();
                }
                if (! $user->role_id) {
                    $user->update(['role_id' => $learnerRole->id]);
                }
            } else {
                $user = User::create([
                    'name' => $lead->name,
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'role_id' => $learnerRole->id,
                    'password' => Hash::make(Str::password(12)),
                    'created_by' => $lead->created_by ?: $performedBy?->id,
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'notes' => 'Converted from lead #'.$lead->id,
                ]);
            }

            if ($courseId) {
                CourseEnrollment::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'course_id' => $courseId,
                        'enrollment_type' => 'course',
                    ],
                    [
                        'status' => 'active',
                        'access_type' => 'free',
                        'enrolled_at' => now(),
                        'access_starts_at' => now(),
                    ]
                );
            }

            $lead->update([
                'status' => 'converted',
                'stage' => 'admitted',
                'converted_user_id' => $user->id,
                'converted_at' => now(),
                'course_id' => $courseId ?: $lead->course_id,
            ]);

            \App\Services\ActivityLogger::log('lead_converted', "Lead {$lead->name} converted to learner {$user->email}", $lead);

            return $user;
        });
    }
}
