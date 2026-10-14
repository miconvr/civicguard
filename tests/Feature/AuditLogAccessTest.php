<?php

use App\Models\User;

test('tanods cannot open the audit logs or export them', function () {
    $tanod = User::factory()->create(['role' => 'tanod']);

    $this->actingAs($tanod)->get(route('admin.auditLogs'))->assertForbidden();
    $this->actingAs($tanod)->get(route('admin.auditLogs.exportPdf'))->assertForbidden();
});

test('officials can open the audit logs', function () {
    $official = User::factory()->create(['role' => 'official']);

    $this->actingAs($official)->get(route('admin.auditLogs'))->assertSuccessful();
});
