@php
    $stored   = $page?->metadata ?? [];
    $def      = config('website_pages.careers', []);
    $tag      = $stored['tag']      ?? $def['tag']      ?? 'Careers';
    $title    = $stored['title']    ?? $def['title']    ?? '';
    $subtitle = $stored['subtitle'] ?? $def['subtitle'] ?? '';
    $perks    = !empty($stored['perks']) ? $stored['perks'] : ($def['perks'] ?? []);
    $jobs     = !empty($stored['jobs'])  ? $stored['jobs']  : ($def['jobs'] ?? []);
@endphp
@include('components.website.partials.head', ['title' => 'Careers'])

<style>
  /* ── Careers-specific ── */
  .jobs-wrap { max-width: 880px; margin: 0 auto; display: flex; flex-direction: column; gap: 16px; }
  .job-card {
    background: #fff; border: 1px solid var(--border2); border-radius: var(--radius);
    padding: 24px 28px; display: flex; align-items: center; justify-content: space-between;
    gap: 20px; flex-wrap: wrap; transition: all .25s; box-shadow: var(--shadow3);
  }
  .job-card:hover { border-color: var(--border); transform: translateY(-2px); box-shadow: var(--shadow2); }
  .job-role { font-size: 17px; font-weight: 600; color: var(--text); margin-bottom: 8px; }
  .job-meta { display: flex; gap: 10px; flex-wrap: wrap; }
  .job-pill { font-size: 11px; font-weight: 600; padding: 4px 12px; border-radius: 50px; background: var(--secondary-faint); color: var(--violet); }
  .job-pill.pink { background: var(--primary-faint); color: var(--pink-dark); }
  @media (max-width: 560px) { .job-card { flex-direction: column; align-items: flex-start; } }
</style>

  @include('components.website.header')

  {{-- ══════════════════ PAGE HEADER ══════════════════ --}}
  <section class="page-header">
    <div class="grid-bg"></div>
    <div class="page-header-content">
      <span class="section-tag tag-violet">💼 {{ $tag }}</span>
      <h1 class="section-title">{{ $title }}</h1>
      <p class="section-subtitle">{{ $subtitle }}</p>
    </div>
  </section>

  {{-- ══════════════════ WHY WORK HERE ══════════════════ --}}
  <section class="section" style="padding-bottom:40px;">
    <div class="section-head">
      <h2 class="section-title">Why work at EDYONE LMS</h2>
    </div>
    <div class="cards-grid">
      @foreach ($perks as $perk)
      <div class="feature-card">
        <div class="feature-icon-wrap">{{ $perk['icon'] ?? '' }}</div>
        <h3 class="feature-title">{{ $perk['title'] ?? '' }}</h3>
        <p class="feature-desc">{{ $perk['desc'] ?? '' }}</p>
      </div>
      @endforeach
    </div>
  </section>

  {{-- ══════════════════ OPEN POSITIONS ══════════════════ --}}
  <section class="section" style="padding-top:40px;">
    <div class="section-head">
      <span class="section-tag tag-pink">Open Positions</span>
      <h2 class="section-title">Current openings</h2>
    </div>
    <div class="jobs-wrap">
      @foreach ($jobs as $job)
      <div class="job-card">
        <div>
          <div class="job-role">{{ $job['role'] ?? '' }}</div>
          <div class="job-meta">
            @if (!empty($job['department']))<span class="job-pill">{{ $job['department'] }}</span>@endif
            @if (!empty($job['location']))<span class="job-pill pink">{{ $job['location'] }}</span>@endif
            @if (!empty($job['type']))<span class="job-pill">{{ $job['type'] }}</span>@endif
          </div>
        </div>
        <a href="{{ url('web/contact') }}" class="btn btn-primary">Apply Now</a>
      </div>
      @endforeach
    </div>
    <p style="text-align:center;color:var(--text3);font-size:13px;margin-top:32px;">
      Don't see a role that fits? Write to us at <strong>support@edyonelms.in</strong> — we are always looking for great people.
    </p>
  </section>

  @include('components.website.app-section')
  @include('components.website.footer')
</body>
</html>
