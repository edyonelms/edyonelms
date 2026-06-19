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
      <span class="section-tag tag-violet">❔ Help Center</span>
      <h1 class="section-title">Frequently asked <span class="gradient-text">questions</span></h1>
      <p class="section-subtitle">
        Everything you need to know about EDYONE LMS. Can't find your answer? Our team is just a message away.
      </p>
    </div>
  </section>

  {{-- ══════════════════ FAQ LIST ══════════════════ --}}
  <section class="section">
    <div class="faq-wrap">

      <details class="faq-item" open>
        <summary class="faq-q">What is EDYONE LMS?<span class="faq-icon">+</span></summary>
        <div class="faq-a">EDYONE LMS is an affordable, all-in-one Learning Management System for schools. It covers admissions, attendance, timetable, exams, fees, study material and parent communication — all from one platform, with apps for admins, teachers, students and parents.</div>
      </details>

      <details class="faq-item">
        <summary class="faq-q">How much does it cost?<span class="faq-icon">+</span></summary>
        <div class="faq-a">Pricing is simple and transparent, designed to be affordable for schools of every size. Visit our <a href="{{ url('web/pricing') }}" style="color:var(--violet);">Pricing page</a> or request a demo and we will share a plan tailored to your student count.</div>
      </details>

      <details class="faq-item">
        <summary class="faq-q">Is there a mobile app?<span class="faq-icon">+</span></summary>
        <div class="faq-a">Yes. EDYONE LMS has dedicated mobile apps for Android and iOS, so admins, teachers, students and parents can stay connected from anywhere.</div>
      </details>

      <details class="faq-item">
        <summary class="faq-q">Can parents pay fees online?<span class="faq-icon">+</span></summary>
        <div class="faq-a">Absolutely. Parents can pay fees securely online and receive instant digital receipts, while your accounts team gets automatic reconciliation and dues tracking.</div>
      </details>

      <details class="faq-item">
        <summary class="faq-q">How long does it take to set up?<span class="faq-icon">+</span></summary>
        <div class="faq-a">Most schools go live within a few days. Our onboarding team helps you import your data, configure classes and fees, and trains your staff so the transition is smooth.</div>
      </details>

      <details class="faq-item">
        <summary class="faq-q">Is my school's data secure?<span class="faq-icon">+</span></summary>
        <div class="faq-a">Yes. Your data is stored securely, access is role-based, and payments are processed through trusted, secure gateways. Your information is never shared without your consent.</div>
      </details>

      <details class="faq-item">
        <summary class="faq-q">Do you provide training and support?<span class="faq-icon">+</span></summary>
        <div class="faq-a">Of course. We provide hands-on onboarding, staff training, and ongoing support over call, chat and WhatsApp so you are never left on your own.</div>
      </details>

      <details class="faq-item">
        <summary class="faq-q">How do I get started?<span class="faq-icon">+</span></summary>
        <div class="faq-a">Simply <a href="{{ url('web/demo') }}" style="color:var(--violet);">request a free demo</a> or <a href="{{ url('web/contact') }}" style="color:var(--violet);">contact us</a>. We will walk you through the platform and help you choose the right plan.</div>
      </details>

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
