@php
    $stored   = $page?->metadata ?? [];
    $def      = config('website_pages.become-executive', []);
    $tag      = $stored['tag']      ?? $def['tag']      ?? 'Partner Program';
    $title    = $stored['title']    ?? $def['title']    ?? '';
    $subtitle = $stored['subtitle'] ?? $def['subtitle'] ?? '';
    $benefits = !empty($stored['benefits']) ? $stored['benefits'] : ($def['benefits'] ?? []);
    $steps    = !empty($stored['steps'])    ? $stored['steps']    : ($def['steps'] ?? []);
@endphp
@include('components.website.partials.head', ['title' => 'Become an Executive'])

<style>
  /* ── Become-an-Executive specific ── */
  .steps-wrap { max-width: 880px; margin: 0 auto; display: grid; grid-template-columns: repeat(4, 1fr); gap: 1px; background: var(--border2); border: 1px solid var(--border2); border-radius: var(--radius-lg); overflow: hidden; }
  .step-card { background: #fff; padding: 28px 22px; text-align: center; }
  .step-num { width: 38px; height: 38px; margin: 0 auto 14px; border-radius: 50%; background: var(--grad1); color: #fff; font-weight: 700; display: flex; align-items: center; justify-content: center; }
  .step-title { font-size: 14px; font-weight: 600; color: var(--text); margin-bottom: 8px; }
  .step-desc { font-size: 12px; color: var(--text3); line-height: 1.6; }
  @media (max-width: 768px) { .steps-wrap { grid-template-columns: 1fr 1fr; } }
  @media (max-width: 480px) { .steps-wrap { grid-template-columns: 1fr; } }
</style>

  @include('components.website.header')

  {{-- ══════════════════ PAGE HEADER ══════════════════ --}}
  <section class="page-header">
    <div class="grid-bg"></div>
    <div class="page-header-content">
      <span class="section-tag tag-pink">🤝 {{ $tag }}</span>
      <h1 class="section-title">{{ $title }}</h1>
      <p class="section-subtitle">{{ $subtitle }}</p>
    </div>
  </section>

  {{-- ══════════════════ BENEFITS ══════════════════ --}}
  <section class="section" style="padding-bottom:40px;">
    <div class="section-head">
      <h2 class="section-title">Why become an executive</h2>
      <p class="section-subtitle">A simple, rewarding way to grow your own business with a product schools love.</p>
    </div>
    <div class="cards-grid">
      @foreach ($benefits as $benefit)
      <div class="feature-card">
        <div class="feature-icon-wrap">{{ $benefit['icon'] ?? '' }}</div>
        <h3 class="feature-title">{{ $benefit['title'] ?? '' }}</h3>
        <p class="feature-desc">{{ $benefit['desc'] ?? '' }}</p>
      </div>
      @endforeach
    </div>
  </section>

  {{-- ══════════════════ HOW IT WORKS ══════════════════ --}}
  <section class="section" style="padding-top:40px;">
    <div class="section-head">
      <span class="section-tag tag-violet">How It Works</span>
      <h2 class="section-title">Get started in 4 steps</h2>
    </div>
    <div class="steps-wrap">
      @foreach ($steps as $i => $step)
      <div class="step-card"><div class="step-num">{{ $i + 1 }}</div><div class="step-title">{{ $step['title'] ?? '' }}</div><p class="step-desc">{{ $step['desc'] ?? '' }}</p></div>
      @endforeach
    </div>
  </section>

  {{-- ══════════════════ CTA ══════════════════ --}}
  <section class="cta-section">
    <div class="cta-bg"></div>
    <div class="cta-card">
      <h2 class="cta-title">Start earning with <span class="gradient-text">EDYONE LMS</span></h2>
      <p class="cta-desc">Apply to become an executive today — our partnerships team will reach out to you.</p>
      <div class="cta-actions">
        <a href="{{ url('web/contact') }}" class="btn btn-primary btn-xl">Apply to Partner</a>
        <a href="{{ url('web/about') }}" class="btn btn-outline btn-xl">Learn About Us</a>
      </div>
    </div>
  </section>

  @include('components.website.app-section')
  @include('components.website.footer')
</body>
</html>
