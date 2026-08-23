/* =========================================================
   StudyFlow — app.js
   Semua komunikasi ke backend Laravel (StudyFlow API) ada di sini.
   Ganti API_BASE kalau backend kamu jalan di alamat lain.
   ========================================================= */

const API_BASE = 'http://127.0.0.1:8000/api';

// Menyimpan data mata pelajaran di memori supaya bisa dipakai ulang
// (misalnya untuk mengisi dropdown di form jadwal) tanpa fetch berulang.
let subjectsCache = [];

/* ---------------------------------------------------------
   TOAST — notifikasi kecil pengganti alert() bawaan browser
   --------------------------------------------------------- */
function showToast(message, type = 'default') {
  const stack = document.getElementById('toast-stack');
  const el = document.createElement('div');
  el.className = `toast toast-${type}`;
  el.textContent = message;
  stack.appendChild(el);
  setTimeout(() => el.remove(), 3800);
}

/* ---------------------------------------------------------
   API WRAPPER
   Semua panggilan ke backend Laravel lewat fungsi ini,
   supaya header Authorization & penanganan error konsisten.
   --------------------------------------------------------- */
async function api(path, { method = 'GET', body = null, auth = true } = {}) {
  const headers = {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  };

  if (auth) {
    const token = localStorage.getItem('studyflow_token');
    if (token) headers['Authorization'] = `Bearer ${token}`;
  }

  const res = await fetch(`${API_BASE}${path}`, {
    method,
    headers,
    body: body ? JSON.stringify(body) : null,
  });

  let data = null;
  try { data = await res.json(); } catch (_) { /* response kosong, biarkan null */ }

  if (!res.ok) {
    // Token tidak valid / kadaluarsa -> paksa kembali ke halaman login
    if (res.status === 401) {
      handleLogout(false);
      showToast('Sesi kamu berakhir, silakan masuk lagi.', 'error');
    }
    const message = data?.message || 'Terjadi kesalahan, coba lagi.';
    throw new Error(message);
  }

  return data;
}

/* =========================================================
   AUTENTIKASI
   ========================================================= */
const authScreen = document.getElementById('auth-screen');
const appScreen = document.getElementById('app-screen');

function switchAuthTab(tab) {
  document.querySelectorAll('.auth-tab').forEach(btn => {
    btn.classList.toggle('is-active', btn.dataset.tab === tab);
  });
  document.getElementById('login-form').classList.toggle('is-hidden', tab !== 'login');
  document.getElementById('register-form').classList.toggle('is-hidden', tab !== 'register');
}
document.querySelectorAll('.auth-tab').forEach(btn => {
  btn.addEventListener('click', () => switchAuthTab(btn.dataset.tab));
});

// --- Login ---
document.getElementById('login-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  const form = e.target;
  const payload = {
    email: form.email.value,
    password: form.password.value,
  };
  try {
    const res = await api('/login', { method: 'POST', body: payload, auth: false });
    localStorage.setItem('studyflow_token', res.data.token);
    localStorage.setItem('studyflow_user', JSON.stringify(res.data.user));
    showToast(`Selamat datang kembali, ${res.data.user.name}.`, 'success');
    enterApp(res.data.user);
  } catch (err) {
    showToast(err.message, 'error');
  }
});

// --- Register ---
document.getElementById('register-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  const form = e.target;
  const payload = {
    name: form.name.value,
    email: form.email.value,
    password: form.password.value,
    password_confirmation: form.password_confirmation.value,
  };
  try {
    const res = await api('/register', { method: 'POST', body: payload, auth: false });
    localStorage.setItem('studyflow_token', res.data.token);
    localStorage.setItem('studyflow_user', JSON.stringify(res.data.user));
    showToast('Buku rencana baru berhasil dibuat.', 'success');
    enterApp(res.data.user);
  } catch (err) {
    showToast(err.message, 'error');
  }
});

// --- Logout ---
document.getElementById('logout-btn').addEventListener('click', () => handleLogout(true));

async function handleLogout(callApi) {
  if (callApi) {
    try { await api('/logout', { method: 'POST' }); } catch (_) { /* abaikan, tetap logout di sisi client */ }
  }
  localStorage.removeItem('studyflow_token');
  localStorage.removeItem('studyflow_user');
  appScreen.classList.add('is-hidden');
  authScreen.classList.remove('is-hidden');
}

function enterApp(user) {
  authScreen.classList.add('is-hidden');
  appScreen.classList.remove('is-hidden');
  document.getElementById('user-name').textContent = user.name;
  loadSubjects();
  loadSchedules();
}

/* =========================================================
   MATA PELAJARAN (Subjects)
   ========================================================= */
const subjectList = document.getElementById('subject-list');
const subjectEmpty = document.getElementById('subject-empty');
const subjectForm = document.getElementById('subject-form');
const subjectToggleBtn = document.getElementById('add-subject-toggle');

subjectToggleBtn.addEventListener('click', () => openSubjectForm());
subjectForm.querySelector('[data-cancel-subject]').addEventListener('click', () => closeSubjectForm());

