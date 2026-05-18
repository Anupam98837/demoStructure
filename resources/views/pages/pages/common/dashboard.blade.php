@extends('pages.layout.structure')

@section('title', 'Dashboard')

@push('styles')
<style>
.att-wrap{display:grid;gap:20px}
.att-hero{
  position:relative;
  overflow:hidden;
  background:linear-gradient(135deg, rgba(15,118,110,.12), rgba(217,119,6,.12));
  border:1px solid var(--line-strong);
  border-radius:28px;
  padding:28px;
  box-shadow:var(--shadow-2);
}
.att-hero::after{
  content:"";
  position:absolute;
  inset:auto -40px -40px auto;
  width:180px;
  height:180px;
  border-radius:999px;
  background:radial-gradient(circle, rgba(217,119,6,.18), transparent 70%);
}
.att-kicker{
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:8px 12px;
  border-radius:999px;
  background:rgba(255,255,255,.72);
  border:1px solid rgba(15,118,110,.14);
  color:var(--primary-color);
  font-size:12px;
  font-weight:800;
  text-transform:uppercase;
  letter-spacing:.08em;
}
.att-hero h1{
  margin:16px 0 8px;
  font-size:clamp(2rem,4vw,3.15rem);
  letter-spacing:-.05em;
}
.att-hero p{
  margin:0;
  max-width:64ch;
  color:var(--muted-color);
  font-size:15px;
  line-height:1.8;
}
.att-grid{
  display:grid;
  grid-template-columns:repeat(3,minmax(0,1fr));
  gap:16px;
}
.att-card{
  background:var(--surface);
  border:1px solid var(--line-strong);
  border-radius:22px;
  padding:20px;
  box-shadow:var(--shadow-1);
}
.att-card h2{
  margin:0 0 10px;
  font-size:18px;
}
.att-card p{
  margin:0;
  color:var(--muted-color);
  line-height:1.7;
}
.att-chip-row{
  display:flex;
  flex-wrap:wrap;
  gap:10px;
  margin-top:16px;
}
.att-chip{
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:9px 12px;
  border-radius:999px;
  background:var(--surface-2);
  border:1px solid var(--line-strong);
  color:var(--ink);
  font-size:12px;
  font-weight:700;
}
.att-list{
  display:grid;
  gap:12px;
  margin-top:6px;
}
.att-list-item{
  padding:14px;
  border-radius:18px;
  background:var(--surface-2);
  border:1px solid var(--line-strong);
}
.att-list-item strong{
  display:block;
  margin-bottom:6px;
  color:var(--ink);
}
.att-list-item span{
  display:block;
  color:var(--muted-color);
  line-height:1.6;
}
@media (max-width: 991.98px){
  .att-grid{grid-template-columns:1fr}
}
</style>
@endpush

@section('content')
<div class="att-wrap">
  <section class="att-hero">
    <span class="att-kicker"><i class="fa-solid fa-clock"></i>Attendance Starter</span>
    <h1 id="dashboardGreeting">Welcome back</h1>
    <p id="dashboardLead">Loading your workspace details...</p>
    <div class="att-chip-row" id="dashboardMeta">
      <span class="att-chip"><i class="fa-solid fa-spinner fa-spin"></i>Fetching account</span>
    </div>
  </section>

  <section class="att-grid">
    <article class="att-card">
      <h2>Current Focus</h2>
      <p>This dashboard is intentionally lightweight for the new attendance build. The shared shell, role handling, logs, notifications, and privilege system stay in place while the new attendance flows are added on top.</p>
    </article>

    <article class="att-card">
      <h2>Quick Notes</h2>
      <div class="att-list">
        <div class="att-list-item">
          <strong>Admin</strong>
          <span>Can manage users, dashboard menus, page privileges, and role privileges from the shared admin shell.</span>
        </div>
        <div class="att-list-item">
          <strong>HR</strong>
          <span>Ready for workforce-facing access with seeded user-management permissions for the fresh attendance database.</span>
        </div>
      </div>
    </article>

    <article class="att-card">
      <h2>Next Build Space</h2>
      <div class="att-list">
        <div class="att-list-item">
          <strong>Attendance modules</strong>
          <span>Daily punch, shift, leave, holiday, and reporting screens can now be added on top of this cleaned attendance starter.</span>
        </div>
      </div>
    </article>
  </section>
</div>
@endsection

@section('scripts')
<script>
(() => {
  const token = sessionStorage.getItem('token') || localStorage.getItem('token');
  if (!token) {
    window.location.replace('/');
    return;
  }

  const greetingEl = document.getElementById('dashboardGreeting');
  const leadEl = document.getElementById('dashboardLead');
  const metaEl = document.getElementById('dashboardMeta');

  function roleLabel(role){
    const map = { admin: 'Admin', hr: 'HR', employee: 'Employee' };
    return map[String(role || '').toLowerCase()] || 'Employee';
  }

  function formatDate(){
    return new Intl.DateTimeFormat([], {
      weekday: 'short',
      day: 'numeric',
      month: 'short',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    }).format(new Date());
  }

  fetch('/api/auth/check', {
    headers: {
      'Authorization': 'Bearer ' + token,
      'Accept': 'application/json'
    }
  })
    .then(async (response) => {
      const data = await response.json().catch(() => ({}));
      if (!response.ok || !data.user) {
        throw new Error(data.message || 'Session expired');
      }
      return data.user;
    })
    .then((user) => {
      const name = user.name || 'Team member';
      const role = String(user.role || 'employee').toLowerCase();
      greetingEl.textContent = `Welcome, ${name}`;
      leadEl.textContent = `You are signed in as ${roleLabel(role)}. This starter now uses the shared attendance shell and is ready for the new workflow build.`;
      metaEl.innerHTML = `
        <span class="att-chip"><i class="fa-solid fa-user-shield"></i>${roleLabel(role)}</span>
        <span class="att-chip"><i class="fa-solid fa-signal"></i>${user.status || 'active'}</span>
        <span class="att-chip"><i class="fa-solid fa-calendar-day"></i>${formatDate()}</span>
      `;
    })
    .catch(() => {
      sessionStorage.removeItem('token');
      sessionStorage.removeItem('role');
      localStorage.removeItem('token');
      localStorage.removeItem('role');
      window.location.replace('/');
    });
})();
</script>
@endsection
