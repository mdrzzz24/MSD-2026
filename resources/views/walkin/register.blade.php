<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Walk-in Registration — Metrodata Solution Day 2026</title>
    <meta name="description" content="Register as a walk-in attendee for Metrodata Solution Day 2026 — Winning with AI. Jakarta, 20 August 2026, Shangri-La Hotel." />
    <meta property="og:title" content="Walk-in Registration — Metrodata Solution Day 2026" />
    <meta property="og:description" content="MSD 2026: Winning with AI — Build, Run, and Scale for Measurable Impact." />
    <meta property="og:image" content="{{ asset('img/header-sos.jpeg') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="preload" as="image" href="{{ asset('img/Website-BG.jpg?v2') }}" fetchpriority="high">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=15">
    <style>
        .walkin-shell{min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:flex-start;padding:44px 16px 24px}
        .walkin-card{width:100%;max-width:640px;background:var(--card-bg);border:1px solid var(--line);border-radius:22px;padding:36px 30px;backdrop-filter:blur(10px);box-shadow:0 24px 60px rgba(0,0,0,.4),inset 0 1px 0 rgba(255,255,255,.06)}
        .walkin-card .wi-logo{display:block;margin:0 auto 20px;height:52px;width:auto}
        .walkin-card .form-grid{margin-top:0}
        .error-box{display:flex;gap:12px;align-items:flex-start;background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3);color:#f87171;border-radius:12px;padding:12px 14px;font-size:13px;line-height:1.6;margin-bottom:22px}
        .error-box svg{flex:none;width:18px;height:18px;margin-top:1px}
        .phone-wrapper{display:flex;align-items:stretch;gap:0}
        .phone-prefix{display:flex;align-items:center;padding:12px 10px;background:rgba(255,255,255,.08);border:1px solid var(--line);border-right:none;border-radius:10px 0 0 10px;font-size:14px;color:rgba(255,255,255,.45);white-space:nowrap;flex-shrink:0;transition:border-color .25s,box-shadow .25s,background .25s}
        .phone-input{flex:1;border-radius:0 10px 10px 0;border-left:none;min-width:0}
        .field-err{display:block;font-size:12px;color:#ef4444;margin-top:2px;min-height:0}
        .field-err:empty{display:none}
        @keyframes fadeInUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
        @keyframes spin{to{transform:rotate(360deg)}}
    </style>
</head>
<body>
<div class="walkin-shell">
    <div class="walkin-card">
        <img src="{{ asset('img/logo-msd.png') }}" alt="MSD" class="wi-logo">
        <div style="text-align:center;margin-bottom:24px">
            <h1 style="color:#fff;font-size:clamp(22px,3vw,28px);font-weight:800;letter-spacing:-.02em">Walk-in Registration</h1>
        </div>

        <div id="walkinErrors" class="error-box" style="display:none" role="alert">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span id="walkinErrorsMsg"></span>
        </div>
        @if (isset($errors) && $errors->any())
            <div class="error-box" role="alert" style="display:flex">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span>@foreach ($errors->all() as $e){{ $e }}@if(!$loop->last)<br>@endif @endforeach</span>
            </div>
        @endif

        <form id="walkinPublicForm" class="form-grid" method="POST" action="{{ route('walkin.public.store') }}" onsubmit="submitWalkinForm(event)">
            @csrf
            {{-- Honeypot (hidden from humans) --}}
            <div class="field" style="display:none" aria-hidden="true">
                <label>Company Website</label>
                <input type="text" name="company_website" tabindex="-1" autocomplete="off">
            </div>
            <div class="field"><label>First Name</label><input required name="firstName" value="{{ old('firstName') }}" placeholder="First Name" /><span class="field-err"></span></div>
            <div class="field"><label>Last Name</label><input required name="lastName" value="{{ old('lastName') }}" placeholder="Last Name" /><span class="field-err"></span></div>
            <div class="field">
                <label>Job Function</label>
                <select name="job_title" required>
                    <option value="">Select Job Function</option>
                    <option>Intern</option>
                    <option>Staff</option>
                    <option>Supervisor</option>
                    <option>Manager</option>
                    <option>Senior Manager</option>
                    <option>General Manager</option>
                    <option>Head of Department</option>
                    <option>Chief</option>
                    <option>Director</option>
                    <option>President</option>
                    <option>Vice President</option>
                </select>
            </div>
            <div class="field">
                <label>Job Title</label>
                <select id="jobRoleSelector" name="job_role_selector" required>
                    <option value="">Select Job Title</option>
                    <option>Student</option>
                    <option>Sales</option>
                    <option>Pre-Sales / Solution Architect</option>
                    <option>Engineering</option>
                    <option>Marketing</option>
                    <option>Management</option>
                    <option>Finance / Accounting</option>
                    <option>Information Technology</option>
                    <option>Operations</option>
                    <option>Human Resources</option>
                    <option>Legal / Compliance</option>
                    <option>Procurement</option>
                    <option>Research &amp; Development</option>
                    <option>Customer Service / Support</option>
                    <option>Consulting</option>
                    <option>Business Development</option>
                    <option>Administration</option>
                </select>
            </div>
            <div class="field" id="jobRoleSpecificField" style="display:none;">
                <label>Job Role</label>
                <select id="jobRoleSpecific">
                    <option value="">Select Job Role</option>
                    <option>IT Infrastructure</option>
                    <option>Infrastructure Engineer</option>
                    <option>System Administrator</option>
                    <option>Network Administrator</option>
                    <option>Network Engineer</option>
                    <option>IT Operations</option>
                    <option>IT Support</option>
                    <option>Helpdesk</option>
                    <option>Desktop Support</option>
                    <option>NOC Engineer</option>
                    <option>Data Center Engineer</option>
                    <option>IT Security</option>
                    <option>Cyber Security Analyst</option>
                    <option>Security Engineer</option>
                    <option>Security Operations Center</option>
                    <option>DevSecOps Engineer</option>
                    <option>IT Governance, Risk &amp; Compliance (GRC)</option>
                    <option>Software Engineer</option>
                    <option>Software Developer</option>
                    <option>Full Stack Developer</option>
                    <option>Front-End Developer</option>
                    <option>Back-End Developer</option>
                    <option>Mobile Developer</option>
                    <option>Web Developer</option>
                    <option>Application Developer</option>
                    <option>Technical Lead</option>
                    <option>Engineering Manager</option>
                    <option>Enterprise Architect</option>
                    <option>Solution Architect</option>
                    <option>Technical Architect</option>
                    <option>Cloud Architect</option>
                    <option>Application Architect</option>
                    <option>Data Analyst</option>
                    <option>Business Intelligence (BI) Analyst</option>
                    <option>Data Engineer</option>
                    <option>Data Scientist</option>
                    <option>AI Engineer</option>
                    <option>Machine Learning Engineer</option>
                    <option>Analytics</option>
                    <option>ERP</option>
                    <option>ERP Basis</option>
                    <option>ERP Functional</option>
                    <option>ERP Consultant</option>
                    <option>Business Application</option>
                    <option>Cloud Engineer</option>
                    <option>Cloud Administrator</option>
                    <option>DevOps Engineer</option>
                    <option>Site Reliability Engineer (SRE)</option>
                    <option>Platform Engineer</option>
                    <option>Engineer</option>
                    <option>Database Administrator (DBA)</option>
                    <option>Database Engineer</option>
                    <option>Database Architect</option>
                    <option>IT Project</option>
                    <option>IT Governance</option>
                    <option>IT Compliance</option>
                    <option>IT Audit</option>
                    <option>IT Risk</option>
                    <option>Digital Transformation</option>
                    <option>Digital Innovation</option>
                    <option>Digital Technology</option>
                    <option>IT Transformation</option>
                    <option>IT Business Analyst</option>
                    <option>System Analyst</option>
                    <option>IT Business Solution</option>
                    <option>IT Product Owner</option>
                    <option>IT Product</option>
                    <option>QA Engineer</option>
                    <option>Software</option>
                    <option>IT Engineer</option>
                    <option>IT Automation Engineer</option>
                    <option>IT Quality Assurance</option>
                </select>
            </div>
            <input type="hidden" name="job_role" id="jobRoleFinal">
            <div class="field"><label>Company Name</label><input required name="company" value="{{ old('company') }}" placeholder="Company Name" /><span class="field-err"></span></div>
            <div class="field"><label>Business Email</label><input required type="email" name="email" value="{{ old('email') }}" placeholder="yourname@company.com" /><span class="field-err"></span></div>
            <div class="field">
                <label>Mobile Phone</label>
                <div class="phone-wrapper"><span class="phone-prefix" id="phonePrefix1">+62</span><input required name="phone" placeholder="815-xxx-xxxx" class="phone-input" id="phoneInput1" oninput="updatePhonePrefix(this)" onfocus="focusPhonePrefix(true)" onblur="focusPhonePrefix(false)" /></div>
            </div>
            <div class="field">
                <label>Industry</label>
                <select name="industry" required>
                    <option value="">Select Industry</option>
                    <option>AGRICULTURE, FORESTRY</option>
                    <option>CHEMICALS</option>
                    <option>CONSTRUCTION, PROPERTY &amp; REAL ESTATE</option>
                    <option>DISTRIBUTION</option>
                    <option>EDUCATION</option>
                    <option>FINANCIAL SERVICES</option>
                    <option>FISHING &amp; MARINE</option>
                    <option>FOREIGN SERVICES</option>
                    <option>GOVERNMENT SERVICES</option>
                    <option>HEALTHCARE</option>
                    <option>HIGH TECHNOLOGY</option>
                    <option>HOSPITALITY / TOURISM</option>
                    <option>MANUFACTURING</option>
                    <option>MEDIA</option>
                    <option>MINING &amp; METALS</option>
                    <option>OIL &amp; GAS</option>
                    <option>PROFESSIONAL &amp; BUSINESS SERVICES</option>
                    <option>RETAIL, WHOLESALE</option>
                    <option>TELECOMMUNICATIONS</option>
                    <option>TRANSPORTATION</option>
                    <option>UTILITIES / PUBLIC SERVICES</option>
                </select>
            </div>
            <div class="field">
                <label>How did you hear about this event?</label>
                <select name="referral_source" required>
                    <option value="">Select one</option>
                    <option>LinkedIn</option>
                    <option>Instagram</option>
                    <option>Kompas Newspaper</option>
                    <option>Metrodata Website</option>
                    <option>Email</option>
                    <option>Metrodata Group Sales Representative / Colleague</option>
                </select>
            </div>
            <div class="field">
                <label>Number of Employee</label>
                <select name="employees" required>
                    <option value="">Select Number of Employee</option>
                    <option>1 – 50</option><option>51 – 200</option>
                    <option>201 – 500</option><option>501 – 1000</option>
                    <option>1000+</option>
                </select>
            </div>
            <div class="field full gdpr-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="gdpr" required checked>
                    <span>By submitting this form, I understand Metrodata will process my personal information in accordance with their <strong><a href="https://www.metrodata.co.id/privacy-policy" target="_blank">Privacy Notice</a></strong>. Additionally, I consent to my information being shared with <strong><a href="https://jovenindo.com/privacy-policy" target="_blank">Event Partners</a></strong> in accordance. I understand I may withdraw my consent or update my information at any time.</span>
                </label>
            </div>
            <div class="field full" style="margin-top:8px">
                <button type="submit" class="btn btn-submit">Submit Walk-in Registration <span class="btn-arrow">→</span></button>
            </div>
        </form>

        {{-- Confirmation info (revealed after the success popup is closed) --}}
        <div id="walkinConfirmation" style="display:none;text-align:center;margin-top:8px">
            <div style="display:inline-flex;align-items:center;gap:8px;padding:8px 20px;border-radius:999px;font-size:13px;font-weight:600;background:rgba(251,191,36,.12);color:#fbbf24;border:1px solid rgba(251,191,36,.25);margin-bottom:18px">
                <svg style="width:16px;height:16px" fill="none" stroke="#fbbf24" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z"/></svg>
                Pending Approval
            </div>
            <h2 style="color:#fff;font-size:clamp(22px,3vw,28px);font-weight:800;margin-bottom:12px">Registration Received!</h2>
            <p style="color:var(--muted);font-size:14.5px;line-height:1.7;max-width:460px;margin:0 auto 24px">You have filled in the walk-in registration form. Your registration is <strong style="color:#fbbf24">Pending</strong> approval — our team will confirm it and issue your badge.</p>
            <div style="background:rgba(255,255,255,.06);border:1px solid var(--line);border-radius:12px;padding:18px 20px;max-width:420px;margin:0 auto 24px;text-align:left;font-size:14px">
                <div style="display:flex;justify-content:space-between;gap:12px;padding:4px 0"><span style="color:var(--muted)">Name</span><strong style="color:#fff" id="confName"></strong></div>
                <div style="display:flex;justify-content:space-between;gap:12px;padding:4px 0"><span style="color:var(--muted)">Company</span><span style="color:#fff" id="confCompany"></span></div>
                <div style="display:flex;justify-content:space-between;gap:12px;padding:4px 0"><span style="color:var(--muted)">Job Function</span><span style="color:#fff" id="confJobFunction"></span></div>
                <div style="display:flex;justify-content:space-between;gap:12px;padding:4px 0"><span style="color:var(--muted)">Job Title</span><span style="color:#fff" id="confJobTitle"></span></div>
                <div style="display:flex;justify-content:space-between;gap:12px;padding:4px 0"><span style="color:var(--muted)">Email</span><span style="color:#fff" id="confEmail"></span></div>
                <div style="display:flex;justify-content:space-between;gap:12px;padding:4px 0"><span style="color:var(--muted)">Phone</span><span style="color:#fff" id="confPhone"></span></div>
            </div>
            <a href="{{ route('home1') }}" class="btn">Back to Event Website</a>
        </div>
    </div>

    <footer>
        <p><strong>Metrodata Solution Day 2026</strong> — Winning with AI · Jakarta, 20 August 2026 · Shangri-La Hotel</p>
    </footer>
</div>

{{-- Success submit popup --}}
<div id="walkinSuccessModal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(5,13,42,.78);backdrop-filter:blur(10px);padding:16px;">
  <div style="background:rgba(255,255,255,.06);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,.1);border-radius:20px;box-shadow:0 25px 60px rgba(0,0,0,.5),inset 0 1px 0 rgba(255,255,255,.08);width:100%;max-width:420px;overflow:hidden;animation:fadeInUp .35s ease-out;text-align:center;padding:36px 32px 28px;">
    <div style="width:72px;height:72px;background:rgba(16,185,129,.15);border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 22px;border:1px solid rgba(16,185,129,.2);">
      <svg style="width:34px;height:34px;color:#10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
    </div>
    <h2 style="font-size:19px;font-weight:700;color:#e2e8f0;margin-bottom:6px;letter-spacing:-.01em;">Registration Successful</h2>
    <p style="font-size:14px;color:#94a3b8;line-height:1.6;margin-bottom:24px;">Thank you, <strong style="color:#fff" id="popName"></strong>! Your walk-in registration form has been submitted successfully.</p>
    <button onclick="closeWalkinSuccess()" style="width:100%;padding:12px 0;background:linear-gradient(135deg,#ff3d6e,#e91e63);color:#fff;font-weight:700;font-size:14px;letter-spacing:.03em;border:none;border-radius:999px;cursor:pointer;box-shadow:0 8px 24px rgba(233,30,99,.35);transition:transform .25s,box-shadow .25s;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 12px 30px rgba(233,30,99,.5)'" onmouseout="this.style.transform='';this.style.boxShadow='0 8px 24px rgba(233,30,99,.35)'">Close</button>
  </div>
</div>

<script>
// Job Function / Job Title / Job Role logic (same as the main registration form):
// - "Job Role" dropdown only appears when Job Title = "Information Technology"
// - DB job_role = "{Job Title} - {Job Role}"
(function () {
    var jobTitleSelect = document.getElementById('jobRoleSelector');
    var jobRoleField = document.getElementById('jobRoleSpecificField');
    var jobRoleSelect = document.getElementById('jobRoleSpecific');
    var jobRoleFinal = document.getElementById('jobRoleFinal');
    var regForm = document.getElementById('walkinPublicForm');

    function updateJobRole() {
        if (!jobTitleSelect || !jobRoleFinal) return;
        var title = jobTitleSelect.value || '';
        var isIT = title === 'Information Technology';
        if (jobRoleField) jobRoleField.style.display = isIT ? '' : 'none';
        if (jobRoleSelect) {
            jobRoleSelect.required = isIT;
            if (!isIT) jobRoleSelect.value = '';
        }
        var specific = jobRoleSelect ? jobRoleSelect.value : '';
        jobRoleFinal.value = specific ? (title + ' - ' + specific) : title;
    }
    if (jobTitleSelect) jobTitleSelect.addEventListener('change', updateJobRole);
    if (jobRoleSelect) jobRoleSelect.addEventListener('change', updateJobRole);
    if (regForm) regForm.addEventListener('reset', updateJobRole);
    updateJobRole();
})();

// Mobile phone +62 prefix — normalize as the user types.
function updatePhonePrefix(el) {
    var v = el.value.replace(/[^0-9]/g, '').replace(/^0/, '');
    el.value = v;
    var p = document.getElementById('phonePrefix1');
    if (!p) return;
    if (v) { p.style.background = '#fff'; p.style.color = '#374151'; }
    else { p.style.background = 'rgba(255,255,255,.08)'; p.style.color = 'rgba(255,255,255,.45)'; }
}
function focusPhonePrefix(focused) {
    var p = document.getElementById('phonePrefix1');
    if (!p) return;
    if (focused) { p.style.borderColor = 'var(--pink,#e91e63)'; p.style.boxShadow = '0 0 0 3px rgba(233,30,99,.15)'; p.style.background = 'rgba(255,255,255,.12)'; }
    else { p.style.borderColor = ''; p.style.boxShadow = ''; var input = document.getElementById('phoneInput1'); if (input) updatePhonePrefix(input); }
}

// ── Walk-in form submit (AJAX) → success popup → reveal confirmation on close ──
async function submitWalkinForm(event) {
    if (event) event.preventDefault();
    var form = document.getElementById('walkinPublicForm');
    var errorsBox = document.getElementById('walkinErrors');
    var errorsMsg = document.getElementById('walkinErrorsMsg');
    errorsBox.style.display = 'none';
    errorsMsg.innerHTML = '';
    var submitBtn = form.querySelector('button[type="submit"]');
    var original = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span style="display:inline-block;width:16px;height:16px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite;vertical-align:-3px;margin-right:8px"></span>Submitting...';
    var tokenInput = form.querySelector('input[name="_token"]');
    var token = tokenInput ? tokenInput.value : '';
    try {
        var body = new URLSearchParams(new FormData(form));
        var res = await fetch('{{ route("walkin.public.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token
            },
            body: body.toString()
        });
        var data = await res.json().catch(function () { return {}; });
        if (!res.ok || data.success === false) {
            var msgs = [];
            if (data && data.errors) {
                Object.keys(data.errors).forEach(function (k) { msgs = msgs.concat(data.errors[k]); });
            }
            if (msgs.length) {
                errorsMsg.innerHTML = msgs.map(function (m) { return m; }).join('<br>');
                errorsBox.style.display = 'flex';
            } else {
                alert(data && data.message ? data.message : 'Registration failed. Please try again.');
            }
            submitBtn.disabled = false;
            submitBtn.innerHTML = original;
            return;
        }
        var r = data.registrant || {};
        document.getElementById('popName').textContent = r.name || '';
        if (r.name) {
            document.getElementById('confName').textContent = r.name;
            document.getElementById('confCompany').textContent = r.company || '-';
            document.getElementById('confJobFunction').textContent = r.job_title || '-';
            document.getElementById('confJobTitle').textContent = r.job_role || '-';
            document.getElementById('confEmail').textContent = r.email || '';
            document.getElementById('confPhone').textContent = r.phone || '-';
        }
        document.getElementById('walkinSuccessModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    } catch (e) {
        alert('Gagal terhubung ke server. Silakan coba lagi.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = original;
    }
}

function closeWalkinSuccess() {
    document.getElementById('walkinSuccessModal').style.display = 'none';
    document.getElementById('walkinPublicForm').style.display = 'none';
    document.getElementById('walkinConfirmation').style.display = 'block';
    document.body.style.overflow = '';
    window.scrollTo(0, 0);
}
</script>
</body>
</html>
