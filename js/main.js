document.addEventListener('DOMContentLoaded', () => {

  // ===== YEAR =====
  const yr = new Date().getFullYear();
  document.getElementById('year').textContent = yr;

  // ===== THEME =====
  const themeBtn = document.getElementById('themeBtn');
  const saved = localStorage.getItem('theme');
  if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.body.classList.add('dark');
  }
  themeBtn.addEventListener('click', () => {
    document.body.classList.toggle('dark');
    localStorage.setItem('theme', document.body.classList.contains('dark') ? 'dark' : 'light');
  });

  // ===== HAMBURGER =====
  const hamburger = document.getElementById('hamburger');
  const navLinks  = document.getElementById('navLinks');
  hamburger.addEventListener('click', () => {
    hamburger.classList.toggle('open');
    navLinks.classList.toggle('open');
  });
  navLinks.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
    hamburger.classList.remove('open');
    navLinks.classList.remove('open');
  }));

  // ===== STICKY NAVBAR =====
  const header = document.getElementById('header');
  window.addEventListener('scroll', () => {
    header.classList.toggle('scrolled', window.scrollY > 50);
  }, { passive: true });

  // ===== ACTIVE NAV =====
  const sections   = document.querySelectorAll('section[id]');
  const navAnchors = document.querySelectorAll('.nav-links a[href^="#"]');
  new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        navAnchors.forEach(a => a.classList.remove('active'));
        const a = document.querySelector(`.nav-links a[href="#${e.target.id}"]`);
        if (a) a.classList.add('active');
      }
    });
  }, { threshold: 0.4, rootMargin: '-70px 0px 0px 0px' }).observe;
  sections.forEach(s => {
    new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          navAnchors.forEach(a => a.classList.remove('active'));
          const a = document.querySelector(`.nav-links a[href="#${e.target.id}"]`);
          if (a) a.classList.add('active');
        }
      });
    }, { threshold: 0.4, rootMargin: '-70px 0px 0px 0px' }).observe(s);
  });

  // ===== TYPING EFFECT =====
  const el    = document.getElementById('typed-text');
  const roles = ['Full Stack Developer', 'PHP Developer', 'JavaScript Engineer', 'Web Designer', 'Problem Solver'];
  let ri = 0, ci = 0, del = false;
  function type() {
    const cur = roles[ri];
    el.textContent = del ? cur.slice(0, ci - 1) : cur.slice(0, ci + 1);
    del ? ci-- : ci++;
    let spd = del ? 55 : 100;
    if (!del && ci === cur.length)  { spd = 1800; del = true; }
    else if (del && ci === 0)       { del = false; ri = (ri + 1) % roles.length; spd = 350; }
    setTimeout(type, spd);
  }
  type();

  // ===== SKILL BARS =====
  const fills = document.querySelectorAll('.skill-fill');
  const skillsSec = document.getElementById('skills');
  if (skillsSec) {
    new IntersectionObserver(entries => {
      if (entries[0].isIntersecting) fills.forEach(f => f.classList.add('animate'));
    }, { threshold: 0.2 }).observe(skillsSec);
  }

  // ===== PROJECTS (AJAX) =====
  loadProjects();

  function loadProjects() {
    const box = document.getElementById('projects-container');
    const err = document.getElementById('projects-error');
    fetch('api/get_projects.php')
      .then(r => { if (!r.ok) throw 0; return r.json(); })
      .then(data => {
        box.innerHTML = '';
        if (!Array.isArray(data) || !data.length) {
          box.innerHTML = `<div class="loading-state"><p>No projects yet — <a href="admin/login.php" style="color:var(--accent)">add via admin panel</a>.</p></div>`;
          return;
        }
        data.forEach((p, i) => box.appendChild(makeCard(p, i)));
        setupFilter();
      })
      .catch(() => {
        box.innerHTML = '';
        err.style.display = 'block';
        fallback(box);
      });
  }

  function makeCard(p, i) {
    const card = document.createElement('article');
    card.className = 'project-card';
    card.style.animationDelay = `${i * 0.1}s`;
    const stack = p.tech_stack || '';
    const tags  = stack.split(',').map(t => t.trim()).filter(Boolean)
                       .map(t => `<span class="tech-tag">${esc(t)}</span>`).join('');
    const lower = stack.toLowerCase();
    card.dataset.filter = [
      lower.includes('c#')                                  ? 'csharp'     : '',
      lower.includes('asp.net') || lower.includes('aspnet') ? 'aspnet'     : '',
      lower.includes('javascript') || lower.includes('js')  ? 'javascript' : '',
      lower.includes('html') || lower.includes('css') || lower.includes('scss') ? 'html' : ''
    ].filter(Boolean).join(' ') || 'all';

    const imgMap = {
      'fitgain':      'photo-1534438327276-14e5300c3a48',
      'identityapp':  'photo-1614064641938-3bbee52942c7',
      'storeapp':     'photo-1472851294608-062f824d29cc',
      'schoolsystem': 'photo-1546410531-bb4caa6b424d',
      'formsapp':     'photo-1587620962725-abab7fe55159'
    };
    const key    = (p.title || '').toLowerCase().replace(/\s+/g, '');
    const imgId  = imgMap[key] || 'photo-1461749280684-dccba630e2f6';
    const imgUrl = `https://images.unsplash.com/${imgId}?auto=format&fit=crop&w=600&q=80`;

    const gh = p.github_url ? `<a href="${esc(p.github_url)}" target="_blank" rel="noopener" class="proj-link">GitHub ↗</a>` : '';
    const lv = p.live_url   ? `<a href="${esc(p.live_url)}"   target="_blank" rel="noopener" class="proj-link">Live ↗</a>`   : '';

    card.innerHTML = `
      <div class="project-thumb"><img src="${imgUrl}" alt="${esc(p.title)}" loading="lazy"></div>
      <div class="project-body">
        <div class="project-tags">${tags}</div>
        <h3 class="project-title">${esc(p.title)}</h3>
        <p class="project-desc">${esc(p.description)}</p>
        <div class="project-links">${gh}${lv}</div>
      </div>`;
    return card;
  }

  function fallback(box) {
    const items = [
      { title:'Portfolio Website',     description:'Full-stack portfolio with PHP, MySQL, admin panel, AJAX & dark mode.',  tech_stack:'HTML5, CSS3, JavaScript, PHP, MySQL', github_url:'https://github.com/osmanemreuzer', live_url:'#' },
      { title:'Student Grade Manager', description:'Python app to manage and calculate student grades automatically.',       tech_stack:'Python, SQLite',                        github_url:'https://github.com/osmanemreuzer', live_url:null },
      { title:'Weather Dashboard',     description:'Real-time weather app using OpenWeatherMap API with responsive UI.',     tech_stack:'HTML, CSS, JavaScript',                github_url:'https://github.com/osmanemreuzer', live_url:'#' },
      { title:'E-Commerce Frontend',   description:'Responsive store with cart via LocalStorage and JS form validation.',   tech_stack:'HTML, CSS, JavaScript',                github_url:'https://github.com/osmanemreuzer', live_url:'#' }
    ];
    items.forEach((p, i) => box.appendChild(makeCard(p, i)));
    setupFilter();
  }

  function setupFilter() {
    const btns  = document.querySelectorAll('.filter-btn');
    const cards = document.querySelectorAll('.project-card');
    btns.forEach(btn => btn.addEventListener('click', () => {
      btns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const f = btn.dataset.filter;
      cards.forEach(c => {
        c.style.display = f === 'all' || (c.dataset.filter || '').includes(f) ? '' : 'none';
      });
    }));
  }

  // ===== CONTACT FORM =====
  const form = document.getElementById('contactForm');
  if (form) {
    const F = {
      name:    { el: document.getElementById('name'),    err: document.getElementById('nameError')    },
      email:   { el: document.getElementById('email'),   err: document.getElementById('emailError')   },
      subject: { el: document.getElementById('subject'), err: document.getElementById('subjectError') },
      message: { el: document.getElementById('message'), err: document.getElementById('messageError') }
    };
    const V = {
      name:    v => v.trim().length < 2    ? 'At least 2 characters required.'       : '',
      email:   v => !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim()) ? 'Enter a valid email.' : '',
      subject: v => v.trim().length < 3    ? 'At least 3 characters required.'       : '',
      message: v => v.trim().length < 10   ? 'At least 10 characters required.'      : ''
    };
    Object.keys(F).forEach(k => {
      F[k].el.addEventListener('blur',  () => vf(k));
      F[k].el.addEventListener('input', () => { if (F[k].el.classList.contains('invalid')) vf(k); });
    });
    function vf(k) {
      const msg = V[k](F[k].el.value);
      F[k].err.textContent = msg;
      F[k].el.classList.toggle('invalid', !!msg);
      F[k].el.classList.toggle('valid',   !msg && F[k].el.value.trim().length > 0);
      return !msg;
    }
    form.addEventListener('submit', async e => {
      e.preventDefault();
      if (!Object.keys(F).map(vf).every(Boolean)) return;
      const btn     = document.getElementById('submitBtn');
      const btnText = btn.querySelector('.btn-text');
      const btnLoad = btn.querySelector('.btn-loading');
      const status  = document.getElementById('formStatus');
      btn.disabled = true;
      btnText.style.display = 'none';
      btnLoad.style.display = 'inline';
      status.className = 'form-status';
      try {
        const res    = await fetch('api/contact.php', { method:'POST', body: new FormData(form) });
        const result = await res.json();
        if (result.success) {
          status.textContent = '✓ Message sent! I\'ll get back to you soon.';
          status.className   = 'form-status success';
          form.reset();
          Object.values(F).forEach(({ el }) => el.classList.remove('valid','invalid'));
          showToast('Message sent successfully!', 'success');
        } else {
          status.textContent = result.message || 'Something went wrong.';
          status.className   = 'form-status error';
        }
      } catch {
        status.textContent = 'Could not reach server. Try again later.';
        status.className   = 'form-status error';
      } finally {
        btn.disabled = false;
        btnText.style.display = 'inline';
        btnLoad.style.display = 'none';
      }
    });
  }

  // ===== TOAST =====
  function showToast(msg, type = 'info') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className   = `toast ${type} show`;
    setTimeout(() => t.classList.remove('show'), 4000);
  }


  // ===== ESCAPE =====
  function esc(s) {
    if (!s) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }
});
