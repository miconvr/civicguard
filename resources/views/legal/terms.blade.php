@extends('legal.layout')
@section('title', 'Terms of Use')
@section('updated', 'DRAFT - set date before publishing')
@section('content')
<p>By creating a CivicGuard account you agree to these terms.</p>

<h2 class="mt-8 text-xl font-semibold">Not an emergency service</h2>
<p>CivicGuard reports are reviewed by staff and are not monitored in real time. In an emergency, call 911 or contact the barangay directly.</p>

<h2 class="mt-8 text-xl font-semibold">Using CivicGuard</h2>
<ul class="list-disc space-y-1 pl-6">
    <li>Give accurate information. Do not file false, prank, or abusive reports.</li>
    <li>Do not upload photos of people without a reason related to the incident, or any unlawful content.</li>
    <li>Keep your login private. You are responsible for activity on your account.</li>
</ul>

<h2 class="mt-8 text-xl font-semibold">What staff may do</h2>
<p>Barangay staff may review, reclassify, reject, or close reports, and may suspend accounts that misuse the system. Severity ratings and category suggestions may come from AI and are not a verified judgment. Staff decide how to respond.</p>

<h2 class="mt-8 text-xl font-semibold">No guarantee of outcome</h2>
<p>Filing a report does not guarantee a particular response or timeline. The service is provided as is and may be unavailable at times.</p>

<h2 class="mt-8 text-xl font-semibold">Your data</h2>
<p>How we handle your personal data is described in the <a class="underline" href="{{ route('privacy') }}">Privacy Policy</a>.</p>

<h2 class="mt-8 text-xl font-semibold">Changes and governing law</h2>
<p>We may update these terms and will post the new version here. These terms are governed by the laws of the Republic of the Philippines.</p>
@endsection
