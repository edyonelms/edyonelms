@php
    $stored   = $page?->metadata ?? [];
    $def      = config('website_pages.services', []);
    $tag      = $stored['tag']      ?? $def['tag']      ?? 'Our Services';
    $title    = $stored['title']    ?? $def['title']    ?? '';
    $subtitle = $stored['subtitle'] ?? $def['subtitle'] ?? '';
    $items    = !empty($stored['items']) ? $stored['items'] : ($def['items'] ?? []);
@endphp
@include('components.website.partials.head', ['title' => 'Services'])

  @include('components.website.header')

  {{-- ══════════════════ PAGE HEADER ══════════════════ --}}
  <section class="page-header">
    <div class="grid-bg"></div>
    <div class="page-header-content">
      <span class="section-tag tag-violet">⚙ {{ $tag }}</span>
      <h1 class="section-title">{{ $title }}</h1>
      <p class="section-subtitle">{{ $subtitle }}</p>
    </div>
  </section>

  {{-- ══════════════════ SERVICES GRID ══════════════════ --}}
  <section class="section">
    <div class="cards-grid">
      @foreach ($items as $item)
      <div class="feature-card">
        <div class="feature-icon-wrap">{{ $item['icon'] ?? '' }}</div>
        <h3 class="feature-title">{{ $item['title'] ?? '' }}</h3>
        <p class="feature-desc">{{ $item['desc'] ?? '' }}</p>
      </div>
      @endforeach
    </div>
  </section>

  {{-- ══════════════════ CTA ══════════════════ --}}
  <section class="cta-section">
    <div class="cta-bg"></div>
    <div class="cta-card">
      <h2 class="cta-title">Ready to digitise your <span class="gradient-text">school operations?</span></h2>
      <p class="cta-desc">Tell us what you need and we will tailor EDYONE LMS to fit your school.</p>
      <div class="cta-actions">
        <a href="{{ url('web/demo') }}" class="btn btn-primary btn-xl">Request a Demo</a>
        <a href="{{ url('web/pricing') }}" class="btn btn-outline btn-xl">View Pricing</a>
      </div>
    </div>
  </section>

  @include('components.website.app-section')
  @include('components.website.footer')
</body>
</html>
