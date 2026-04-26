@php
    $seoTitle = config('app.name').' — Build your SaaS faster';
    $seoDescription = 'A reusable SaaS template with authentication, multi-tenant teams, Stripe billing and member invitations.';
    $seoUrl = url()->current();
@endphp

<meta name="description" content="{{ $seoDescription }}">
<link rel="canonical" href="{{ $seoUrl }}">

<meta property="og:type" content="website">
<meta property="og:locale" content="en_US">
<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $seoUrl }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
