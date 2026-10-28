<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }

    public function getLocalizedActionAttribute(): string
    {
        return match ($this->action) {
            'report_resolved' => __('Report resolved'),
            'report_status_changed' => __('Status changed'),
            'report_confirmed_fixed' => __('Confirmed fixed'),
            'report_assigned' => __('Report assigned'),
            'resident_verified' => __('Resident verified'),
            'residence_verification_rejected' => __('Residence verification rejected'),
            'report_severity_changed' => __('Severity changed'),
            'report_submitted' => __('Report submitted'),
            'report_reopened' => __('Report reopened'),
            'report_deleted' => __('Report deleted'),
            'staff_created' => __('Staff account created'),
            'resident_password_reset' => __('Resident password reset'),
            'curfew_logged' => __('Curfew violation logged'),
            default => __(ucwords(str_replace('_', ' ', $this->action))),
        };
    }

    public function getLocalizedDescriptionAttribute(): ?string
    {
        $metadata = $this->metadata ?? [];
        $description = $this->description;

        return match ($this->action) {
            'report_status_changed', 'report_resolved' => __('Report status changed to :status.', [
                'status' => __(ucfirst(str_replace('_', ' ', $metadata['status'] ?? $this->legacyStatus($description) ?? ''))),
            ]),
            'report_severity_changed' => __('Severity changed from :from to :to.', [
                'from' => __(ucfirst($metadata['from'] ?? $this->legacySeverity($description)[0] ?? '')),
                'to' => __(ucfirst($metadata['to'] ?? $this->legacySeverity($description)[1] ?? '')),
            ]),
            'report_assigned' => __('Report assigned to :name.', [
                'name' => $metadata['assigned_name'] ?? $this->legacyAssignee($description) ?? '',
            ]),
            'report_submitted' => __('Incident report submitted. Severity rated by :source.', [
                'source' => ($metadata['severity_source'] ?? $this->legacySeveritySource($description)) === 'keyword rules'
                    ? __('automated rules')
                    : ($metadata['severity_source'] ?? $this->legacySeveritySource($description) ?? __('automated rules')),
            ]),
            'report_confirmed_fixed' => __('Resident confirmed the issue was fixed.'),
            'report_reopened' => __('Resident said the issue is not fixed. Report reopened.'),
            'report_deleted' => __('Resident removed pending incident report #:id.', ['id' => $this->auditable_id]),
            'resident_verified' => $this->legacyVerificationDescription($description, true) ?? __('Residence verification approved.'),
            'residence_verification_rejected' => $this->legacyVerificationDescription($description, false) ?? __('Residence verification declined.'),
            'staff_created' => __('Staff account created.'),
            'resident_password_reset' => __('Resident password reset by authorized staff.'),
            'curfew_logged' => __('Curfew violation logged.'),
            default => $description ? __($description) : null,
        };
    }

    private function legacyStatus(?string $description): ?string
    {
        return preg_match('/^Report status changed to (.+)\.$/', $description ?? '', $matches) ? $matches[1] : null;
    }

    private function legacySeverity(?string $description): array
    {
        return preg_match('/^Severity changed from (.+) to (.+)\.$/', $description ?? '', $matches)
            ? [$matches[1], $matches[2]]
            : [];
    }

    private function legacyAssignee(?string $description): ?string
    {
        return preg_match('/^Report assigned to (.+)\.$/', $description ?? '', $matches) ? $matches[1] : null;
    }

    private function legacySeveritySource(?string $description): ?string
    {
        return preg_match('/^Incident report submitted\. Severity rated by (.+)\.$/', $description ?? '', $matches)
            ? $matches[1]
            : null;
    }

    private function legacyVerificationDescription(?string $description, bool $approved): ?string
    {
        $verb = $approved ? 'approved' : 'declined';

        return preg_match('/^Residence verification '.$verb.' for (.+) \((.+)\)\.$/', $description ?? '', $matches)
            ? __('Residence verification '.($approved ? 'approved' : 'declined').' for :name (:email).', [
                'name' => $matches[1],
                'email' => $matches[2],
            ])
            : null;
    }
}
