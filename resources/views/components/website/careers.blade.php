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
      <span class="section-tag tag-violet">💼 Careers</span>
      <h1 class="section-title">Build the future of <span class="gradient-text">education with us</span></h1>
      <p class="section-subtitle">
        We are a fast-growing team on a mission to make quality school technology affordable for every
        institution in India. If that excites you, we would love to hear from you.
      </p>
    </div>
  </section>

  {{-- ══════════════════ WHY WORK HERE ══════════════════ --}}
  <section class="section" style="padding-bottom:40px;">
    <div class="section-head">
      <h2 class="section-title">Why work at EDYONE LMS</h2>
    </div>
    <div class="cards-grid">
      <div class="feature-card">
        <div class="feature-icon-wrap">🚀</div>
        <h3 class="feature-title">Real Impact</h3>
        <p class="feature-desc">Your work directly improves how thousands of students, teachers and parents learn and connect.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon-wrap">🌱</div>
        <h3 class="feature-title">Grow Fast</h3>
        <p class="feature-desc">Take ownership early, learn across functions, and grow as quickly as you can deliver.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon-wrap">🤝</div>
        <h3 class="feature-title">Supportive Team</h3>
        <p class="feature-desc">A friendly, ambitious team that celebrates wins and helps each other through challenges.</p>
      </div>
    </div>
  </section>

  {{-- ══════════════════ OPEN POSITIONS ══════════════════ --}}
  <section class="section" style="padding-top:40px;">
    <div class="section-head">
      <span class="section-tag tag-pink">Open Positions</span>
      <h2 class="section-title">Current openings</h2>
    </div>
    <div class="jobs-wrap">

      <div class="job-card">
        <div>
          <div class="job-role">Business Development Executive</div>
          <div class="job-meta"><span class="job-pill">Sales</span><span class="job-pill pink">Field / Remote</span><span class="job-pill">Full-time</span></div>
        </div>
        <a href="{{ url('web/contact') }}" class="btn btn-primary">Apply Now</a>
      </div>

      <div class="job-card">
        <div>
          <div class="job-role">Customer Support Associate</div>
          <div class="job-meta"><span class="job-pill">Support</span><span class="job-pill pink">Aligarh, UP</span><span class="job-pill">Full-time</span></div>
        </div>
        <a href="{{ url('web/contact') }}" class="btn btn-primary">Apply Now</a>
      </div>

      <div class="job-card">
        <div>
          <div class="job-role">Full-Stack Developer (Laravel / React Native)</div>
          <div class="job-meta"><span class="job-pill">Engineering</span><span class="job-pill pink">Remote</span><span class="job-pill">Full-time</span></div>
        </div>
        <a href="{{ url('web/contact') }}" class="btn btn-primary">Apply Now</a>
      </div>

      <div class="job-card">
        <div>
          <div class="job-role">School Onboarding Specialist</div>
          <div class="job-meta"><span class="job-pill">Operations</span><span class="job-pill pink">Hybrid</span><span class="job-pill">Full-time</span></div>
        </div>
        <a href="{{ url('web/contact') }}" class="btn btn-primary">Apply Now</a>
      </div>

    </div>
    <p style="text-align:center;color:var(--text3);font-size:13px;margin-top:32px;">
      Don't see a role that fits? Write to us at <strong>support@edyonelms.in</strong> — we are always looking for great people.
    </p>
  </section>

  @include('components.website.app-section')
  @include('components.website.footer')
</body>
</html>
