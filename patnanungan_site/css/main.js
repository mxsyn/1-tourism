/* ════════════════════════════════════════
   Discover Patnanungan — Shared JS
   ════════════════════════════════════════ */

/* ── MOBILE NAV TOGGLE ─── */
function toggleNav() {
  document.getElementById('navLinks').classList.toggle('open');
}
document.addEventListener('click', e => {
  const nav = document.getElementById('navLinks');
  const toggle = document.getElementById('navToggle');
  if (nav && toggle && !nav.contains(e.target) && !toggle.contains(e.target)) {
    nav.classList.remove('open');
  }
});

/* ── BACK TO TOP ─── */
window.addEventListener('scroll', () => {
  const btn = document.getElementById('backTop');
  if (!btn) return;
  btn.classList.toggle('visible', window.scrollY > 400);
});
function scrollToTop() { window.scrollTo({ top: 0, behavior: 'smooth' }); }

/* ── FAQ ACCORDION ─── */
function toggleFaq(el) {
  const answer = el.nextElementSibling;
  const isOpen = el.classList.contains('open');
  document.querySelectorAll('.faq-q.open').forEach(q => {
    q.classList.remove('open');
    q.nextElementSibling.style.display = 'none';
  });
  if (!isOpen) { el.classList.add('open'); answer.style.display = 'block'; }
}

/* ── GALLERY FILTER ─── */
function filterGallery(cat, tab) {
  document.querySelectorAll('.g-tab').forEach(t => t.classList.remove('active'));
  tab.classList.add('active');
  document.querySelectorAll('.g-item').forEach(item => {
    item.style.display = (cat === 'all' || item.dataset.cat === cat) ? 'flex' : 'none';
  });
}

/* ── CONTACT FORM VALIDATION (client-side) ─── */
function submitForm() {
  const name  = document.getElementById('f-name')?.value.trim();
  const email = document.getElementById('f-email')?.value.trim();
  const type  = document.getElementById('f-type')?.value;
  const msg   = document.getElementById('f-msg')?.value.trim();

  if (!name)  { alert('Please enter your full name.'); return false; }
  if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    alert('Please enter a valid email address.'); return false;
  }
  if (!type)  { alert('Please select an inquiry type.'); return false; }
  if (!msg)   { alert('Please enter your message.'); return false; }

  // In production: form submits to contact.php POST handler
  // For demo: show success message
  const success = document.getElementById('formSuccess');
  if (success) {
    success.style.display = 'block';
    ['f-name','f-email','f-type','f-date','f-msg'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.value = '';
    });
  }
  return false; // prevent default submit in demo
}

/* ── DOC MODAL ─── */
function openDoc(key) {
  const d = docContent[key];
  if (!d) return;
  document.getElementById('docTitle').textContent = d.title;
  document.getElementById('docBody').innerHTML = d.body;
  document.getElementById('docModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeDoc() {
  const m = document.getElementById('docModal');
  if (m) m.classList.remove('open');
  document.body.style.overflow = '';
}
document.addEventListener('DOMContentLoaded', () => {
  const m = document.getElementById('docModal');
  if (m) m.addEventListener('click', e => { if (e.target === m) closeDoc(); });
});
