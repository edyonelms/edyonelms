@include('components.website.partials.head', ['title' => 'Services'])

  @include('components.website.header')

  {{-- ══════════════════ PAGE HEADER ══════════════════ --}}
  <section class="page-header">
    <div class="grid-bg"></div>
    <div class="page-header-content">
      <span class="section-tag tag-violet">⚙ Our Services</span>
      <h1 class="section-title">Everything your school needs, <span class="gradient-text">end to end</span></h1>
      <p class="section-subtitle">
        From setup to daily operations, EDYONE LMS gives schools a complete digital toolkit —
        backed by hands-on services so you are never left figuring it out alone.
      </p>
    </div>
  </section>

  {{-- ══════════════════ SERVICES GRID ══════════════════ --}}
  <section class="section">
    <div class="cards-grid">

      <div class="feature-card">
        <div class="feature-icon-wrap">🎓</div>
        <h3 class="feature-title">School Management System</h3>
        <p class="feature-desc">Manage students, staff, classes, sections and the full academic year from one powerful dashboard.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon-wrap">📝</div>
        <h3 class="feature-title">Admissions &amp; Enquiries</h3>
        <p class="feature-desc">Capture enquiries, run online admissions and convert leads into enrolled students with less paperwork.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon-wrap">🗓️</div>
        <h3 class="feature-title">Attendance &amp; Timetable</h3>
        <p class="feature-desc">Daily attendance, automated timetables and arrangement management for teachers and classes.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon-wrap">📊</div>
        <h3 class="feature-title">Exams &amp; Report Cards</h3>
        <p class="feature-desc">Set up exams, enter marks and generate professional report cards in a few clicks.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon-wrap">💳</div>
        <h3 class="feature-title">Fee Management</h3>
        <p class="feature-desc">Define fee structures, collect payments online, and track dues with automatic receipts and reminders.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon-wrap">📚</div>
        <h3 class="feature-title">Digital Content &amp; Quizzes</h3>
        <p class="feature-desc">Share syllabus, study material, books and quizzes so learning continues beyond the classroom.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon-wrap">🔔</div>
        <h3 class="feature-title">Notifications &amp; Communication</h3>
        <p class="feature-desc">Reach parents and staff instantly with announcements, push notifications and calendar updates.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon-wrap">🆔</div>
        <h3 class="feature-title">ID Cards &amp; Documents</h3>
        <p class="feature-desc">Generate student and staff ID cards and essential documents directly from the platform.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon-wrap">🤝</div>
        <h3 class="feature-title">Onboarding &amp; Training</h3>
        <p class="feature-desc">Guided setup, data migration and staff training so your school goes live smoothly and quickly.</p>
      </div>

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
