@php
    $stored   = $page?->metadata ?? [];
    $def      = config('website_pages.blogs', []);
    $tag      = $stored['tag']      ?? $def['tag']      ?? 'The EDYONE Blog';
    $title    = $stored['title']    ?? $def['title']    ?? '';
    $subtitle = $stored['subtitle'] ?? $def['subtitle'] ?? '';
    $posts    = !empty($stored['posts']) ? $stored['posts'] : ($def['posts'] ?? []);
@endphp
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
      <span class="section-tag tag-dual">✍ {{ $tag }}</span>
      <h1 class="section-title">{{ $title }}</h1>
      <p class="section-subtitle">{{ $subtitle }}</p>
    </div>
  </section>

  {{-- ══════════════════ BLOG GRID ══════════════════ --}}
  <section class="section">
    <div class="blog-grid">
      @foreach ($posts as $post)
      <article class="blog-card">
        <div class="blog-thumb">{{ $post['icon'] ?? '📝' }}</div>
        <div class="blog-body">
          <div class="blog-cat">{{ $post['category'] ?? '' }}</div>
          <h3 class="blog-heading">{{ $post['title'] ?? '' }}</h3>
          <p class="blog-excerpt">{{ $post['excerpt'] ?? '' }}</p>
          <div class="blog-foot"><span>{{ $post['read_time'] ?? '' }}</span><a href="{{ $post['link'] ?? '#' }}">Read more →</a></div>
        </div>
      </article>
      @endforeach
    </div>

    <p style="text-align:center;color:var(--text3);font-size:13px;margin-top:44px;">
      More articles coming soon. Follow us on social media for the latest updates.
    </p>
  </section>

  @include('components.website.app-section')
  @include('components.website.footer')
</body>
</html>
