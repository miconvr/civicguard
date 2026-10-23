@extends('legal.layout')
@section('title', 'Privacy Policy')
@section('updated', 'October 23, 2026')
@section('content')
<p>CivicGuard is an incident reporting system for Barangay Maimpis, City of San Fernando, Pampanga. This policy explains what personal data we collect and how we handle it, in line with the Data Privacy Act of 2012 (Republic Act 10173).</p>

<h2 class="mt-8 text-xl font-semibold">Who is responsible for your data</h2>
<p>Barangay Maimpis, City of San Fernando, Pampanga, is the personal information controller for the data collected through CivicGuard.</p>

<h2 class="mt-8 text-xl font-semibold">What we collect</h2>
<ul class="list-disc space-y-1 pl-6">
    <li>Account details: your name, email address, and a hashed password. Phone number and address if you add them.</li>
    <li>Reports you file: description, category, location text, an optional GPS pin, and an optional photo. Photos may contain embedded location or device metadata; avoid attaching images with details you do not want barangay staff to receive.</li>
    <li>Chat messages you send to the AI Chatbot.</li>
    <li>Activity records: actions such as filing, assigning, or resolving a report, kept in an audit log.</li>
    <li>For a random 10% sample of visits to the main CivicGuard interface, basic browser timing measurements (time to first byte, largest contentful paint, interaction delay, and layout shift) and a broad page group. These measurements do not include your account, the page URL, report contents, or form entries.</li>
    <li>For barangay tanods only: curfew records about minors, which staff enter themselves.</li>
    <li>If you request resident verification: an image showing your name and Maimpis residence, plus the time you agreed to its review. Please cover ID numbers, QR codes, birth dates, and unrelated details.</li>
</ul>

<h2 class="mt-8 text-xl font-semibold">Why we collect it</h2>
<p>To let you report incidents, let barangay staff review and respond, show you the status of your reports, and keep an accountable record of how reports are handled.</p>
<p>Resident verification is optional. We use the submitted image only to decide whether to show a Maimpis resident badge. Admins and barangay officials can review it, and we delete it as soon as a decision is made. The result, consent time, and review record remain while your account exists.</p>

<h2 class="mt-8 text-xl font-semibold">Who can see it</h2>
<p>Your reports are visible to barangay officials and administrators. Tanods see only the reports assigned to them and curfew records they logged themselves. Other residents cannot see your reports. Only admins and barangay officials can view a pending residency proof image; it is deleted after a decision.</p>

<h2 class="mt-8 text-xl font-semibold">Services we use</h2>
<ul class="list-disc space-y-1 pl-6">
    <li>AI providers (Google Gemini and Groq): the AI Chatbot, category suggestions, and severity ratings send the text you write (chat messages and report descriptions) to one or both of these providers for processing. If one is unavailable, the other is used. Do not include information you do not want shared this way.</li>
    <li>Wasmer: hosts the website and its database.</li>
    <li>CivicGuard serves the Figtree and Bitter typefaces from its own website; your browser does not need to contact a separate font provider.</li>
</ul>
<p>We do not sell your data or use it for advertising.</p>

<h2 class="mt-8 text-xl font-semibold">How long we keep it</h2>
<p>Reports and audit log entries are kept for as long as the barangay needs them for its records and to handle incidents. If you want to know how long a particular record is kept, ask the barangay using the contact details below.</p>

<h2 class="mt-8 text-xl font-semibold">Your rights</h2>
<p>You may ask to see the data we hold about you, correct it, object to its processing, or have it deleted, and you may complain to the National Privacy Commission. Residents can delete their own account from Profile, then Delete Account. Deleting your account also deletes the reports you filed, their photos, and any stored chatbot messages linked to your account. Entries in the audit log are kept without your name attached. Staff accounts are removed by the barangay administrator on request.</p>

<h2 class="mt-8 text-xl font-semibold">Security</h2>
<p>Passwords are stored hashed, access is limited by role, and the site uses HTTPS. No system is perfectly secure, so please use a strong password.</p>

<h2 class="mt-8 text-xl font-semibold">Contact</h2>
<p>For privacy questions or requests, contact:</p>
<ul class="list-disc space-y-1 pl-6">
    <li>Sangguniang Kabataan: <a class="underline" href="mailto:skbarangaymaimpiscsfp@gmail.com">skbarangaymaimpiscsfp@gmail.com</a></li>
    <li>Barangay Maimpis hall: <a class="underline" href="tel:+63453002629">(045) 300 2629</a></li>
</ul>
@endsection
