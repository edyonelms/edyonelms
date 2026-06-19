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
      <span class="section-tag tag-pink">🤝 Partner Program</span>
      <h1 class="section-title">Become an <span class="gradient-text">EDYONE Executive</span></h1>
      <p class="section-subtitle">
        Partner with EDYONE LMS to bring affordable school technology to institutions in your region —
        and earn attractive recurring income while you do it.
      </p>
    </div>
  </section>

  {{-- ══════════════════ BENEFITS ══════════════════ --}}
  <section class="section" style="padding-bottom:40px;">
    <div class="section-head">
      <h2 class="section-title">Why become an executive</h2>
      <p class="section-subtitle">A simple, rewarding way to grow your own business with a product schools love.</p>
    </div>
    <div class="cards-grid">
      <div class="feature-card">
        <div class="feature-icon-wrap">💸</div>
        <h3 class="feature-title">Attractive Commissions</h3>
        <p class="feature-desc">Earn competitive payouts on every school you onboard, plus recurring income as they renew.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon-wrap">📈</div>
        <h3 class="feature-title">Ready Demand</h3>
        <p class="feature-desc">Schools everywhere need affordable digital tools — you bring a product that practically sells itself.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon-wrap">🎒</div>
        <h3 class="feature-title">Full Sales Kit</h3>
        <p class="feature-desc">Get brochures, demos, pricing and live training so you can pitch with confidence from day one.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon-wrap">🛟</div>
        <h3 class="feature-title">Dedicated Support</h3>
        <p class="feature-desc">Our team handles onboarding and technical support, so you can focus on building relationships.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon-wrap">⏱️</div>
        <h3 class="feature-title">Flexible &amp; Independent</h3>
        <p class="feature-desc">Work your own hours, in your own region — full-time or alongside your existing work.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon-wrap">🏅</div>
        <h3 class="feature-title">Recognition &amp; Rewards</h3>
        <p class="feature-desc">Top-performing executives unlock higher tiers, bonuses and exclusive incentives.</p>
      </div>
    </div>
  </section>

  {{-- ══════════════════ HOW IT WORKS ══════════════════ --}}
  <section class="section" style="padding-top:40px;">
    <div class="section-head">
      <span class="section-tag tag-violet">How It Works</span>
      <h2 class="section-title">Get started in 4 steps</h2>
    </div>
    <div class="steps-wrap">
      <div class="step-card"><div class="step-num">1</div><div class="step-title">Apply</div><p class="step-desc">Fill in a short form and tell us about your region and experience.</p></div>
      <div class="step-card"><div class="step-num">2</div><div class="step-title">Onboard</div><p class="step-desc">Get trained on the product, pricing and sales material.</p></div>
      <div class="step-card"><div class="step-num">3</div><div class="step-title">Pitch</div><p class="step-desc">Introduce EDYONE LMS to schools and book demos with our support.</p></div>
      <div class="step-card"><div class="step-num">4</div><div class="step-title">Earn</div><p class="step-desc">Get paid for every school you bring on board — and keep earning.</p></div>
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
