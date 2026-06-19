@include('components.website.partials.head', ['title' => 'Blogs'])

<style>
  /* ── Blogs-specific ── */
  .blog-grid { max-width: 1180px; margin: 0 auto; display: grid; grid-template-columns: repeat(3, 1fr); gap: 28px; }
  .blog-card {
    background: #fff; border: 1px solid var(--border2); border-radius: var(--radius);
    overflow: hidden; transition: all .28s; box-shadow: var(--shadow3); display: flex; flex-direction: column;
  }
  .blog-card:hover { transform: translateY(-4px); box-shadow: var(--shadow2); border-color: var(--border); }
  .blog-thumb { height: 150px; background: var(--grad2); display: flex; align-items: center; justify-content: center; font-size: 42px; }
  .blog-body { padding: 22px 22px 24px; display: flex; flex-direction: column; flex: 1; }
  .blog-cat { font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: var(--violet); margin-bottom: 10px; }
  .blog-heading { font-size: 16px; font-weight: 600; color: var(--text); line-height: 1.4; margin-bottom: 10px; }
  .blog-excerpt { font-size: 13px; color: var(--text3); line-height: 1.7; margin-bottom: 16px; flex: 1; }
  .blog-foot { display: flex; align-items: center; justify-content: space-between; font-size: 12px; color: var(--text4); }
  .blog-foot a { color: var(--violet); text-decoration: none; font-weight: 600; }
  @media (max-width: 1024px) { .blog-grid { grid-template-columns: 1fr 1fr; } }
  @media (max-width: 640px) { .blog-grid { grid-template-columns: 1fr; } }
</style>

  @include('components.website.header')

  {{-- ══════════════════ PAGE HEADER ══════════════════ --}}
  <section class="page-header">
    <div class="grid-bg"></div>
    <div class="page-header-content">
      <span class="section-tag tag-dual">✍ The EDYONE Blog</span>
      <h1 class="section-title">Ideas &amp; insights for <span class="gradient-text">modern schools</span></h1>
      <p class="section-subtitle">
        Practical tips, product updates and stories to help your school run better, teach smarter and
        engage families more effectively.
      </p>
    </div>
  </section>

  {{-- ══════════════════ BLOG GRID ══════════════════ --}}
  <section class="section">
    <div class="blog-grid">

      <article class="blog-card">
        <div class="blog-thumb">📲</div>
        <div class="blog-body">
          <div class="blog-cat">School Tech</div>
          <h3 class="blog-heading">5 ways an LMS saves your school hours every week</h3>
          <p class="blog-excerpt">From attendance to fee collection, see where digital tools cut the busywork so your staff can focus on students.</p>
          <div class="blog-foot"><span>5 min read</span><a href="#">Read more →</a></div>
        </div>
      </article>

      <article class="blog-card">
        <div class="blog-thumb">💳</div>
        <div class="blog-body">
          <div class="blog-cat">Fees</div>
          <h3 class="blog-heading">Moving to online fee collection: a simple guide</h3>
          <p class="blog-excerpt">How to roll out online payments smoothly, get parents on board, and reconcile fees without the headache.</p>
          <div class="blog-foot"><span>6 min read</span><a href="#">Read more →</a></div>
        </div>
      </article>

      <article class="blog-card">
        <div class="blog-thumb">👨‍👩‍👧</div>
        <div class="blog-body">
          <div class="blog-cat">Communication</div>
          <h3 class="blog-heading">Keeping parents engaged with instant notifications</h3>
          <p class="blog-excerpt">Why timely updates build trust, and how to use announcements and push alerts the right way.</p>
          <div class="blog-foot"><span>4 min read</span><a href="#">Read more →</a></div>
        </div>
      </article>

      <article class="blog-card">
        <div class="blog-thumb">📊</div>
        <div class="blog-body">
          <div class="blog-cat">Academics</div>
          <h3 class="blog-heading">Faster exams and report cards, start to finish</h3>
          <p class="blog-excerpt">A step-by-step look at setting up exams and generating professional report cards in minutes.</p>
          <div class="blog-foot"><span>5 min read</span><a href="#">Read more →</a></div>
        </div>
      </article>

      <article class="blog-card">
        <div class="blog-thumb">🎓</div>
        <div class="blog-body">
          <div class="blog-cat">Admissions</div>
          <h3 class="blog-heading">Turning enquiries into enrolments online</h3>
          <p class="blog-excerpt">Build an admission funnel that captures every lead and helps your team follow up at the right time.</p>
          <div class="blog-foot"><span>7 min read</span><a href="#">Read more →</a></div>
        </div>
      </article>

      <article class="blog-card">
        <div class="blog-thumb">🚀</div>
        <div class="blog-body">
          <div class="blog-cat">Getting Started</div>
          <h3 class="blog-heading">Going digital: a checklist for school leaders</h3>
          <p class="blog-excerpt">Everything to prepare before you switch to an LMS, so your rollout is smooth from day one.</p>
          <div class="blog-foot"><span>6 min read</span><a href="#">Read more →</a></div>
        </div>
      </article>

    </div>

    <p style="text-align:center;color:var(--text3);font-size:13px;margin-top:44px;">
      More articles coming soon. Follow us on social media for the latest updates.
    </p>
  </section>

  @include('components.website.app-section')
  @include('components.website.footer')
</body>
</html>
