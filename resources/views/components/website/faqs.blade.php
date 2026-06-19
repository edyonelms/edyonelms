@php
    $stored   = $page?->metadata ?? [];
    $def      = config('website_pages.faqs', []);
    $tag      = $stored['tag']      ?? $def['tag']      ?? 'Help Center';
    $title    = $stored['title']    ?? $def['title']    ?? '';
    $subtitle = $stored['subtitle'] ?? $def['subtitle'] ?? '';
    $faqs     = !empty($stored['faqs']) ? $stored['faqs'] : ($def['faqs'] ?? []);
@endphp
@include('components.website.partials.head', ['title' => 'FAQs'])

<style>
  /* ── FAQ-specific ── */
  .faq-wrap { max-width: 820px; margin: 0 auto; display: flex; flex-direction: column; gap: 14px; }
  .faq-item { background: #fff; border: 1px solid var(--border2); border-radius: var(--radius); overflow: hidden; transition: border-color .2s, box-shadow .2s; }
  .faq-item[open] { border-color: var(--border); box-shadow: var(--shadow3); }
  .faq-q {
    list-style: none; cursor: pointer; padding: 20px 24px;
    display: flex; align-items: center; justify-content: space-between; gap: 16px;
    font-size: 15px; font-weight: 600; color: var(--text);
  }
  .faq-q::-webkit-details-marker { display: none; }
  .faq-icon { flex-shrink: 0; width: 26px; height: 26px; border-radius: 50%; background: var(--secondary-faint); color: var(--violet); display: flex; align-items: center; justify-content: center; font-size: 16px; transition: transform .25s; }
  .faq-item[open] .faq-icon { transform: rotate(45deg); background: var(--grad1); color: #fff; }
  .faq-a { padding: 0 24px 22px; font-size: 14px; color: var(--text3); line-height: 1.8; }
</style>

  @include('components.website.header')

  {{-- ══════════════════ PAGE HEADER ══════════════════ --}}
  <section class="page-header">
    <div class="grid-bg"></div>
    <div class="page-header-content">
      <span class="section-tag tag-violet">❔ {{ $tag }}</span>
      <h1 class="section-title">{{ $title }}</h1>
      <p class="section-subtitle">{{ $subtitle }}</p>
    </div>
  </section>

  {{-- ══════════════════ FAQ LIST ══════════════════ --}}
  <section class="section">
    <div class="faq-wrap">
      @foreach ($faqs as $i => $faq)
      <details class="faq-item" @if ($i === 0) open @endif>
        <summary class="faq-q">{{ $faq['question'] ?? '' }}<span class="faq-icon">+</span></summary>
        <div class="faq-a">{!! nl2br(e($faq['answer'] ?? '')) !!}</div>
      </details>
      @endforeach
    </div>
  </section>

  {{-- ══════════════════ CTA ══════════════════ --}}
  <section class="cta-section">
    <div class="cta-bg"></div>
    <div class="cta-card">
      <h2 class="cta-title">Still have <span class="gradient-text">questions?</span></h2>
      <p class="cta-desc">Our friendly team is happy to help you with anything about EDYONE LMS.</p>
      <div class="cta-actions">
        <a href="{{ url('web/contact') }}" class="btn btn-primary btn-xl">Contact Us</a>
        <a href="{{ url('web/demo') }}" class="btn btn-outline btn-xl">Request a Demo</a>
      </div>
    </div>
  </section>

  @include('components.website.app-section')
  @include('components.website.footer')
</body>
</html>
