@include('components.website.partials.head', ['title' => 'Why Us'])

  @include('components.website.header')

  {{-- ══════════════════ PAGE HEADER ══════════════════ --}}
  <section class="page-header">
    <div class="grid-bg"></div>
    <div class="page-header-content">
      <span class="section-tag tag-dual">★ Why EDYONE LMS</span>
      <h1 class="section-title">The smarter choice for <span class="gradient-text">modern schools</span></h1>
      <p class="section-subtitle">
        Hundreds of schools across India trust EDYONE LMS to run admissions, academics, fees and
        communication on a single affordable platform. Here is what sets us apart.
      </p>
    </div>
  </section>

  {{-- ══════════════════ REASONS GRID ══════════════════ --}}
  <section class="section">
    <div class="cards-grid">

      <div class="feature-card">
        <div class="feature-icon-wrap">💰</div>
        <h3 class="feature-title">Genuinely Affordable</h3>
        <p class="feature-desc">Transparent, per-student pricing with no hidden setup fees. Built so that schools of every size can afford world-class technology.</p>
        <span class="feature-tag tag-v">Best value</span>
      </div>

      <div class="feature-card">
        <div class="feature-icon-wrap">🧩</div>
        <h3 class="feature-title">All-in-One Platform</h3>
        <p class="feature-desc">Admissions, attendance, timetable, exams, fees, study material and parent communication — everything in one login instead of five different tools.</p>
        <span class="feature-tag tag-p">Unified</span>
      </div>

      <div class="feature-card">
        <div class="feature-icon-wrap">📱</div>
        <h3 class="feature-title">Mobile First</h3>
        <p class="feature-desc">Dedicated apps for admins, teachers, students and parents — so your whole school stays connected from any phone, anywhere.</p>
        <span class="feature-tag tag-v">Android &amp; iOS</span>
      </div>

      <div class="feature-card">
        <div class="feature-icon-wrap">💳</div>
        <h3 class="feature-title">Online Fee Collection</h3>
        <p class="feature-desc">Collect fees online with instant receipts and reconciliation. Parents pay in seconds, your accounts team saves hours every week.</p>
        <span class="feature-tag tag-p">Secure</span>
      </div>

      <div class="feature-card">
        <div class="feature-icon-wrap">🛟</div>
        <h3 class="feature-title">Real Human Support</h3>
        <p class="feature-desc">Onboarding, training and ongoing help from a team that actually understands schools — over call, chat and WhatsApp.</p>
        <span class="feature-tag tag-v">Always on</span>
      </div>

      <div class="feature-card">
        <div class="feature-icon-wrap">🇮🇳</div>
        <h3 class="feature-title">Built for India</h3>
        <p class="feature-desc">Designed around Indian school workflows, boards, fee structures and languages — not a foreign product forced to fit.</p>
        <span class="feature-tag tag-p">Local</span>
      </div>

    </div>
  </section>

  {{-- ══════════════════ CTA ══════════════════ --}}
  <section class="cta-section">
    <div class="cta-bg"></div>
    <div class="cta-card">
      <h2 class="cta-title">See why schools switch to <span class="gradient-text">EDYONE LMS</span></h2>
      <p class="cta-desc">Book a free, no-obligation demo and we will show you exactly how it works for your school.</p>
      <div class="cta-actions">
        <a href="{{ url('web/demo') }}" class="btn btn-primary btn-xl">Request a Demo</a>
        <a href="{{ url('web/contact') }}" class="btn btn-outline btn-xl">Talk to Us</a>
      </div>
    </div>
  </section>

  @include('components.website.app-section')
  @include('components.website.footer')
</body>
</html>