function openSubjectForm(subject = null) {
  subjectForm.reset();
  subjectForm.id.value = subject ? subject.id : '';
  if (subject) {
    subjectForm.name.value = subject.name;
    subjectForm.description.value = subject.description || '';
  }
  subjectForm.classList.remove('is-hidden');
  subjectForm.name.focus();
}
function closeSubjectForm() {
  subjectForm.classList.add('is-hidden');
  subjectForm.reset();
}

subjectForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  const id = subjectForm.id.value;
  const payload = {
    name: subjectForm.name.value,
    description: subjectForm.description.value,
  };
  try {
    if (id) {
      await api(`/subjects/${id}`, { method: 'PUT', body: payload });
      showToast('Mata pelajaran diperbarui.', 'success');
    } else {
      await api('/subjects', { method: 'POST', body: payload });
      showToast('Mata pelajaran ditambahkan.', 'success');
    }
    closeSubjectForm();
    await loadSubjects();
    await loadSchedules(); // refresh label subject di kartu jadwal
  } catch (err) {
    showToast(err.message, 'error');
  }
});

async function loadSubjects() {
  try {
    const res = await api('/subjects');
    subjectsCache = res.data;
    renderSubjects(subjectsCache);
    fillSubjectDropdown(subjectsCache);
  } catch (err) {
    showToast(err.message, 'error');
  }
}

function renderSubjects(subjects) {
  subjectList.innerHTML = '';
  subjectEmpty.classList.toggle('is-hidden', subjects.length > 0);

  subjects.forEach(subject => {
    const li = document.createElement('li');
    li.className = 'subject-card';
    li.innerHTML = `
      <div>
        <h3>${escapeHtml(subject.name)}</h3>
        <p>${escapeHtml(subject.description || '')}</p>
        <span class="count">${subject.schedules_count ?? 0} jadwal</span>
      </div>
      <div class="subject-actions">
        <button class="icon-btn" data-edit-subject="${subject.id}" title="Ubah">&#9998;</button>
        <button class="icon-btn danger" data-delete-subject="${subject.id}" title="Hapus">&times;</button>
      </div>
    `;
    subjectList.appendChild(li);
  });

  subjectList.querySelectorAll('[data-edit-subject]').forEach(btn => {
    btn.addEventListener('click', () => {
      const subject = subjectsCache.find(s => s.id === Number(btn.dataset.editSubject));
      openSubjectForm(subject);
    });
  });
  subjectList.querySelectorAll('[data-delete-subject]').forEach(btn => {
    btn.addEventListener('click', () => deleteSubject(btn.dataset.deleteSubject));
  });
}

let isDeletingSubject = false; // penanda supaya tombol hapus tidak bisa dipencet dobel

async function deleteSubject(id) {
  if (isDeletingSubject) return;
  if (!confirm('Hapus mata pelajaran ini? Jadwal yang terkait juga akan ikut terhapus.')) return;

  isDeletingSubject = true;
  try {
    await api(`/subjects/${id}`, { method: 'DELETE' });
    showToast('Mata pelajaran dihapus.', 'success');
  } catch (err) {
    if (!err.message.toLowerCase().includes('no query results')) {
      showToast(err.message, 'error');
    }
  } finally {
    isDeletingSubject = false;
    await loadSubjects();
    await loadSchedules();
  }
}

function fillSubjectDropdown(subjects) {
  const select = scheduleForm.subject_id;
  const current = select.value;
  select.innerHTML = subjects.map(s => `<option value="${s.id}">${escapeHtml(s.name)}</option>`).join('');
  if (current) select.value = current;
}

/* =========================================================
   JADWAL BELAJAR (Schedules)
   ========================================================= */
const scheduleList = document.getElementById('schedule-list');
const scheduleEmpty = document.getElementById('schedule-empty');
const scheduleForm = document.getElementById('schedule-form');
const scheduleToggleBtn = document.getElementById('add-schedule-toggle');

let schedulesCache = [];

scheduleToggleBtn.addEventListener('click', () => {
  if (subjectsCache.length === 0) {
    showToast('Tambahkan mata pelajaran dulu sebelum menulis jadwal.', 'error');
    return;
  }
  openScheduleForm();
});
scheduleForm.querySelector('[data-cancel-schedule]').addEventListener('click', () => closeScheduleForm());

function openScheduleForm(schedule = null) {
  scheduleForm.reset();
  fillSubjectDropdown(subjectsCache);
  scheduleForm.id.value = schedule ? schedule.id : '';
  if (schedule) {
    scheduleForm.title.value = schedule.title;
    scheduleForm.subject_id.value = schedule.subject.id;
    scheduleForm.study_date.value = schedule.study_date;
    scheduleForm.start_time.value = schedule.start_time;
    scheduleForm.end_time.value = schedule.end_time;
    scheduleForm.status.value = schedule.status;
    scheduleForm.notes.value = schedule.notes || '';
  }
  scheduleForm.classList.remove('is-hidden');
  scheduleForm.title.focus();
}
function closeScheduleForm() {
  scheduleForm.classList.add('is-hidden');
  scheduleForm.reset();
}

scheduleForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  const id = scheduleForm.id.value;
  const payload = {
    subject_id: Number(scheduleForm.subject_id.value),
    title: scheduleForm.title.value,
    study_date: scheduleForm.study_date.value,
    start_time: scheduleForm.start_time.value,
    end_time: scheduleForm.end_time.value,
    status: scheduleForm.status.value,
    notes: scheduleForm.notes.value,
  };
  try {
    if (id) {
      await api(`/schedules/${id}`, { method: 'PUT', body: payload });
      showToast('Jadwal diperbarui.', 'success');
    } else {
      await api('/schedules', { method: 'POST', body: payload });
      showToast('Jadwal baru ditulis di buku.', 'success');
    }
    closeScheduleForm();
    await loadSchedules();
    await loadSubjects(); // refresh jumlah jadwal per mata pelajaran
  } catch (err) {
    showToast(err.message, 'error');
  }
});

async function loadSchedules() {
  try {
    const res = await api('/schedules');
    schedulesCache = res.data;
    renderSchedules(schedulesCache);
  } catch (err) {
    showToast(err.message, 'error');
  }
}

const STATUS_LABEL = {
  planned: 'Direncanakan',
  in_progress: 'Berjalan',
  done: 'Selesai',
};

function renderSchedules(schedules) {
  scheduleList.innerHTML = '';
  scheduleEmpty.classList.toggle('is-hidden', schedules.length > 0);

  // Kelompokkan berdasarkan tanggal supaya terasa seperti halaman per-hari di buku catatan
  let lastDate = null;
  schedules.forEach(schedule => {
    if (schedule.study_date !== lastDate) {
      const label = document.createElement('p');
      label.className = 'schedule-group-label';
      label.textContent = formatDateLabel(schedule.study_date);
      scheduleList.appendChild(label);
      lastDate = schedule.study_date;
    }

    const card = document.createElement('div');
    card.className = 'schedule-card';
    card.innerHTML = `
      <div class="schedule-time">${schedule.start_time}<br>&darr;<br>${schedule.end_time}</div>
      <div class="schedule-main">
        <span class="subject-tag">${escapeHtml(schedule.subject?.name || '—')}</span>
        <h3>${escapeHtml(schedule.title)}</h3>
        ${schedule.notes ? `<p class="notes">${escapeHtml(schedule.notes)}</p>` : ''}
      </div>
      <div class="schedule-side">
        <span class="status-tag status-${schedule.status}">${STATUS_LABEL[schedule.status] || schedule.status}</span>
        <div class="schedule-actions">
          <button class="icon-btn" data-edit-schedule="${schedule.id}" title="Ubah">&#9998;</button>
          <button class="icon-btn danger" data-delete-schedule="${schedule.id}" title="Hapus">&times;</button>
        </div>
      </div>
    `;
    scheduleList.appendChild(card);
  });

  scheduleList.querySelectorAll('[data-edit-schedule]').forEach(btn => {
    btn.addEventListener('click', () => {
      const schedule = schedulesCache.find(s => s.id === Number(btn.dataset.editSchedule));
      openScheduleForm(schedule);
    });
  });
  scheduleList.querySelectorAll('[data-delete-schedule]').forEach(btn => {
    btn.addEventListener('click', () => deleteSchedule(btn.dataset.deleteSchedule));
  });
}

let isDeletingSchedule = false; // penanda supaya tombol hapus tidak bisa dipencet dobel

async function deleteSchedule(id) {
  if (isDeletingSchedule) return; // abaikan klik berikutnya selama masih proses
  if (!confirm('Hapus jadwal ini dari buku rencana?')) return;

  isDeletingSchedule = true;
  try {
    await api(`/schedules/${id}`, { method: 'DELETE' });
    showToast('Jadwal dihapus.', 'success');
  } catch (err) {
    // Kalau datanya memang sudah tidak ada (404), anggap saja "sudah terhapus",
    // tidak perlu tampilkan pesan error mentah dari backend ke pengguna.
    if (!err.message.toLowerCase().includes('no query results')) {
      showToast(err.message, 'error');
    }
  } finally {
    isDeletingSchedule = false;
    await loadSchedules();
    await loadSubjects();
  }
}

/* ---------------------------------------------------------
   UTILITAS
   --------------------------------------------------------- */
function formatDateLabel(dateStr) {
  const date = new Date(`${dateStr}T00:00:00`);
  return date.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
}

function escapeHtml(str) {
  const div = document.createElement('div');
  div.textContent = str ?? '';
  return div.innerHTML;
}

/* =========================================================
   INISIALISASI — cek apakah sudah pernah login sebelumnya
   ========================================================= */
(function init() {
  const token = localStorage.getItem('studyflow_token');
  const userRaw = localStorage.getItem('studyflow_user');
  if (token && userRaw) {
    try {
      enterApp(JSON.parse(userRaw));
    } catch (_) {
      handleLogout(false);
    }
  }
})();
