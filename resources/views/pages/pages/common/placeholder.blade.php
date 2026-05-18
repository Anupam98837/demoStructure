@extends('pages.layout.structure')

@section('title', $pageTitle ?? 'Coming Soon')

@section('content')
<div class="card border-0 shadow-1" style="max-width:960px;border-radius:24px;">
  <div class="card-body p-4 p-lg-5">
    <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill mb-3" style="background:var(--surface-2);border:1px solid var(--line-strong);color:var(--primary-color);font-weight:700;">
      <i class="{{ $pageIcon ?? 'fa-solid fa-screwdriver-wrench' }}"></i>
      <span>{{ $pageTitle ?? 'Coming Soon' }}</span>
    </div>
    <h1 class="mb-3">{{ $pageTitle ?? 'Coming Soon' }}</h1>
    <p class="text-muted mb-0" style="line-height:1.8;">
      {{ $pageLead ?? 'This page is ready to be connected to the new attendance-system workflow.' }}
    </p>
  </div>
</div>
@endsection
